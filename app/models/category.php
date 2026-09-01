<?php

class Category {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function findOrCreate($name) {
        $query = "SELECT category_id FROM categories WHERE name = :name";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($category) {
            return $category['category_id'];
        }

        $query = "INSERT INTO categories (name) VALUES (:name)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
} 