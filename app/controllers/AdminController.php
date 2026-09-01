<?php
class AdminController {
    private $db;
    private $session;
    private $brandModel;
    private $categoryModel;
    private $productModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        
        // Initialize models
        require_once APP_PATH . '/models/brand.php';
        require_once APP_PATH . '/models/category.php';
        require_once APP_PATH . '/models/product.php';
        
        $this->brandModel = new Brand();
        $this->categoryModel = new Category();
        $this->productModel = new Product();

        global $session;
        $this->session = $session;
    }

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        // Check if user is admin
        if (!$this->session->has('user_id') || !$this->session->get('is_admin')) {
            header('Location: ' . BASE_URL);
            exit;
        }

        // Fetch counts
        $stmt = $this->db->query('SELECT COUNT(*) FROM products');
        $total_products = $stmt->fetchColumn();
        error_log("Total products count: " . $total_products);

        $stmt = $this->db->query('SELECT COUNT(*) FROM orders');
        $total_orders = $stmt->fetchColumn();
        error_log("Total orders count: " . $total_orders);

        $stmt = $this->db->query('SELECT COUNT(*) FROM users');
        $total_customers = $stmt->fetchColumn();
        error_log("Total customers count: " . $total_customers);

        $stmt = $this->db->query('SELECT COUNT(*) FROM categories');
        $total_categories = $stmt->fetchColumn();
        error_log("Total categories count: " . $total_categories);

        // Fetch total codes count
        $stmt = $this->db->query('SELECT COUNT(*) FROM discount_codes');
        $total_codes = $stmt->fetchColumn();

        // Fetch all codes with user information
        $stmt = $this->db->query('
            SELECT dc.*, u.username 
            FROM discount_codes dc 
            LEFT JOIN users u ON dc.user_id = u.user_id 
            ORDER BY dc.created_at DESC
        ');
        $all_codes = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Fetch recent orders (limit 5)
        $stmt = $this->db->query('SELECT o.order_id, u.username, o.order_date, o.total_amount, o.status FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.order_date DESC LIMIT 5');
        $recent_orders = $stmt->fetchAll(PDO::FETCH_OBJ);
        error_log("Recent orders fetched: " . count($recent_orders));

        // Fetch low stock products (stock_quantity <= 5)
        $stmt = $this->db->query('SELECT product_id, name, stock_quantity FROM products WHERE stock_quantity <= 5 ORDER BY stock_quantity ASC');
        $low_stock_products = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Fetch all brands (only id and name)
        $stmt = $this->db->query('SELECT brand_id, name FROM brands ORDER BY name ASC');
        $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch total brands count
        $stmt = $this->db->query('SELECT COUNT(*) FROM brands');
        $total_brands = $stmt->fetchColumn();

        // Pass counts and data to view
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/admin/dashboard.php';
        include APP_PATH . '/views/templates/footer.php';
    }

    public function categories() {
        // Check if user is admin
        if (!$this->session->has('user_id') || !$this->session->get('is_admin')) {
            header('Location: ' . BASE_URL);
            exit;
        }

        // Fetch categories
        $stmt = $this->db->query('SELECT * FROM categories ORDER BY name ASC');
        $categories = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Pass data to view
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/admin/categories.php';
        include APP_PATH . '/views/templates/footer.php';
    }

    public function products() {
        // Check if user is admin
        if (!$this->session->has('user_id') || !$this->session->get('is_admin')) {
            header('Location: ' . BASE_URL);
            exit;
        }

        // Fetch all products with category and brand names
        $stmt = $this->db->query('
            SELECT p.*, c.name AS category_name, b.name AS brand_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            ORDER BY p.name ASC
        ');
        $products = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Pass data to view
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/admin/products.php';
        include APP_PATH . '/views/templates/footer.php';
    }

    public function updateOrderStatus() {
        // Check if user is admin
        if (!$this->session->has('user_id') || !$this->session->get('is_admin')) {
            header('Location: ' . BASE_URL);
            exit;
        }

        // Fetch orders with status 'Processing'
        $stmt = $this->db->query('SELECT order_id, order_date FROM orders WHERE status = "Processing"');
        $processing_orders = $stmt->fetchAll(PDO::FETCH_OBJ);

        $now = new DateTime();
        foreach ($processing_orders as $order) {
            $orderDate = new DateTime($order->order_date);
            $interval = $orderDate->diff($now);
            if ($interval->h + ($interval->days * 24) >= 24) {
                // Update status to 'Delivered'
                $updateStmt = $this->db->prepare('UPDATE orders SET status = ? WHERE order_id = ?');
                $updateStmt->execute(['Delivered', $order->order_id]);
            }
        }
    }

    public function orders() {
        // Check if user is admin
        if (!$this->session->has('user_id') || !$this->session->get('is_admin')) {
            header('Location: ' . BASE_URL);
            exit;
        }

        // Update order statuses
        $this->updateOrderStatus();

        // Fetch all orders with user info
        $stmt = $this->db->query('
            SELECT o.*, u.username
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.user_id
            ORDER BY o.order_date DESC
        ');
        $orders = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Pass data to view
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/admin/orders.php';
        include APP_PATH . '/views/templates/footer.php';
    }

    public function userDetails($id) {
        // Check if user is admin
        if (!$this->session->has('user_id') || !$this->session->get('is_admin')) {
            header('Location: ' . BASE_URL);
            exit;
        }

        // Fetch user details by ID
        $stmt = $this->db->prepare('SELECT * FROM users WHERE user_id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$user) {
            // User not found, redirect or show error
            header('HTTP/1.0 404 Not Found');
            echo 'User not found';
            exit;
        }

        // Pass data to view
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/admin/user_details.php';
        include APP_PATH . '/views/templates/footer.php';
    }

    public function customers() {
        // Check if user is admin
        if (!$this->session->has('user_id') || !$this->session->get('is_admin')) {
            header('Location: ' . BASE_URL);
            exit;
        }

        // Fetch all users/customers
        $stmt = $this->db->query('SELECT * FROM users ORDER BY username ASC');
        $customers = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Pass data to view
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/admin/customers.php';
        include APP_PATH . '/views/templates/footer.php';
    }

    public function brands() {
        // Check if user is admin
        if (!$this->session->has('user_id') || !$this->session->get('is_admin')) {
            header('Location: ' . BASE_URL);
            exit;
        }

        // Fetch all brands with productcount from brands table
        $stmt = $this->db->query('SELECT brand_id, name, productcount FROM brands ORDER BY name ASC');
        $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Pass data to view
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/admin/brands.php';
        include APP_PATH . '/views/templates/footer.php';
    }

    public function createProduct() {
        // Check if user is admin
        if (!$this->session->has('user_id') || !$this->session->get('is_admin')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $product_name = $_POST['product_name'] ?? '';
                $brand_name = $_POST['brand_name'] ?? '';
                $price = $_POST['price'] ?? 0;
                $category = $_POST['category'] ?? '';
                $description = $_POST['description'] ?? '';
                $image_path = $_POST['image_path'] ?? '';
                $stock_quantity = $_POST['stock_quantity'] ?? 10;
                $volume = $_POST['volume'] ?? 0;
                $concentration = $_POST['concentration'] ?? '';
                $gender = $_POST['gender'] ?? '';

                // Validate required fields
                if (empty($product_name) || empty($brand_name) || empty($price) || empty($category) || empty($description) || empty($volume) || empty($concentration) || empty($gender)) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'All fields are required']);
                    exit;
                }

                // Check if brand exists, if not create it
                $brand = $this->brandModel->findByName($brand_name);
                if (!$brand) {
                    $brand_id = $this->brandModel->create($brand_name);
                } else {
                    $brand_id = $brand['brand_id'];
                }

                // Check if category exists, if not create it
                $category_id = $this->categoryModel->findOrCreate($category);

                // Create the product
                $productData = [
                    'product_name' => $product_name,
                    'brand_id' => $brand_id,
                    'price' => $price,
                    'category_id' => $category_id,
                    'description' => $description,
                    'image_path' => $image_path,
                    'stock_quantity' => $stock_quantity,
                    'volume' => $volume,
                    'concentration' => $concentration,
                    'gender' => $gender
                ];

                if ($this->productModel->create($productData)) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Product added successfully']);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Failed to add product']);
                }
            } catch (Exception $e) {
                error_log('Error in createProduct: ' . $e->getMessage());
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'An error occurred while adding the product: ' . $e->getMessage()]);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        exit;
    }

    // AJAX: Get product details by ID
    public function getProductAjax() {
        if (!$this->session->has('user_id') || !$this->session->get('is_admin')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing product ID']);
            exit;
        }
        $product = $this->productModel;
        if ($product->getById($id)) {
            $productArr = [
                'product_id' => $product->product_id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock_quantity' => $product->stock_quantity,
                'image' => $product->image,
                'category_id' => $product->category_id,
                'brand_id' => $product->brand_id,
                'volume' => $product->volume,
                'concentration' => $product->concentration,
                'gender' => $product->gender
            ];
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'product' => $productArr]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Product not found']);
        }
        exit;
    }

    // AJAX: Update product details
    public function updateProductAjax() {
        if (!$this->session->has('user_id') || !$this->session->get('is_admin')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        $product = $this->productModel;
        $product->product_id = $_POST['product_id'] ?? null;
        $product->name = $_POST['name'] ?? '';
        $product->stock_quantity = $_POST['stock_quantity'] ?? 0;
        $product->price = $_POST['price'] ?? 0;
        $product->concentration = $_POST['concentration'] ?? '';
        $product->gender = $_POST['gender'] ?? '';
        $product->category_id = $_POST['category_id'] ?? null;
        $product->brand_id = $_POST['brand_id'] ?? null;
        $product->description = $_POST['description'] ?? '';
        $product->image = $_POST['image'] ?? '';
        $product->volume = $_POST['volume'] ?? 0;
        $success = $product->update();
        header('Content-Type: application/json');
        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Update failed']);
        }
        exit;
    }

    // AJAX: Delete product by ID
    public function delete($id, $postData = null) {
        try {
            // Vérifier si c'est une requête AJAX
            if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Requête invalide']);
                exit;
            }

            // Vérifier l'authentification admin
            if (!$this->session->has('user_id') || !$this->session->get('is_admin')) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
                exit;
            }

            // Vérifier que l'ID est valide
            if (!is_numeric($id)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID de produit invalide']);
                exit;
            }

            $product = $this->productModel;
            $product->product_id = $id;
            
            // Vérifier si le produit existe
            if (!$product->getById($id)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Produit non trouvé']);
                exit;
            }

            // Supprimer le produit
            $success = $product->delete();
            
            header('Content-Type: application/json');
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Produit supprimé avec succès']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Échec de la suppression du produit']);
            }
        } catch (Throwable $e) {
            error_log('Error in delete product: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'message' => 'Une erreur est survenue lors de la suppression du produit',
                'debug' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function discountCodes() {
        // Check if user is admin
        if (!$this->session->has('user_id') || !$this->session->get('is_admin')) {
            header('Location: ' . BASE_URL);
            exit;
        }

        // Fetch all codes with user information
        $stmt = $this->db->query('
            SELECT dc.*, u.username 
            FROM discount_codes dc 
            LEFT JOIN users u ON dc.user_id = u.user_id 
            ORDER BY dc.created_at DESC
        ');
        $codes = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Include header
        include APP_PATH . '/views/templates/header.php';
        
        // Include the discount codes view
        include APP_PATH . '/views/admin/discount.php';
        
        // Include footer
        include APP_PATH . '/views/templates/footer.php';
    }

    public function userLoyaltyAjax() {
        // Check if user is admin
        if (!$this->session->has('user_id') || !$this->session->get('is_admin')) {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }

        $user_id = $_GET['user_id'] ?? null;
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'ID utilisateur manquant']);
            exit;
        }

        // Get user loyalty information
        $stmt = $this->db->prepare('
            SELECT 
                u.username,
                COALESCE(SUM(oi.quantity), 0) as delivered_items,
                ul.last_discount_code,
                ul.last_discount_used
            FROM users u
            LEFT JOIN orders o ON u.user_id = o.user_id AND o.status = "Delivered"
            LEFT JOIN order_items oi ON o.order_id = oi.order_id
            LEFT JOIN user_loyalty ul ON u.user_id = ul.user_id
            WHERE u.user_id = ?
            GROUP BY u.user_id
        ');
        $stmt->execute([$user_id]);
        $loyalty = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($loyalty) {
            echo json_encode(['success' => true, 'loyalty' => $loyalty]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Aucune donnée trouvée']);
        }
        exit;
    }
}
