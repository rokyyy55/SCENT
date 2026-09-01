-- Procédures stockées et triggers pour Scent

USE scent_db;

-- ========== PROCÉDURES STOCKÉES ==========
DELIMITER $$

CREATE PROCEDURE generate_missing_discount_codes()
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE uid INT;
    DECLARE total_items INT;
    DECLARE milestones INT;
    DECLARE codes_given INT;
    DECLARE i INT;
    DECLARE code_value VARCHAR(10);

    -- Cursor for users with delivered items
    DECLARE cur CURSOR FOR
        SELECT o.user_id, SUM(oi.quantity) AS total_items
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.status = 'Delivered'
        GROUP BY o.user_id
        HAVING total_items >= 5;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO uid, total_items;
        IF done THEN
            LEAVE read_loop;
        END IF;

        SET milestones = FLOOR(total_items / 5);

        -- Count how many codes this user already has
        SELECT COUNT(*) INTO codes_given
        FROM discount_codes
        WHERE user_id = uid AND discount_percentage = 10;

        SET i = codes_given + 1;
        WHILE i <= milestones DO
            SET code_value = LPAD(FLOOR(RAND() * 1000000), 6, '0');
            INSERT INTO discount_codes (user_id, code, discount_percentage, is_used, created_at)
            VALUES (uid, code_value, 10, 0, NOW());
            SET i = i + 1;
        END WHILE;
    END LOOP;
    CLOSE cur;
END$$

DELIMITER ;
-- 1. Procédure pour afficher les détails d'une commande pour un client et le total à payer
DELIMITER //
CREATE PROCEDURE GetOrderDetails(IN p_order_id INT, IN p_user_id INT)
BEGIN
    DECLARE total DECIMAL(10, 2);
    
    -- Vérifier que la commande appartient bien à l'utilisateur
    IF EXISTS (SELECT 1 FROM orders WHERE order_id = p_order_id AND user_id = p_user_id) THEN
        -- Afficher les informations de la commande
        SELECT 
            o.order_id,
            o.order_date,
            o.status,
            o.shipping_address,
            o.shipping_city,
            o.shipping_postal_code,
            o.shipping_country,
            o.payment_method
        FROM orders o
        WHERE o.order_id = p_order_id;
        
        -- Afficher les produits de la commande
        SELECT 
            p.name,
            p.image,
            oi.quantity,
            oi.price,
            (oi.quantity * oi.price) AS subtotal
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = p_order_id;
        
        -- Calculer et afficher le total à payer
        SELECT total_amount AS total FROM orders WHERE order_id = p_order_id;
    ELSE
        SELECT 'Commande non trouvée ou accès non autorisé' AS message;
    END IF;
END //
DELIMITER ;

-- 2. Procédure pour finaliser une commande et vider le panier
DELIMITER //
CREATE PROCEDURE FinalizeOrder(
    IN p_user_id INT, 
    IN p_shipping_address TEXT,
    IN p_shipping_city VARCHAR(50),
    IN p_shipping_postal_code VARCHAR(20),
    IN p_shipping_country VARCHAR(50),
    IN p_payment_method VARCHAR(50),
    OUT p_order_id INT
)
BEGIN
    DECLARE v_cart_id INT;
    DECLARE v_total DECIMAL(10, 2) DEFAULT 0;
    
    -- Trouver le panier de l'utilisateur
    SELECT cart_id INTO v_cart_id FROM carts WHERE user_id = p_user_id LIMIT 1;
    
    IF v_cart_id IS NOT NULL THEN
        -- Calculer le total de la commande
        SELECT SUM(ci.quantity * p.price) INTO v_total
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.product_id
        WHERE ci.cart_id = v_cart_id;
        
        -- Insérer la nouvelle commande
        INSERT INTO orders (
            user_id, 
            total_amount, 
            shipping_address, 
            shipping_city, 
            shipping_postal_code, 
            shipping_country, 
            payment_method,
            status
        ) VALUES (
            p_user_id, 
            v_total, 
            p_shipping_address, 
            p_shipping_city, 
            p_shipping_postal_code, 
            p_shipping_country, 
            p_payment_method,
            'Processing'
        );
        
        -- Récupérer l'ID de la commande créée
        SET p_order_id = LAST_INSERT_ID();
        
        -- Transférer les éléments du panier vers la commande
        INSERT INTO order_items (order_id, product_id, quantity, price)
        SELECT p_order_id, ci.product_id, ci.quantity, p.price
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.product_id
        WHERE ci.cart_id = v_cart_id;
        
        -- Vider le panier
        DELETE FROM cart_items WHERE cart_id = v_cart_id;
        
        SELECT 'Commande finalisée avec succès' AS message, p_order_id AS order_id;
    ELSE
        SET p_order_id = NULL;
        SELECT 'Panier non trouvé' AS message;
    END IF;
END //
DELIMITER ;

-- 3. Procédure pour afficher l'historique des commandes d'un client
DELIMITER //
CREATE PROCEDURE GetOrderHistory(IN p_user_id INT)
BEGIN
    SELECT 
        o.order_id,
        o.order_date,
        o.total_amount,
        o.status,
        o.shipping_address,
        o.shipping_city,
        COUNT(oi.order_item_id) AS total_items
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.user_id = p_user_id
    GROUP BY o.order_id
    ORDER BY o.order_date DESC;
END //
DELIMITER ;
DELIMITER //
CREATE PROCEDURE restock_product(
    IN p_product_id INT,
    IN p_quantity INT,
    IN p_purchase_id VARCHAR(50),
    IN p_notes TEXT
)
BEGIN
    -- Update product stock
    UPDATE products 
    SET stock_quantity = stock_quantity + p_quantity
    WHERE product_id = p_product_id;
    
    -- Record transaction
    INSERT INTO inventory_transactions 
        (product_id, quantity_change, transaction_type, reference_id, notes)
    VALUES 
        (p_product_id, p_quantity, 'restock', p_purchase_id, p_notes);
        
    SELECT 'Success' AS result, 
           CONCAT('Added ', p_quantity, ' units to product #', p_product_id) AS message;
END //
DELIMITER ;