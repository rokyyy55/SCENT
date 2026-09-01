<?php
/**
 * Modèle Cart pour gérer le panier d'achat
 */
class Cart {
    private $conn;
    private $table = 'carts';
    private $items_table = 'cart_items';

    // Propriétés
    public $cart_id;
    public $user_id;
    public $created_at;
    public $updated_at;

    /**
     * Constructeur avec connexion DB
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Créer un nouveau panier
     */
    public function create($user_id = null) {
        $query = 'INSERT INTO ' . $this->table . ' (user_id) VALUES (:user_id)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        
        if ($stmt->execute()) {
            $this->cart_id = $this->conn->lastInsertId();
            $this->user_id = $user_id;
            return true;
        }

        return false;
    }

    /**
     * Obtenir un panier par ID utilisateur
     */
    public function getByUser($user_id) {
        $query = 'SELECT cart_id, user_id, created_at, updated_at 
                FROM ' . $this->table . ' 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT 0,1';
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->cart_id = $row['cart_id'];
            $this->user_id = $row['user_id'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }

        return false;
    }

    /**
     * Obtenir un panier par ID
     */
    public function getById($cart_id) {
        $query = 'SELECT cart_id, user_id, created_at, updated_at 
                FROM ' . $this->table . ' 
                WHERE cart_id = :cart_id 
                LIMIT 0,1';
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->cart_id = $row['cart_id'];
            $this->user_id = $row['user_id'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }

        return false;
    }

    /**
     * Ajouter un produit au panier
     */
    public function addItem($product_id, $quantity = 1) {
        // Vérifier si le produit existe déjà dans le panier
        $query = 'SELECT cart_item_id, quantity 
                FROM ' . $this->items_table . ' 
                WHERE cart_id = :cart_id AND product_id = :product_id 
                LIMIT 0,1';
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cart_id', $this->cart_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();

        // Si le produit existe déjà, mettre à jour la quantité
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $new_quantity = $row['quantity'] + $quantity;
            
            $update_query = 'UPDATE ' . $this->items_table . ' 
                          SET quantity = :quantity 
                          WHERE cart_item_id = :cart_item_id';
                          
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(':quantity', $new_quantity);
            $update_stmt->bindParam(':cart_item_id', $row['cart_item_id']);
            
            return $update_stmt->execute();
        } 
        // Sinon, ajouter un nouvel élément
        else {
            $insert_query = 'INSERT INTO ' . $this->items_table . ' 
                          (cart_id, product_id, quantity) 
                          VALUES (:cart_id, :product_id, :quantity)';
                          
            $insert_stmt = $this->conn->prepare($insert_query);
            $insert_stmt->bindParam(':cart_id', $this->cart_id);
            $insert_stmt->bindParam(':product_id', $product_id);
            $insert_stmt->bindParam(':quantity', $quantity);
            
            return $insert_stmt->execute();
        }
    }

    /**
     * Mettre à jour la quantité d'un produit dans le panier
     */
    public function updateItem($cart_item_id, $quantity) {
        // Si la quantité est 0 ou moins, supprimer l'élément
        if ($quantity <= 0) {
            return $this->removeItem($cart_item_id);
        }

        $query = 'UPDATE ' . $this->items_table . ' 
                SET quantity = :quantity 
                WHERE cart_item_id = :cart_item_id AND cart_id = :cart_id';
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':cart_item_id', $cart_item_id);
        $stmt->bindParam(':cart_id', $this->cart_id);
        
        return $stmt->execute();
    }

    /**
     * Supprimer un produit du panier
     */
    public function removeItem($cart_item_id) {
        $query = 'DELETE FROM ' . $this->items_table . ' 
                WHERE cart_item_id = :cart_item_id AND cart_id = :cart_id';
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cart_item_id', $cart_item_id);
        $stmt->bindParam(':cart_id', $this->cart_id);
        
        return $stmt->execute();
    }

    /**
     * Vider le panier
     */
    public function clear() {
        $query = 'DELETE FROM ' . $this->items_table . ' WHERE cart_id = :cart_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cart_id', $this->cart_id);
        
        return $stmt->execute();
    }

    /**
     * Obtenir tous les éléments du panier
     */
    public function getItems() {
        $query = 'SELECT 
                    ci.cart_item_id, 
                    ci.product_id, 
                    ci.quantity,
                    p.name,
                    p.price,
                    p.image,
                    p.stock_quantity,
                    (ci.quantity * p.price) as subtotal
                FROM 
                    ' . $this->items_table . ' ci
                JOIN
                    products p ON ci.product_id = p.product_id
                WHERE 
                    ci.cart_id = :cart_id';
                    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cart_id', $this->cart_id);
        $stmt->execute();
        
        return $stmt;
    }

    /**
     * Obtenir le nombre total d'articles dans le panier
     */
    public function getItemCount() {
        $query = 'SELECT SUM(quantity) as total_items 
                FROM ' . $this->items_table . ' 
                WHERE cart_id = :cart_id';
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cart_id', $this->cart_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total_items'] ? $row['total_items'] : 0;
    }

    /**
     * Obtenir le montant total du panier
     */
    public function getTotal() {
        $query = 'SELECT 
                    SUM(ci.quantity * p.price) as total 
                FROM 
                    ' . $this->items_table . ' ci
                JOIN
                    products p ON ci.product_id = p.product_id
                WHERE 
                    ci.cart_id = :cart_id';
                    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cart_id', $this->cart_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'] ? $row['total'] : 0;
    }

    /**
     * Associer un panier à un utilisateur (après connexion)
     */
    public function assignToUser($user_id) {
        $query = 'UPDATE ' . $this->table . ' 
                SET user_id = :user_id 
                WHERE cart_id = :cart_id';
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':cart_id', $this->cart_id);
        
        return $stmt->execute();
    }

    /**
     * Fusionner les paniers (session et utilisateur)
     */
    public function merge($session_cart_id) {
        // Obtenir tous les éléments du panier de session
        $query = 'SELECT product_id, quantity 
                FROM ' . $this->items_table . ' 
                WHERE cart_id = :session_cart_id';
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':session_cart_id', $session_cart_id);
        $stmt->execute();
        
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ajouter chaque élément au panier de l'utilisateur
        foreach ($items as $item) {
            $this->addItem($item['product_id'], $item['quantity']);
        }
        
        // Supprimer le panier de session
        $delete_query = 'DELETE FROM ' . $this->table . ' WHERE cart_id = :session_cart_id';
        $delete_stmt = $this->conn->prepare($delete_query);
        $delete_stmt->bindParam(':session_cart_id', $session_cart_id);
        
        return $delete_stmt->execute();
    }
}