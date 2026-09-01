<?php
/**
 * Contrôleur Cart pour gérer les actions liées au panier
 */
class CartController {

    private $db;
    public function placeOrder() {
        global $session;
        
        // Check if the user is logged in
        if (!$session->has('user_id')) {
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }
    
        // Collect order data from POST
        $user_id = $session->get('user_id');
        $address = $_POST['address'] ?? '';
        $city = $_POST['city'] ?? '';
        $postal_code = $_POST['postal_code'] ?? '';
        $country = $_POST['country'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? '';
        $shipping_method = $_POST['shipping_method'] ?? '';
        $payment_method = $_POST['payment_method'] ?? '';
        $cart = $_SESSION['cart'] ?? [];
        $total = 0;
        
        // Calculate total amount from cart
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    
        // Insert order into the database
        $query = 'INSERT INTO orders (user_id, order_date, total_amount, status, shipping_address, shipping_city, shipping_postal_code, shipping_country, payment_method) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($query);
        $status = 'processing'; // Order status is set to 'processing'
        $stmt->execute([$user_id, $total, $status, $address, $city, $postal_code, $country, $payment_method]);
        $order_id = $this->db->lastInsertId(); // Get the last inserted order ID
    
        // Insert each item from the cart into the order_items table
        foreach ($cart as $item) {
            $query = 'INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)';
            $stmt = $this->db->prepare($query);
            $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
    
            // Update product stock after order is placed
            $updateStock = $this->db->prepare('UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?');
            $updateStock->execute([$item['quantity'], $item['id']]);
        }
    
        // Clear the cart after placing the order
        unset($_SESSION['cart']);
    
        // Redirect to order confirmation page
        header('Location: ' . BASE_URL . '/orders/orderconfirmation/' . $order_id);
        exit;
    }
    
    public function __construct() {
        // Initialiser la base de données en utilisant votre classe Database
        $database = new Database();
        $this->db = $database->connect();
        
        // Initialiser le panier
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * Afficher le contenu du panier
     */
    public function index() {
        // Get the items from the cart session
        $cart_items = [];
        $total = 0;
        
        // Transform session cart data to properly formatted cart items
        foreach ($_SESSION['cart'] ?? [] as $product_id => $item) {
            // Calculate subtotal for each item
            $subtotal = $item['price'] * $item['quantity'];
            
            // Get product stock quantity from DB if not already present
            if (!isset($item['stock_quantity'])) {
                $stmt = $this->db->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
                $stmt->execute([$product_id]);
                $stock = $stmt->fetchColumn();
                $item['stock_quantity'] = $stock ?: 10; // Default to 10 if not found
            }
            
            // Add cart_item_id and subtotal to item
            $item['cart_item_id'] = $product_id;
            $item['subtotal'] = $subtotal;
            
            // Add to cart items and total
            $cart_items[] = $item;
            $total += $subtotal;
        }
        
        $item_count = $this->getCartCount();
        
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/cart/index.php';
        include APP_PATH . '/views/templates/footer.php';
    }

    /**
     * Ajouter un produit au panier
     */
    public function add($product_id) {
        // Récupérer le produit directement depuis la base de données
        $query = "SELECT p.*, b.name as brand_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.brand_id 
                WHERE p.product_id = :product_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Produit non trouvé'
                ]);
                exit;
            } else {
                $_SESSION['error'] = 'Produit non trouvé';
                header('Location: ' . BASE_URL . '/product');
                exit;
            }
        }
        
        // Vérifier si le produit est en stock
        if ($product['stock_quantity'] <= 0) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Produit en rupture de stock'
                ]);
                exit;
            } else {
                $_SESSION['error'] = 'Produit en rupture de stock';
                header('Location: ' . BASE_URL . '/product/details/' . $product_id);
                exit;
            }
        }
        
        // Récupérer la quantité
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        // S'assurer que la quantité est au moins 1
        $quantity = max(1, $quantity);
        
        // S'assurer que la quantité ne dépasse pas le stock disponible
        $quantity = min($quantity, $product['stock_quantity']);
        
        // Fix image path
        $imagePath = $product['image'] ?? 'placeholder.jpg';
        
        // If the path has /images/products/, remove it to get just the relative path
        if (strpos($imagePath, '/images/products/') === 0) {
            $imagePath = substr($imagePath, strlen('/images/products/'));
        } 
        // If path still starts with a slash, remove it
        elseif (strpos($imagePath, '/') === 0) {
            $imagePath = ltrim($imagePath, '/');
        }
        
        // Ajouter au panier (session)
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'id' => $product_id,
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $imagePath,
                'brand_name' => $product['brand_name'] ?? '',
                'quantity' => $quantity,
                'stock_quantity' => $product['stock_quantity']
            ];
        }
        
        // Mettre à jour la session
        $_SESSION['cart_updated'] = true;
        
        // Si c'est une requête AJAX, retourner une réponse JSON
        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'productId' => $product_id,
                'productName' => $product['name'],
                'productPrice' => $product['price'],
                'productImage' => $imagePath,
                'productBrand' => $product['brand_name'] ?? '',
                'quantity' => $quantity,
                'cartCount' => $this->getCartCount(),
                'cartTotal' => $this->getCartTotal(),
                'message' => 'Le produit a été ajouté à votre panier.'
            ]);
            exit;
        } else {
            // Sinon, rediriger vers la page de confirmation traditionnelle
            $_SESSION['success'] = 'Le produit ' . $product['name'] . ' a été ajouté à votre panier.';
            header('Location: ' . BASE_URL . '/cart/added/' . $product_id);
            exit;
        }
    }
    
    /**
     * Page de confirmation d'ajout au panier
     */
    public function added($product_id) {
        // Récupérer le produit directement depuis la base de données
        $query = "SELECT p.*, b.name as brand_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.brand_id 
                WHERE p.product_id = :product_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Calculate cart totals
        $cart_total = $this->getCartTotal();
        $cart_count = $this->getCartCount();
        
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/cart/added.php';
        include APP_PATH . '/views/templates/footer.php';
    }
    
    /**
     * Afficher la page de validation de commande
     */
    public function checkout() {
        // Redirect if cart is empty
        if (empty($_SESSION['cart'])) {
            $_SESSION['error'] = 'Votre panier est vide. Veuillez ajouter des produits avant de passer commande.';
            header('Location: ' . BASE_URL . '/cart');
            exit;
        }
        
        // Transform cart data to cart items for the view
        $cart_items = [];
        $total = 0;
        
        foreach ($_SESSION['cart'] as $product_id => $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $item['subtotal'] = $subtotal;
            $cart_items[] = $item;
            $total += $subtotal;
        }
        
        // Get user data if logged in
        $user_model = null;
        if (isset($_SESSION['user_id'])) {
            // Load user model data
            $user_id = $_SESSION['user_id'];
            $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $user_model = $stmt->fetch(PDO::FETCH_OBJ);
        }
        
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/cart/checkout.php';
        include APP_PATH . '/views/templates/footer.php';
    }
    
    /**
     * Calculer le nombre total d'articles dans le panier
     */
    private function getCartCount() {
        $count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }
    
    /**
     * Calculer le montant total du panier
     */
    private function getCartTotal() {
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
    
    /**
     * Mettre à jour la quantité d'un produit dans le panier
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/cart');
            exit;
        }
        
        if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
            $product_id = $_POST['product_id'];
            $quantity = (int)$_POST['quantity'];
            
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$product_id]);
            } else {
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                }
            }
            
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'cartCount' => $this->getCartCount(),
                    'cartTotal' => $this->getCartTotal()
                ]);
                exit;
            }
        }
        
        header('Location: ' . BASE_URL . '/cart');
        exit;
    }
    
    /**
     * Supprimer un produit du panier
     */
    public function remove($product_id) {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        
        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'cartCount' => $this->getCartCount(),
                'cartTotal' => $this->getCartTotal()
            ]);
            exit;
        }
        
        header('Location: ' . BASE_URL . '/cart');
        exit;
    }
    
    /**
     * Vider le panier
     */
    public function clear() {
        $_SESSION['cart'] = [];
        
        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true
            ]);
            exit;
        }
        
        header('Location: ' . BASE_URL . '/cart');
        exit;
    }
    
    /**
     * Vérifier si la requête est AJAX
     */
    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}