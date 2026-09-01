-- Update the ENUM definition for the status column in the orders table
ALTER TABLE orders MODIFY status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending';

-- Set all existing orders with empty status to 'Shipped'
UPDATE orders
SET status = 'Shipped'
WHERE status IS NULL OR status = '';

-- Update orders older than 24 hours from 'Shipped' to 'Delivered'
UPDATE orders
SET status = 'Delivered'
WHERE status = 'Shipped'
  AND TIMESTAMPDIFF(HOUR, order_date, NOW()) >= 24;

-- Sample command to cancel a specific order (replace <your_order_id> with the actual order ID)
-- UPDATE orders
-- SET status = 'Cancelled'
-- WHERE order_id = <your_order_id>;

-- Verify the current status of all orders
SELECT order_id, user_id, order_date, total_amount, status
FROM orders
ORDER BY order_date DESC;

-- Check the ENUM definition for the status column
SHOW COLUMNS FROM orders LIKE 'status'; 