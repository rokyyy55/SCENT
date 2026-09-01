<?php
class WishlistController {
    public function toggle() {
        global $session;
        if (!$session->has('user_id')) {
            echo json_encode(['error' => 'not_logged_in']);
            exit;
        }
        $user_id = $session->get('user_id');
        $product_id = $_POST['product_id'] ?? null;
        if (!$product_id) {
            echo json_encode(['error' => 'no_product_id']);
            exit;
        }
        require_once APP_PATH . '/models/Wishlist.php';
        $wishlist = new Wishlist();
        $inWishlist = $wishlist->toggle($user_id, $product_id);
        echo json_encode(['in_wishlist' => $inWishlist]);
        exit;
    }

    public function index() {
        global $session;
        if (!$session->has('user_id')) {
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }
        require_once APP_PATH . '/models/Wishlist.php';
        $wishlist = new Wishlist();
        $products = $wishlist->getWishlistProducts($session->get('user_id'));
        include APP_PATH . '/views/user/wishlist.php';
    }
} 