-- Trigger pour mettre à jour le stock après la validation d'une commande
DELIMITER //
CREATE TRIGGER update_stock_after_order
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    UPDATE products
    SET stock_quantity = stock_quantity - NEW.quantity
    WHERE product_id = NEW.product_id;
END //
DELIMITER ;

-- Trigger pour empêcher l'insertion d'une commande si la quantité dépasse le stock
DELIMITER //
CREATE TRIGGER check_stock_before_order
BEFORE INSERT ON order_items
FOR EACH ROW
BEGIN
    DECLARE available_stock INT;
    
    -- Récupérer le stock disponible
    SELECT stock_quantity INTO available_stock
    FROM products
    WHERE product_id = NEW.product_id;
    
    -- Vérifier si le stock est suffisant
    IF NEW.quantity > available_stock THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stock insuffisant pour ce produit';
    END IF;
END //
DELIMITER ;
DELIMITER $$

CREATE TRIGGER generate_discount_code_after_delivery
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    DECLARE total_items INT DEFAULT 0;
    DECLARE milestone INT DEFAULT 0;
    DECLARE last_milestone INT DEFAULT 0;
    DECLARE code_value VARCHAR(10);

    -- Only act if the status changed to Delivered
    IF NEW.status = 'Delivered' AND OLD.status <> 'Delivered' THEN

        -- Count all delivered items for this user
        SELECT SUM(oi.quantity)
        INTO total_items
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.user_id = NEW.user_id AND o.status = 'Delivered';

        -- Calculate the milestone (how many 5s)
        SET milestone = FLOOR(total_items / 5);

        -- Get the last milestone for this user
        SELECT IFNULL(MAX(milestone), 0) INTO last_milestone
        FROM user_code_milestones
        WHERE user_id = NEW.user_id;

        -- If the user reached a new milestone, generate a code
        IF milestone > last_milestone THEN
            -- Generate a random 6-digit code
            SET code_value = LPAD(FLOOR(RAND() * 1000000), 6, '0');

            -- Insert the code
            INSERT INTO discount_codes (user_id, code, discount_percentage, is_used, created_at)
            VALUES (NEW.user_id, code_value, 10, 0, NOW());

            -- Record the new milestone
            INSERT INTO user_code_milestones (user_id, milestone)
            VALUES (NEW.user_id, milestone);
        END IF;
    END IF;
END$$
-- 1. For each user, calculate how many 5-item milestones they have reached
INSERT IGNORE INTO user_code_milestones (user_id, milestone)
SELECT user_id, milestone
FROM (
    SELECT
        o.user_id,
        n AS milestone
    FROM (
        SELECT o.user_id, FLOOR(SUM(oi.quantity) / 5) AS max_milestone
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.status = 'Delivered'
        GROUP BY o.user_id
    ) AS user_milestones
    JOIN (
        SELECT 1 AS n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
    ) AS numbers
    ON numbers.n <= user_milestones.max_milestone
    WHERE user_milestones.max_milestone > 0
) AS milestones;

-- 2. For each user and milestone, generate a code if not already present
INSERT INTO discount_codes (user_id, code, discount_percentage, is_used, created_at)
SELECT
    ucm.user_id,
    LPAD(FLOOR(RAND() * 1000000), 6, '0'),
    10,
    0,
    NOW()
FROM user_code_milestones ucm
LEFT JOIN discount_codes dc
    ON dc.user_id = ucm.user_id
    AND dc.discount_percentage = 10
    AND dc.code IN (
        SELECT code FROM discount_codes WHERE user_id = ucm.user_id
    )
WHERE NOT EXISTS (
    SELECT 1 FROM discount_codes d2
    WHERE d2.user_id = ucm.user_id
      AND d2.discount_percentage = 10
);


DELIMITER ;
-- Trigger pour restaurer le stock après l'annulation d'une commande
DELIMITER //
CREATE TRIGGER restore_stock_after_cancel
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.status = 'Cancelled' AND OLD.status != 'Cancelled' THEN
        -- Restaurer le stock pour tous les produits de la commande
        UPDATE products p
        JOIN order_items oi ON p.product_id = oi.product_id
        SET p.stock_quantity = p.stock_quantity + oi.quantity
        WHERE oi.order_id = NEW.order_id;
    END IF;
END //
DELIMITER ;

-- Trigger pour garder trace des commandes annulées
DELIMITER //
CREATE TRIGGER track_cancelled_orders
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.status = 'Cancelled' AND OLD.status != 'Cancelled' THEN
        -- Insérer dans l'historique des commandes annulées
        INSERT INTO cancelled_orders_history (
            order_id,
            user_id,
            total_amount,
            reason
        ) VALUES (
            NEW.order_id,
            NEW.user_id,
            NEW.total_amount,
            'Commande annulée par l\'utilisateur ou l\'administrateur'
        );
    END IF;
END //
DELIMITER ;
DELIMITER //
CREATE PROCEDURE sell_product(
    IN p_product_id INT,
    IN p_quantity INT,
    IN p_order_id VARCHAR(50)
)
BEGIN
    DECLARE current_stock INT;
    
    -- Get current stock quantity
    SELECT stock_quantity INTO current_stock 
    FROM products 
    WHERE product_id = p_product_id;
    
    -- Check if we have enough stock
    IF current_stock >= p_quantity THEN
        -- Update product stock
        UPDATE products 
        SET stock_quantity = stock_quantity - p_quantity
        WHERE product_id = p_product_id;
        
        -- Record transaction
        INSERT INTO inventory_transactions 
            (product_id, quantity_change, transaction_type, reference_id, notes)
        VALUES 
            (p_product_id, -p_quantity, 'sale', p_order_id, CONCAT('Sale of ', p_quantity, ' units'));
            
        SELECT 'Success' AS result, 
               CONCAT('Sold ', p_quantity, ' units of product #', p_product_id) AS message;
    ELSE
        SELECT 'Error' AS result, 
               CONCAT('Insufficient stock. Available: ', current_stock, ', Requested: ', p_quantity) AS message;
    END IF;
END //
DELIMITER ;

-- Procedure to restock a product (increase quantity)




-- 6. SIXTH: Create a trigger to update product last_updated timestamp on inventory changes
DELIMITER //
CREATE TRIGGER update_product_timestamp
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    IF OLD.stock_quantity != NEW.stock_quantity THEN
        UPDATE products 
        SET updated_at = CURRENT_TIMESTAMP
        WHERE product_id = NEW.product_id;
    END IF;
END //
DELIMITER ;
DELIMITER //

-- Trigger when products are added
CREATE TRIGGER after_product_insert
AFTER INSERT ON product
FOR EACH ROW
BEGIN
    UPDATE brand 
    SET productcount = productcount + 1 
    WHERE brand_id = NEW.brand_id;
END //

-- Trigger when products are deleted
CREATE TRIGGER after_product_delete
AFTER DELETE ON product
FOR EACH ROW
BEGIN
    UPDATE brand 
    SET productcount = productcount - 1 
    WHERE brand_id = OLD.brand_id;
END //

-- Trigger when product brand is updated
CREATE TRIGGER after_product_update
AFTER UPDATE ON product
FOR EACH ROW
BEGIN
    IF OLD.brand_id <> NEW.brand_id THEN
        UPDATE brand 
        SET productcount = productcount - 1 
        WHERE brand_id = OLD.brand_id;
        
        UPDATE brand 
        SET productcount = productcount + 1 
        WHERE brand_id = NEW.brand_id;
    END IF;
END //

-- Trigger to monitor stock levels and add to low stock when reaching 10 units
DELIMITER //
CREATE TRIGGER monitor_low_stock
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    IF NEW.stock_quantity <= 10 AND OLD.stock_quantity > 10 THEN
        -- Insert into low stock table if not already present
        INSERT IGNORE INTO low_stock_products (product_id, alert_threshold, created_at)
        VALUES (NEW.product_id, 10, NOW());
    END IF;
END //
DELIMITER ;

DELIMITER ;