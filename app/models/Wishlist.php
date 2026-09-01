<?php
class Wishlist {
    private $conn;
    private $table = 'wishlist';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function isInWishlist($user_id, $product_id) {
        $stmt = $this->conn->prepare('SELECT 1 FROM ' . $this->table . ' WHERE user_id = ? AND product_id = ?');
        $stmt->execute([$user_id, $product_id]);
        return $stmt->fetch() ? true : false;
    }

    public function add($user_id, $product_id) {
        $stmt = $this->conn->prepare('INSERT IGNORE INTO ' . $this->table . ' (user_id, product_id) VALUES (?, ?)');
        return $stmt->execute([$user_id, $product_id]);
    }

    public function remove($user_id, $product_id) {
        $stmt = $this->conn->prepare('DELETE FROM ' . $this->table . ' WHERE user_id = ? AND product_id = ?');
        return $stmt->execute([$user_id, $product_id]);
    }

    public function toggle($user_id, $product_id) {
        if ($this->isInWishlist($user_id, $product_id)) {
            $this->remove($user_id, $product_id);
            return false;
        } else {
            $this->add($user_id, $product_id);
            return true;
        }
    }

    public function getUserWishlist($user_id) {
        $stmt = $this->conn->prepare('SELECT product_id FROM ' . $this->table . ' WHERE user_id = ?');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getWishlistProducts($user_id) {
        $stmt = $this->conn->prepare('
            SELECT p.*, b.name as brand_name
            FROM ' . $this->table . ' w
            JOIN products p ON w.product_id = p.product_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            WHERE w.user_id = ?
        ');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
} 