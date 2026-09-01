<?php
class OrdersController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function placeOrder() {
        // DEBUG: Show errors directly in browser for troubleshooting
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Remove user_id session check for guest checkout
        // if (!isset($_SESSION['user_id'])) {
        //     die('DEBUG: user_id not set in session');
        // }
        if (!isset($_POST['payment_method'])) {
            die('DEBUG: payment_method not set in POST');
        }
        if (empty($_SESSION['cart'])) {
            die('DEBUG: cart is empty');
        }

        global $session;

        try {
            $this->db->beginTransaction();

            // Calculate total with discount if applicable
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Add shipping cost
            $shipping = $total >= 50 ? 0 : 4.90;
            $total_with_shipping = $total + $shipping;

            // Apply discount if code exists (from POST, not session)
            $discount_amount = 0;
            if ($user_id && isset($_POST['discount_code']) && !empty($_POST['discount_code'])) {
                $stmt = $this->db->prepare("SELECT * FROM discount_codes WHERE code = ? AND user_id = ? AND is_used = 0");
                $stmt->execute([$_POST['discount_code'], $user_id]);
                $discount = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($discount) {
                    // Apply fixed 50 euro discount only if code is valid
                    $discount_amount = 50;
                }
            }
            $total_with_shipping -= $discount_amount;

            // Use user_id if logged in, otherwise NULL
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

            // Create order
            $stmt = $this->db->prepare("
                INSERT INTO orders (user_id, total_amount, status, delivery_town, phone_number)
                VALUES (?, ?, 'Processing', ?, ?)
            ");
            $stmt->execute([
                $user_id,
                $total_with_shipping,
                $_POST['delivery_town'],
                !empty($_POST['phone_number']) ? $_POST['phone_number'] : null
            ]);
            $order_id = $this->db->lastInsertId();
            // DEBUG: Log order_id after insert
            file_put_contents(__DIR__ . '/../../order_debug.log', date('Y-m-d H:i:s') . " - Order inserted with order_id: $order_id\n", FILE_APPEND);

            // Mark discount code as used only after order is created
            if ($user_id && isset($_POST['discount_code']) && !empty($_POST['discount_code'])) {
                $stmt = $this->db->prepare("UPDATE discount_codes SET is_used = 1, used_at = NOW() WHERE code = ? AND user_id = ? AND is_used = 0");
                $stmt->execute([$_POST['discount_code'], $user_id]);
            }

            // Insert each item from the cart into the order_items table
            foreach ($_SESSION['cart'] as $item) {
                $query = 'INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)';
                $stmt = $this->db->prepare($query);
                $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);

                // Update product stock after order is placed
                $updateStock = $this->db->prepare('UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?');
                $updateStock->execute([$item['quantity'], $item['id']]);
            }

            $this->db->commit();
            // DEBUG: Log commit
            file_put_contents(__DIR__ . '/../../order_debug.log', date('Y-m-d H:i:s') . " - Order committed for order_id: $order_id\n", FILE_APPEND);

            // --- LOYALTY LOGIC START ---
            if ($user_id) { // Only run loyalty logic for logged-in users
                // Count total delivered items for this user
                $stmt = $this->db->prepare("SELECT SUM(oi.quantity) FROM orders o JOIN order_items oi ON o.order_id = oi.order_id WHERE o.user_id = ? AND o.status = 'Delivered'");
                $stmt->execute([$user_id]);
                $delivered_items = (int)$stmt->fetchColumn();
                // Check last_discount_used
                $stmt = $this->db->prepare("SELECT last_discount_used FROM user_loyalty WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $last_discount_used = $stmt->fetchColumn();
                $threshold = 5;
                if ($delivered_items >= $threshold && $last_discount_used) {
                    // Generate a 4-digit code
                    $code = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                    // Insert into discount_codes
                    $stmt = $this->db->prepare("INSERT INTO discount_codes (user_id, code, discount_amount, is_used) VALUES (?, ?, ?, 0)");
                    $stmt->execute([$user_id, $code, 50]);
                    // Update last_discount_code and mark as not used
                    $stmt = $this->db->prepare("UPDATE user_loyalty SET last_discount_code = ?, last_discount_used = 0 WHERE user_id = ?");
                    $stmt->execute([$code, $user_id]);
                }
            }
            // --- LOYALTY LOGIC END ---
            unset($_SESSION['cart']);

            // Redirect to order confirmation page
            header('Location: ' . BASE_URL . '/orders/orderconfirmation/' . $order_id);
            exit;
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Erreur lors de la confirmation de la commande. Veuillez réessayer plus tard.';
            header('Location: ' . BASE_URL . '/cart');
            exit;
        }
    }

    public function orderconfirmation($order_id) {
        // Fetch order by ID
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE order_id = ?');
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$order) {
            // Order not found, show 404 or error
            header("HTTP/1.0 404 Not Found");
            echo "Order not found.";
            exit;
        }

        // Fetch order items
        $stmt = $this->db->prepare('SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?');
        $stmt->execute([$order_id]);
        $order_items = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Prepare image paths with fallback for order items
        $placeholder = BASE_URL . '/public/images/products/placeholder.jpg';
        foreach ($order_items as $item) {
            $imageRelPath = ltrim($item->image, '/');
            $imageFile = $_SERVER['DOCUMENT_ROOT'] . '/scent/public/' . $imageRelPath;
            if (empty($item->image) || !file_exists($imageFile)) {
                $item->image = $placeholder;
            } else {
                // Set image to relative path only, not full URL
                $item->image = $imageRelPath;
            }
        }

        // Include header, view, footer
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/orders/orderconfirmation.php';
    }

    public function listOrders() {
        global $session;
        $user_id = $session->get('user_id') ?? null;

        if (!$user_id) {
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }

        // Fetch orders for the logged-in user
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC');
        $stmt->execute([$user_id]);
        $orders = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Update status from 'Processing' to 'Delivered' if 24 hours have passed
        $now = new DateTime();
        foreach ($orders as $order) {
            if ($order->status === 'Processing') {
                $orderDate = new DateTime($order->order_date);
                $interval = $orderDate->diff($now);
                if ($interval->h + ($interval->days * 24) >= 24) {
                    // Update status to 'Delivered'
                    $updateStmt = $this->db->prepare('UPDATE orders SET status = ? WHERE order_id = ?');
                    $updateStmt->execute(['Delivered', $order->order_id]);
                    $order->status = 'Delivered'; // Update local object for display
                }
            }
        }

        // Include header and list of orders view
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/orders/listoforders.php';
    }

    public function cancelOrder($order_id) {
        global $session;
        $user_id = $session->get('user_id') ?? null;

        if (!$user_id) {
            $_SESSION['error'] = 'Vous devez être connecté pour annuler une commande.';
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }

        require_once APP_PATH . '/models/order.php';
        require_once APP_PATH . '/models/orderitem.php';

        $orderModel = new Order();
        $orderItemModel = new OrderItem();

        // Check if order exists and belongs to user
        $order = $orderModel->getById($order_id, $user_id);
        if (!$order) {
            $_SESSION['error'] = 'Commande introuvable ou vous n\'êtes pas autorisé à l\'annuler.';
            header('Location: ' . BASE_URL . '/orders');
            exit;
        }

        // Delete all associated order items first
        if (!$orderItemModel->deleteByOrderId($order_id)) {
            $_SESSION['error'] = 'Erreur lors de la suppression des articles de commande.';
            header('Location: ' . BASE_URL . '/orders');
            exit;
        }

        // Then delete the order itself
        if ($orderModel->delete($order_id)) {
            // Display success page with message only, no redirect
            include APP_PATH . '/views/orders/cancelled.php';
            exit;
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression de la commande.';
            header('Location: ' . BASE_URL . '/orders');
            exit;
        }
    }

    public function codes() {
        global $session;
        
        // Check if user is logged in
        if (!$session->has('user_id')) {
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }
        
        // Get user's codes from database
        $database = new Database();
        $conn = $database->connect();
        
        $query = 'SELECT * FROM discount_codes 
                 WHERE user_id = :user_id 
                 ORDER BY created_at DESC';
                 
        $stmt = $conn->prepare($query);
        $user_id = $session->get('user_id');
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $codes = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        // Include header
        include APP_PATH . '/views/templates/header.php';
        
        // Include the codes view
        include APP_PATH . '/views/orders/codes.php';
        
        // Include footer
        include APP_PATH . '/views/templates/footer.php';
    }

    public function validateCodeAjax() {
        $code = $_GET['code'] ?? '';
        session_start();
        $user_id = $_SESSION['user_id'] ?? null;
        $valid = false;
        if ($user_id && $code) {
            $stmt = $this->db->prepare("SELECT * FROM discount_codes WHERE code = ? AND user_id = ? AND is_used = 0");
            $stmt->execute([$code, $user_id]);
            $valid = $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
        }
        header('Content-Type: application/json');
        echo json_encode(['valid' => $valid]);
        exit;
    }
}
