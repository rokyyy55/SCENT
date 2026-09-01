<?php
/**
 * Direct Cart Fix - Save this as fix-cart.php in your root directory
 * This is a simpler version without session constants that might cause errors
 */

// Include your basic configuration and database connection
require_once 'config/config.php';  // Adjust path if needed
require_once 'libraries/Database.php';  // Adjust path if needed

// Start session in a compatible way
if (!isset($_SESSION)) {
    session_start();
}

// Output function
function output($message, $data = null) {
    echo "<p><strong>$message</strong></p>";
    if ($data !== null) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
    echo "<hr>";
}

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

// Connect to database
$database = new Database();
$conn = $database->connect();

// Create cart tables if they don't exist
$createCartTable = "CREATE TABLE IF NOT EXISTS `carts` (
    `cart_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$createCartItemsTable = "CREATE TABLE IF NOT EXISTS `cart_items` (
    `cart_item_id` INT AUTO_INCREMENT PRIMARY KEY,
    `cart_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`cart_id`) REFERENCES `carts`(`cart_id`) ON DELETE CASCADE
)";

try {
    $conn->exec($createCartTable);
    $conn->exec($createCartItemsTable);
    output("Cart tables created or verified");
} catch (PDOException $e) {
    output("Error creating tables: " . $e->getMessage());
}

// Check current session cart
output("Current Session Cart", isset($_SESSION['cart']) ? $_SESSION['cart'] : "Not set");

// Check if user is logged in
if ($is_logged_in) {
    output("User is logged in with ID: " . $user_id);
    
    // Check if user has a cart in the database
    $cartQuery = "SELECT * FROM carts WHERE user_id = :user_id";
    $stmt = $conn->prepare($cartQuery);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($cart) {
        output("User has a database cart", $cart);
        
        // Check cart items
        $itemsQuery = "SELECT ci.*, p.name, p.price 
                      FROM cart_items ci 
                      JOIN products p ON ci.product_id = p.product_id 
                      WHERE ci.cart_id = :cart_id";
        $stmt = $conn->prepare($itemsQuery);
        $stmt->bindParam(':cart_id', $cart['cart_id']);
        $stmt->execute();
        
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($items)) {
            output("Cart contains items", $items);
        } else {
            output("Cart is empty");
            
            // Transfer session cart items if any exist
            if (!empty($_SESSION['cart'])) {
                output("Found session cart items to transfer");
                
                foreach ($_SESSION['cart'] as $product_id => $item) {
                    $quantity = $item['quantity'] ?? 1;
                    
                    // Verify product exists
                    $productQuery = "SELECT * FROM products WHERE product_id = :product_id";
                    $stmt = $conn->prepare($productQuery);
                    $stmt->bindParam(':product_id', $product_id);
                    $stmt->execute();
                    
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($product) {
                        // Add to database cart
                        $addItemQuery = "INSERT INTO cart_items (cart_id, product_id, quantity) 
                                        VALUES (:cart_id, :product_id, :quantity)";
                        $stmt = $conn->prepare($addItemQuery);
                        $stmt->bindParam(':cart_id', $cart['cart_id']);
                        $stmt->bindParam(':product_id', $product_id);
                        $stmt->bindParam(':quantity', $quantity);
                        
                        if ($stmt->execute()) {
                            output("Added product $product_id to database cart");
                        } else {
                            output("Failed to add product $product_id to cart");
                        }
                    } else {
                        output("Product $product_id not found in database");
                    }
                }
                
                // Clear session cart after transfer
                $_SESSION['cart'] = [];
                output("Session cart cleared after transfer");
            }
        }
    } else {
        output("User has no database cart - creating one");
        
        // Create a new cart for the user
        $createCartQuery = "INSERT INTO carts (user_id) VALUES (:user_id)";
        $stmt = $conn->prepare($createCartQuery);
        $stmt->bindParam(':user_id', $user_id);
        
        if ($stmt->execute()) {
            $cart_id = $conn->lastInsertId();
            output("Created new cart with ID: " . $cart_id);
            
            // Transfer session cart items if any exist
            if (!empty($_SESSION['cart'])) {
                output("Found session cart items to transfer to new cart");
                
                foreach ($_SESSION['cart'] as $product_id => $item) {
                    $quantity = $item['quantity'] ?? 1;
                    
                    // Verify product exists
                    $productQuery = "SELECT * FROM products WHERE product_id = :product_id";
                    $stmt = $conn->prepare($productQuery);
                    $stmt->bindParam(':product_id', $product_id);
                    $stmt->execute();
                    
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($product) {
                        // Add to database cart
                        $addItemQuery = "INSERT INTO cart_items (cart_id, product_id, quantity) 
                                        VALUES (:cart_id, :product_id, :quantity)";
                        $stmt = $conn->prepare($addItemQuery);
                        $stmt->bindParam(':cart_id', $cart_id);
                        $stmt->bindParam(':product_id', $product_id);
                        $stmt->bindParam(':quantity', $quantity);
                        
                        if ($stmt->execute()) {
                            output("Added product $product_id to new database cart");
                        } else {
                            output("Failed to add product $product_id to new cart");
                        }
                    } else {
                        output("Product $product_id not found in database");
                    }
                }
                
                // Clear session cart after transfer
                $_SESSION['cart'] = [];
                output("Session cart cleared after transfer to new cart");
            }
        } else {
            output("Failed to create cart for user");
        }
    }
} else {
    output("User is not logged in - using session cart");
    
    // Check if we need to initialize the session cart
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
        output("Initialized empty session cart");
    }
}

// Add a test product to cart
echo "<h2>Test Product Addition</h2>";
echo "<form method='post'>";
echo "<p>Product ID: <input type='number' name='product_id' value='1' min='1'></p>";
echo "<p>Quantity: <input type='number' name='quantity' value='1' min='1'></p>";
echo "<p><input type='submit' name='add_to_cart' value='Add to Cart'></p>";
echo "</form>";

if (isset($_POST['add_to_cart']) && !empty($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = max(1, (int)$_POST['quantity']);
    
    // Verify product exists
    $productQuery = "SELECT * FROM products WHERE product_id = :product_id";
    $stmt = $conn->prepare($productQuery);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        output("Error: Product with ID $product_id not found");
    } else {
        output("Adding product to cart", $product);
        
        if ($is_logged_in) {
            // Add to database cart
            $cartQuery = "SELECT cart_id FROM carts WHERE user_id = :user_id";
            $stmt = $conn->prepare($cartQuery);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cart) {
                $cart_id = $cart['cart_id'];
                
                // Check if product already in cart
                $checkQuery = "SELECT cart_item_id, quantity FROM cart_items 
                              WHERE cart_id = :cart_id AND product_id = :product_id";
                $stmt = $conn->prepare($checkQuery);
                $stmt->bindParam(':cart_id', $cart_id);
                $stmt->bindParam(':product_id', $product_id);
                $stmt->execute();
                
                $item = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($item) {
                    // Update quantity
                    $new_quantity = $item['quantity'] + $quantity;
                    $updateQuery = "UPDATE cart_items SET quantity = :quantity WHERE cart_item_id = :cart_item_id";
                    $stmt = $conn->prepare($updateQuery);
                    $stmt->bindParam(':quantity', $new_quantity);
                    $stmt->bindParam(':cart_item_id', $item['cart_item_id']);
                    
                    if ($stmt->execute()) {
                        output("Updated cart item quantity to $new_quantity");
                    } else {
                        output("Failed to update cart item");
                    }
                } else {
                    // Add new item
                    $addQuery = "INSERT INTO cart_items (cart_id, product_id, quantity) 
                                VALUES (:cart_id, :product_id, :quantity)";
                    $stmt = $conn->prepare($addQuery);
                    $stmt->bindParam(':cart_id', $cart_id);
                    $stmt->bindParam(':product_id', $product_id);
                    $stmt->bindParam(':quantity', $quantity);
                    
                    if ($stmt->execute()) {
                        output("Added new item to database cart");
                    } else {
                        output("Failed to add item to database cart");
                    }
                }
            } else {
                output("Error: No cart found for logged in user");
            }
        } else {
            // Add to session cart
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                output("Updated session cart quantity");
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product_id,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image'] ?? 'placeholder.jpg',
                    'quantity' => $quantity
                ];
                output("Added new item to session cart");
            }
            
            output("Updated session cart", $_SESSION['cart']);
        }
    }
}

// Quick fixes
echo "<h2>Quick Fixes</h2>";
echo "<form method='post'>";
echo "<p><input type='submit' name='fix_cart_controller' value='Fix Cart Problems'></p>";
echo "</form>";

if (isset($_POST['fix_cart_controller'])) {
    // 1. Make sure session is started
    if (!isset($_SESSION)) {
        session_start();
    }
    
    // 2. Ensure cart view file exists with correct location
    $cart_view_dir = 'views/cart';
    if (!is_dir($cart_view_dir)) {
        mkdir($cart_view_dir, 0755, true);
    }
    
    $cart_index_view = "$cart_view_dir/index.php";
    $cart_view_content = '<?php
// Cart index view
?>
<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Mon panier</h1>
            
            <?php if (!empty($items)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th class="text-center">Prix unitaire</th>
                                <th class="text-center">Quantité</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?= BASE_URL ?>/public/images/products/<?= $item[\'image\'] ?? \'placeholder.jpg\' ?>" alt="<?= htmlspecialchars($item[\'name\']) ?>" class="img-thumbnail me-3" style="width: 60px;">
                                            <div>
                                                <h6 class="mb-0"><?= htmlspecialchars($item[\'name\']) ?></h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?= number_format($item[\'price\'], 2, \',\', \' \') ?> €
                                    </td>
                                    <td class="text-center align-middle">
                                        <?= $item[\'quantity\'] ?>
                                    </td>
                                    <td class="text-end align-middle fw-bold">
                                        <?= number_format($item[\'price\'] * $item[\'quantity\'], 2, \',\', \' \') ?> €
                                    </td>
                                    <td class="text-center align-middle">
                                        <a href="<?= BASE_URL ?>/cart/remove/<?= $item[\'product_id\'] ?? $item[\'id\'] ?>" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total :</td>
                                <td class="text-end fw-bold"><?= number_format($total, 2, \',\', \' \') ?> €</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= BASE_URL ?>/product" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Continuer mes achats
                    </a>
                    <div>
                        <a href="<?= BASE_URL ?>/cart/clear" class="btn btn-outline-danger me-2">
                            <i class="fas fa-trash-alt me-2"></i>Vider le panier
                        </a>
                        <a href="<?= BASE_URL ?>/cart/checkout" class="btn btn-primary">
                            <i class="fas fa-shopping-bag me-2"></i>Passer la commande
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <h4>Votre panier est vide</h4>
                    <p>Vous n\'avez aucun article dans votre panier actuellement.</p>
                    <a href="<?= BASE_URL ?>/product" class="btn btn-primary">Découvrir nos parfums</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>';

    // Create a simplified CartController file
    $cart_controller = 'controllers/CartController.php';
    $controller_content = '<?php
class CartController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        
        // Make sure session is started
        if (!isset($_SESSION)) {
            session_start();
        }
    }
    
    public function index() {
        $items = [];
        $total = 0;
        
        // Check if user is logged in
        if (isset($_SESSION[\'user_id\'])) {
            // Get cart from database
            $query = "SELECT c.cart_id FROM carts c WHERE c.user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(\':user_id\', $_SESSION[\'user_id\']);
            $stmt->execute();
            
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cart) {
                // Get cart items
                $query = "SELECT ci.*, p.name, p.price, p.image 
                          FROM cart_items ci 
                          JOIN products p ON ci.product_id = p.product_id 
                          WHERE ci.cart_id = :cart_id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(\':cart_id\', $cart[\'cart_id\']);
                $stmt->execute();
                
                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Calculate total
                foreach ($items as $item) {
                    $total += $item[\'price\'] * $item[\'quantity\'];
                }
            }
        } else {
            // Use session cart
            if (isset($_SESSION[\'cart\']) && !empty($_SESSION[\'cart\'])) {
                foreach ($_SESSION[\'cart\'] as $product_id => $item) {
                    $items[] = $item;
                    $total += $item[\'price\'] * $item[\'quantity\'];
                }
            }
        }
        
        // Load view
        include \'views/templates/header.php\';
        include \'views/cart/index.php\';
        include \'views/templates/footer.php\';
    }
    
    public function add($product_id) {
        // Get product info
        $query = "SELECT * FROM products WHERE product_id = :product_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(\':product_id\', $product_id);
        $stmt->execute();
        
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            $_SESSION[\'error\'] = "Produit non trouvé";
            header(\'Location: \' . BASE_URL . \'/product\');
            exit;
        }
        
        // Get quantity
        $quantity = isset($_POST[\'quantity\']) ? (int)$_POST[\'quantity\'] : 1;
        $quantity = max(1, $quantity);
        
        // Check if user is logged in
        if (isset($_SESSION[\'user_id\'])) {
            // Get or create user cart
            $query = "SELECT cart_id FROM carts WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(\':user_id\', $_SESSION[\'user_id\']);
            $stmt->execute();
            
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$cart) {
                // Create new cart
                $query = "INSERT INTO carts (user_id) VALUES (:user_id)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(\':user_id\', $_SESSION[\'user_id\']);
                $stmt->execute();
                
                $cart_id = $this->db->lastInsertId();
            } else {
                $cart_id = $cart[\'cart_id\'];
            }
            
            // Check if product already in cart
            $query = "SELECT cart_item_id, quantity FROM cart_items 
                      WHERE cart_id = :cart_id AND product_id = :product_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(\':cart_id\', $cart_id);
            $stmt->bindParam(\':product_id\', $product_id);
            $stmt->execute();
            
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($item) {
                // Update quantity
                $new_quantity = $item[\'quantity\'] + $quantity;
                $query = "UPDATE cart_items SET quantity = :quantity WHERE cart_item_id = :cart_item_id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(\':quantity\', $new_quantity);
                $stmt->bindParam(\':cart_item_id\', $item[\'cart_item_id\']);
                $stmt->execute();
            } else {
                // Add new item
                $query = "INSERT INTO cart_items (cart_id, product_id, quantity) 
                          VALUES (:cart_id, :product_id, :quantity)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(\':cart_id\', $cart_id);
                $stmt->bindParam(\':product_id\', $product_id);
                $stmt->bindParam(\':quantity\', $quantity);
                $stmt->execute();
            }
        } else {
            // Use session cart
            if (!isset($_SESSION[\'cart\'])) {
                $_SESSION[\'cart\'] = [];
            }
            
            if (isset($_SESSION[\'cart\'][$product_id])) {
                $_SESSION[\'cart\'][$product_id][\'quantity\'] += $quantity;
            } else {
                $_SESSION[\'cart\'][$product_id] = [
                    \'id\' => $product_id,
                    \'product_id\' => $product_id,
                    \'name\' => $product[\'name\'],
                    \'price\' => $product[\'price\'],
                    \'image\' => $product[\'image\'] ?? \'placeholder.jpg\',
                    \'quantity\' => $quantity
                ];
            }
        }
        
        $_SESSION[\'success\'] = "Produit ajouté au panier";
        header(\'Location: \' . BASE_URL . \'/cart\');
        exit;
    }
    
    public function remove($product_id) {
        if (isset($_SESSION[\'user_id\'])) {
            // Remove from database cart
            $query = "SELECT cart_id FROM carts WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(\':user_id\', $_SESSION[\'user_id\']);
            $stmt->execute();
            
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cart) {
                $query = "DELETE FROM cart_items 
                          WHERE cart_id = :cart_id AND product_id = :product_id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(\':cart_id\', $cart[\'cart_id\']);
                $stmt->bindParam(\':product_id\', $product_id);
                $stmt->execute();
            }
        } else {
            // Remove from session cart
            if (isset($_SESSION[\'cart\'][$product_id])) {
                unset($_SESSION[\'cart\'][$product_id]);
            }
        }
        
        header(\'Location: \' . BASE_URL . \'/cart\');
        exit;
    }
    
    public function clear() {
        if (isset($_SESSION[\'user_id\'])) {
            // Clear database cart
            $query = "SELECT cart_id FROM carts WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(\':user_id\', $_SESSION[\'user_id\']);
            $stmt->execute();
            
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cart) {
                $query = "DELETE FROM cart_items WHERE cart_id = :cart_id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(\':cart_id\', $cart[\'cart_id\']);
                $stmt->execute();
            }
        }
        
        // Also clear session cart
        $_SESSION[\'cart\'] = [];
        
        header(\'Location: \' . BASE_URL . \'/cart\');
        exit;
    }
}';

    // Write files
    file_put_contents($cart_index_view, $cart_view_content);
    file_put_contents($cart_controller, $controller_content);
    
    output("Fixed cart files created:", [
        "Cart View" => $cart_index_view,
        "Cart Controller" => $cart_controller
    ]);
    
    output("Done! Please replace your existing files with these simplified versions.");
}

// Link to return to cart
echo "<p><a href='" . (defined('BASE_URL') ? BASE_URL : '') . "/cart' class='btn btn-primary'>Go to Cart</a></p>";
?>