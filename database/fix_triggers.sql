-- Drop existing triggers
DROP TRIGGER IF EXISTS after_product_insert;
DROP TRIGGER IF EXISTS after_product_delete;
DROP TRIGGER IF EXISTS after_product_update;

-- Recreate triggers with correct column names
DELIMITER //

-- Trigger when products are added
CREATE TRIGGER after_product_insert
AFTER INSERT ON products
FOR EACH ROW
BEGIN
    UPDATE brands 
    SET productcount = productcount + 1 
    WHERE brand_id = NEW.brand_id;
END //

-- Trigger when products are deleted
CREATE TRIGGER after_product_delete
AFTER DELETE ON products
FOR EACH ROW
BEGIN
    UPDATE brands 
    SET productcount = productcount - 1 
    WHERE brand_id = OLD.brand_id;
END //

-- Trigger when product brand is updated
CREATE TRIGGER after_product_update
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    IF OLD.brand_id <> NEW.brand_id THEN
        UPDATE brands 
        SET productcount = productcount - 1 
        WHERE brand_id = OLD.brand_id;
        
        UPDATE brands 
        SET productcount = productcount + 1 
        WHERE brand_id = NEW.brand_id;
    END IF;
END //

DELIMITER ; 