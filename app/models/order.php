<?php
/**
 * Modèle Order pour gérer les commandes
 */
class Order {
    private $conn;
    private $table = 'orders';
    private $items_table = 'order_items';

    // Propriétés
    public $order_id;
    public $user_id;
    public $order_date;
    public $total_amount;
    public $status;
    public $shipping_address;
    public $shipping_city;
    public $shipping_postal_code;
    public $shipping_country;
    public $payment_method;

    /**
     * Constructeur avec connexion DB
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Créer une commande à partir d'un panier
     */
    public function createFromCart($cart_id, $user_id, $shipping_info, $payment_method) {
        // Utiliser la procédure stockée pour finaliser la commande
        $order_id = null;
        $params = [
            $user_id,
            $shipping_info['address'],
            $shipping_info['city'],
            $shipping_info['postal_code'],
            $shipping_info['country'],
            $payment_method,
            &$order_id
        ];
        
        $database = new Database();
        $results = $database->callProcedure('FinalizeOrder', $params);
        
        if ($order_id) {
            $this->order_id = $order_id;
            return true;
        }
        
        return false;
    }

    /**
     * Obtenir une commande par ID
     */
    public function getById($order_id, $user_id = null) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE order_id = :order_id';
        
        // Si user_id est fourni, vérifier que la commande appartient à cet utilisateur
        if ($user_id) {
            $query .= ' AND user_id = :user_id';
        }
        
        $query .= ' LIMIT 0,1';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        
        if ($user_id) {
            $stmt->bindParam(':user_id', $user_id);
        }
        
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->order_id = $row['order_id'];
            $this->user_id = $row['user_id'];
            $this->order_date = $row['order_date'];
            $this->total_amount = $row['total_amount'];
            $this->status = $row['status'];
            $this->shipping_address = $row['shipping_address'];
            $this->shipping_city = $row['shipping_city'];
            $this->shipping_postal_code = $row['shipping_postal_code'];
            $this->shipping_country = $row['shipping_country'];
            $this->payment_method = $row['payment_method'];
            
            return true;
        }
        
        return false;
    }

    /**
     * Obtenir les commandes d'un utilisateur
     */
    public function getByUser($user_id) {
        // Utiliser la procédure stockée pour obtenir l'historique des commandes
        $database = new Database();
        return $database->callProcedure('GetOrderHistory', [$user_id]);
    }

    /**
     * Obtenir les détails d'une commande
     */
    public function getDetails() {
        if (!$this->order_id) {
            return false;
        }
        
        // Utiliser la procédure stockée pour obtenir les détails de la commande
        $database = new Database();
        return $database->callProcedure('GetOrderDetails', [$this->order_id, $this->user_id]);
    }

    /**
     * Obtenir les éléments d'une commande
     */
    public function getItems() {
        $query = 'SELECT 
                    oi.order_item_id, 
                    oi.product_id, 
                    oi.quantity,
                    oi.price,
                    p.name,
                    p.image,
                    (oi.quantity * oi.price) as subtotal
                FROM 
                    ' . $this->items_table . ' oi
                JOIN
                    products p ON oi.product_id = p.product_id
                WHERE 
                    oi.order_id = :order_id';
                    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $this->order_id);
        $stmt->execute();
        
        return $stmt;
    }

    /**
     * Mettre à jour le statut d'une commande
     */
    public function updateStatus($status) {
        $query = 'UPDATE ' . $this->table . ' 
                SET status = :status 
                WHERE order_id = :order_id';
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':order_id', $this->order_id);
        
        if ($stmt->execute()) {
            $this->status = $status;
            return true;
        }
        
        return false;
    }

    /**
     * Annuler une commande
     */
    public function cancel($reason = '') {
        // Mettre à jour le statut de la commande à 'Cancelled'
        // Le trigger restaurera automatiquement le stock et enregistrera dans l'historique
        return $this->updateStatus('Cancelled');
    }

    /**
     * Obtenir toutes les commandes (pour l'administration)
     */
    public function getAll($limit = null, $offset = null) {
        $query = 'SELECT 
                    o.order_id, 
                    o.user_id, 
                    u.username,
                    o.order_date, 
                    o.total_amount, 
                    o.status,
                    o.shipping_address,
                    o.shipping_city
                FROM 
                    ' . $this->table . ' o
                LEFT JOIN
                    users u ON o.user_id = u.user_id
                ORDER BY 
                    o.order_date DESC';
        
        // Ajouter la pagination si les paramètres sont fournis
        if ($limit !== null && $offset !== null) {
            $query .= ' LIMIT :limit OFFSET :offset';
        }
                
        $stmt = $this->conn->prepare($query);
        
        // Lier les paramètres de pagination si fournis
        if ($limit !== null && $offset !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir les commandes par statut
     */
    public function getByStatus($status, $limit = 10, $offset = 0) {
        $query = 'SELECT 
                    o.order_id, 
                    o.user_id, 
                    u.username,
                    o.order_date, 
                    o.total_amount, 
                    o.status,
                    o.shipping_address,
                    o.shipping_city
                FROM 
                    ' . $this->table . ' o
                LEFT JOIN
                    users u ON o.user_id = u.user_id
                WHERE
                    o.status = :status
                ORDER BY 
                    o.order_date DESC
                LIMIT :limit OFFSET :offset';
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }

    /**
     * Compter le nombre de commandes par statut
     */
    public function countByStatus($status) {
        $query = 'SELECT COUNT(*) as count FROM ' . $this->table . ' WHERE status = :status';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['count'];
    }

    /**
     * Récupère le nombre total de commandes
     */
    public function getTotalCount() {
        $query = 'SELECT COUNT(*) as total FROM ' . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Récupère les commandes récentes
     */
    public function getRecentOrders($limit = 5) {
        $query = 'SELECT 
                    o.order_id,
                    o.user_id,
                    o.total_amount,
                    o.status,
                    o.order_date,
                    o.shipping_address,
                    o.shipping_city,
                    o.shipping_postal_code,
                    o.shipping_country,
                    u.username,
                    u.email
                FROM 
                    ' . $this->table . ' o
                LEFT JOIN
                    users u ON o.user_id = u.user_id
                ORDER BY
                    o.order_date DESC
                LIMIT :limit';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Supprime une commande par ID
     */
    public function delete($order_id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE order_id = :order_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        return $stmt->execute();
    }
}
