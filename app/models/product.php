<?php
/**
 * Modèle Product pour gérer les parfums
 */
class Product {
    private $conn;
    private $table = 'products';

    // Propriétés du produit
    public $product_id;
    public $name;
    public $description;
    public $price;
    public $stock_quantity;
    public $image;
    public $category_id;
    public $brand_id;
    public $volume;
    public $concentration;
    public $gender;
    public $created_at;
    public $updated_at;

    // Propriétés liées (relations)
    public $category_name;
    public $brand_name;

    /**
     * Constructeur avec connexion DB
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Récupérer tous les produits
     */
    public function getAll($limit = 10, $offset = 0) {
        // First check if average_rating column exists
        $checkColumnQuery = "SHOW COLUMNS FROM products LIKE 'average_rating'";
        $checkStmt = $this->conn->prepare($checkColumnQuery);
        $checkStmt->execute();
        $columnExists = $checkStmt->rowCount() > 0;

        $query = 'SELECT 
                    p.product_id, 
                    p.name, 
                    p.description, 
                    p.price, 
                    p.stock_quantity, 
                    p.image, 
                    p.volume, 
                    p.concentration, 
                    p.gender,
                    ' . ($columnExists ? 'p.average_rating,' : '0 as average_rating,') . '
                    c.name as category_name,
                    b.name as brand_name
                FROM 
                    ' . $this->table . ' p
                LEFT JOIN
                    categories c ON p.category_id = c.category_id
                LEFT JOIN
                    brands b ON p.brand_id = b.brand_id
                ORDER BY
                    p.name ASC
                LIMIT :limit OFFSET :offset';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer un seul produit
     */
    public function getSingle() {
        // First check if average_rating column exists
        $checkColumnQuery = "SHOW COLUMNS FROM products LIKE 'average_rating'";
        $checkStmt = $this->conn->prepare($checkColumnQuery);
        $checkStmt->execute();
        $columnExists = $checkStmt->rowCount() > 0;

        // Build the query based on whether the column exists
        $query = 'SELECT 
                    p.product_id, 
                    p.name, 
                    p.description, 
                    p.price, 
                    p.stock_quantity, 
                    p.image, 
                    p.category_id, 
                    p.brand_id, 
                    p.volume, 
                    p.concentration, 
                    p.gender,
                    p.created_at,
                    p.updated_at,
                    ' . ($columnExists ? 'p.average_rating,' : '0 as average_rating,') . '
                    c.name as category_name,
                    b.name as brand_name
                FROM 
                    ' . $this->table . ' p
                LEFT JOIN
                    categories c ON p.category_id = c.category_id
                LEFT JOIN
                    brands b ON p.brand_id = b.brand_id
                WHERE 
                    p.product_id = :product_id
                LIMIT 0,1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Définir les propriétés
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->stock_quantity = $row['stock_quantity'];
            $this->image = $row['image'];
            $this->category_id = $row['category_id'];
            $this->brand_id = $row['brand_id'];
            $this->volume = $row['volume'];
            $this->concentration = $row['concentration'];
            $this->gender = $row['gender'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            $this->category_name = $row['category_name'];
            $this->brand_name = $row['brand_name'];
            
            return true;
        }

        return false;
    }

    /**
     * Créer un nouveau produit
     */
    public function create($data) {
        $query = "INSERT INTO products (
            name, brand_id, price, category_id, description, 
            image, stock_quantity, volume, concentration, gender
        ) VALUES (
            :name, :brand_id, :price, :category_id, :description,
            :image, :stock_quantity, :volume, :concentration, :gender
        )";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $data['product_name']);
        $stmt->bindParam(':brand_id', $data['brand_id']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':image', $data['image_path']);
        $stmt->bindParam(':stock_quantity', $data['stock_quantity']);
        $stmt->bindParam(':volume', $data['volume']);
        $stmt->bindParam(':concentration', $data['concentration']);
        $stmt->bindParam(':gender', $data['gender']);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Error creating product: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mettre à jour un produit
     */
    public function update() {
        $query = 'UPDATE ' . $this->table . '
                SET 
                    name = :name, 
                    description = :description, 
                    price = :price, 
                    stock_quantity = :stock_quantity, 
                    image = :image, 
                    category_id = :category_id, 
                    brand_id = :brand_id, 
                    volume = :volume, 
                    concentration = :concentration, 
                    gender = :gender
                WHERE 
                    product_id = :product_id';

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        $this->product_id = intval($this->product_id);
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = floatval($this->price);
        $this->stock_quantity = intval($this->stock_quantity);
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->category_id = intval($this->category_id);
        $this->brand_id = intval($this->brand_id);
        $this->volume = intval($this->volume);
        $this->concentration = htmlspecialchars(strip_tags($this->concentration));
        $this->gender = htmlspecialchars(strip_tags($this->gender));

        // Lier les données
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':stock_quantity', $this->stock_quantity);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':brand_id', $this->brand_id);
        $stmt->bindParam(':volume', $this->volume);
        $stmt->bindParam(':concentration', $this->concentration);
        $stmt->bindParam(':gender', $this->gender);

        if ($stmt->execute()) {
            return true;
        }

        // Afficher l'erreur si quelque chose ne va pas
        $errorInfo = $stmt->errorInfo();
        printf("Erreur PDO: %s\n", $errorInfo[2]);
        return false;
    }

    /**
     * Supprimer un produit
     */
    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE product_id = :product_id';
        $stmt = $this->conn->prepare($query);

        // Nettoyer l'ID
        $this->product_id = intval($this->product_id);

        // Lier l'ID
        $stmt->bindParam(':product_id', $this->product_id);

        if ($stmt->execute()) {
            return true;
        }

        // Afficher l'erreur si quelque chose ne va pas
        $errorInfo = $stmt->errorInfo();
        printf("Erreur PDO: %s\n", $errorInfo[2]);
        return false;
    }

    /**
     * Rechercher des produits
     */
    public function search($keyword, $category = null, $brand = null, $min_price = null, $max_price = null, $gender = null) {
        // Construire la requête de base
        $query = 'SELECT 
                    p.product_id, 
                    p.name, 
                    p.description, 
                    p.price, 
                    p.stock_quantity, 
                    p.image, 
                    p.volume, 
                    p.concentration, 
                    p.gender,
                    c.name as category_name,
                    b.name as brand_name
                FROM 
                    ' . $this->table . ' p
                LEFT JOIN
                    categories c ON p.category_id = c.category_id
                LEFT JOIN
                    brands b ON p.brand_id = b.brand_id
                WHERE 1=1';

        // Ajouter les conditions de recherche
        $params = [];

        if ($keyword) {
            $query .= ' AND (p.name LIKE :keyword OR p.description LIKE :keyword)';
            $params[':keyword'] = '%' . $keyword . '%';
        }

        if ($category) {
            $query .= ' AND p.category_id = :category_id';
            $params[':category_id'] = $category;
        }

        if ($brand !== null && is_int($brand) && $brand > 0) {
            $query .= ' AND p.brand_id = :brand_id';
            $params[':brand_id'] = $brand;
        }

        if ($min_price) {
            $query .= ' AND p.price >= :min_price';
            $params[':min_price'] = $min_price;
        }

        if ($max_price) {
            $query .= ' AND p.price <= :max_price';
            $params[':max_price'] = $max_price;
        }

        if ($gender) {
            $query .= ' AND p.gender = :gender';
            $params[':gender'] = $gender;
        }

        $query .= ' ORDER BY p.created_at DESC';

        $stmt = $this->conn->prepare($query);

        // Lier les paramètres
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Récupérer toutes les catégories
     */
    public function getCategories() {
        $query = 'SELECT * FROM categories ORDER BY name';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Récupérer toutes les marques
     */
    public function getBrands() {
        $query = 'SELECT * FROM brands ORDER BY name';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Récupérer une marque par son ID
     * 
     * @param int $brand_id ID de la marque
     * @return array|false Informations sur la marque ou false si non trouvée
     */
    public function getBrandById($brand_id) {
        $query = 'SELECT * FROM brands WHERE brand_id = :brand_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':brand_id', $brand_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupérer tous les produits d'une marque spécifique
     * 
     * @param int $brand_id ID de la marque
     * @return PDOStatement Requête préparée contenant les produits
     */
    public function getProductsByBrand($brand_id) {
        $query = 'SELECT 
                    p.product_id, 
                    p.name, 
                    p.description, 
                    p.price, 
                    p.stock_quantity, 
                    p.image, 
                    p.volume, 
                    p.concentration, 
                    p.gender,
                    c.name as category_name,
                    b.name as brand_name
                FROM 
                    ' . $this->table . ' p
                LEFT JOIN
                    categories c ON p.category_id = c.category_id
                LEFT JOIN
                    brands b ON p.brand_id = b.brand_id
                WHERE 
                    p.brand_id = :brand_id
                ORDER BY
                    p.name ASC';
                    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':brand_id', $brand_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }

    /**
     * Récupère le nombre total de produits
     */
    public function getTotalCount() {
        $query = 'SELECT COUNT(*) as total FROM ' . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Récupère les produits en stock faible
     */
    public function getLowStockProducts() {
        $query = 'SELECT p.*, b.name as brand_name, c.name as category_name 
                 FROM products p 
                 LEFT JOIN brands b ON p.brand_id = b.brand_id 
                 LEFT JOIN categories c ON p.category_id = c.category_id 
                 WHERE p.stock_quantity <= 10 
                 ORDER BY p.stock_quantity ASC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un produit par son ID
     */
    public function getById($product_id) {
        $this->product_id = $product_id;
        return $this->getSingle();
    }
}