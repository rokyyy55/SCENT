-- Base de données pour Scent (e-commerce de parfums)

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS scent_db;
USE scent_db;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    address TEXT,
    city VARCHAR(50),
    postal_code VARCHAR(20),
    country VARCHAR(50),
    phone VARCHAR(20),
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
ALTER TABLE orders MODIFY status ENUM('Processing','Done','Terminé') DEFAULT 'Processing';
UPDATE orders
SET status = 'Done'
WHERE status = 'Processing'
  AND TIMESTAMPDIFF(HOUR, order_date, NOW()) >= 2;
-- Table des catégories de parfums
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT
);
-- Create a new junction table to manage the many-to-many relationship between brands and categories
CREATE TABLE IF NOT EXISTS brand_category (
    brand_id INT,
    category_id INT,
    PRIMARY KEY (brand_id, category_id),
    FOREIGN KEY (brand_id) REFERENCES brands(brand_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
);

-- Table des marques
CREATE TABLE IF NOT EXISTS brands (
    brand_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    image VARCHAR(255)
);

-- Table des produits (parfums)
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    image VARCHAR(255),
    category_id INT,
    brand_id INT,
    volume INT, -- en ml
    concentration ENUM('Eau de Cologne', 'Eau de Toilette', 'Eau de Parfum', 'Parfum'),
    gender ENUM('Homme', 'Femme', 'Unisexe'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    average_rating DECIMAL(3,2) DEFAULT 0.00,
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    FOREIGN KEY (brand_id) REFERENCES brands(brand_id)
);
-- 1. FIRST: Update product quantities with random values between 5 and 100
UPDATE products SET stock_quantity = FLOOR(5 + RAND() * 96);

-- 2. SECOND: Create inventory_transactions table to track all quantity changes
CREATE TABLE IF NOT EXISTS inventory_transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity_change INT NOT NULL, -- negative for sales, positive for restocking
    transaction_type ENUM('sale', 'restock', 'adjustment') NOT NULL,
    reference_id VARCHAR(50), -- could reference order_id, purchase_id, etc.
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- 3. THIRD: Create stored procedures for sales and restocking


-- Table des paniers
CREATE TABLE IF NOT EXISTS carts (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Table des éléments du panier
CREATE TABLE IF NOT EXISTS cart_items (
    cart_item_id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT,
    product_id INT,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (cart_id) REFERENCES carts(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Table des commandes
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    shipping_address TEXT,
    shipping_city VARCHAR(50),
    shipping_postal_code VARCHAR(20),
    shipping_country VARCHAR(50),
    payment_method VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Table des éléments de commande
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL, -- Prix au moment de l'achat
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Table historique des commandes annulées
CREATE TABLE IF NOT EXISTS cancelled_orders_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    user_id INT,
    cancellation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2),
    reason TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Table des notes et avis
CREATE TABLE IF NOT EXISTS reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    user_id INT,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Table des sessions
CREATE TABLE IF NOT EXISTS sessions (
    session_id VARCHAR(255) PRIMARY KEY,
    user_id INT,
    data TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Table for product ratings
CREATE TABLE IF NOT EXISTS ratings (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product_rating (user_id, product_id)
);

-- Create trigger to update average_rating when a new rating is added
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
DELIMITER ;

-- Create trigger to update average_rating when a rating is updated
DELIMITER //
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
DELIMITER ;

-- Create trigger to update average_rating when a rating is deleted
DELIMITER //
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

-- Table for tracking low stock products
CREATE TABLE IF NOT EXISTS low_stock_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    alert_threshold INT NOT NULL DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_product (product_id)
);