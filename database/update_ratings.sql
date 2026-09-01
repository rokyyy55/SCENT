-- Add average_rating column to products table if it doesn't exist
ALTER TABLE products
ADD COLUMN IF NOT EXISTS average_rating DECIMAL(3,2) DEFAULT 0.00;

-- Update average_rating for existing products
UPDATE products p
SET average_rating = (
    SELECT COALESCE(AVG(rating), 0)
    FROM ratings r
    WHERE r.product_id = p.product_id
); 
DELIMITER //

CREATE TRIGGER update_product_rating
AFTER INSERT ON ratings
FOR EACH ROW
BEGIN
    UPDATE products 
    SET average_rating = (
        SELECT AVG(rating) 
        FROM ratings 
        WHERE product_id = NEW.product_id
    )
    WHERE product_id = NEW.product_id;
END//

CREATE TRIGGER update_product_rating_on_update
AFTER UPDATE ON ratings
FOR EACH ROW
BEGIN
    UPDATE products 
    SET average_rating = (
        SELECT AVG(rating) 
        FROM ratings 
        WHERE product_id = NEW.product_id
    )
    WHERE product_id = NEW.product_id;
END//

CREATE TRIGGER update_product_rating_on_delete
AFTER DELETE ON ratings
FOR EACH ROW
BEGIN
    UPDATE products 
    SET average_rating = (
        SELECT COALESCE(AVG(rating), 0) 
        FROM ratings 
        WHERE product_id = OLD.product_id
    )
    WHERE product_id = OLD.product_id;
END//

DELIMITER ;