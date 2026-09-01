<?php
class OrderItem {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function deleteByOrderId($order_id) {
        $query = 'DELETE FROM order_items WHERE order_id = ?';
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$order_id]);
    }
}
