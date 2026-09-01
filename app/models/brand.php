<?php

class Brand {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function findByName($name) {
        $query = "SELECT * FROM brands WHERE name = :name";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name) {
        $query = "INSERT INTO brands (name) VALUES (:name)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
} 