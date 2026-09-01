<?php
/**
 * Contrôleur pour gérer les produits (parfums)
 */
class ProductController {
    private $product_model;
    
    public function __construct() {
        // Charger le modèle Product
        require_once APP_PATH . '/models/Product.php';
        $this->product_model = new Product();
    }
    
    /**
     * Afficher la page d'accueil avec les produits
     */
    public function index() {
        // Récupérer les produits
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12; // 12 produits par page
        $offset = ($page - 1) * $limit;
        
        $products = $this->product_model->getAll($limit, $offset);
        
        // Récupérer les catégories et marques pour les filtres
        $categories_stmt = $this->product_model->getCategories();
        $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $brands_stmt = $this->product_model->getBrands();
        $brands = $brands_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Charger la vue
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/products/index.php';
        include APP_PATH . '/views/templates/footer.php';
    }
    
    /**
     * Afficher les détails d'un produit
     */
    public function details($id) {
        // Récupérer le produit
        $this->product_model->product_id = $id;
        $exists = $this->product_model->getSingle();
        
        if (!$exists) {
            // Produit non trouvé, rediriger vers la page d'accueil
            header('Location: ' . BASE_URL . '/product');
            exit;
        }
        
        // Calculate Algerian price
        $conversion_rate = 160; // Example conversion rate: 1 Euro = 160 DZD
        $price_dz = $this->product_model->price * $conversion_rate;
        
        // Pass price_dz to the view by defining a variable
        $GLOBALS['price_dz'] = $price_dz;
        
        // Charger la vue
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/products/details.php';
        include APP_PATH . '/views/templates/footer.php';
    }
    
    /**
     * Afficher tous les produits d'une marque spécifique
     */
    public function brand($brand_id) {
        // Récupérer les informations de la marque
        $brand_info = $this->product_model->getBrandById($brand_id);
        
        if (!$brand_info) {
            // Marque non trouvée, rediriger vers la page d'accueil
            header('Location: ' . BASE_URL . '/product');
            exit;
        }
        
        // Récupérer tous les produits de cette marque
        $stmt = $this->product_model->getProductsByBrand($brand_id);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Récupérer les catégories et marques pour les filtres latéraux si nécessaire
        $categories_stmt = $this->product_model->getCategories();
        $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $brands_stmt = $this->product_model->getBrands();
        $brands = $brands_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Charger la vue
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/products/brand.php';
        include APP_PATH . '/views/templates/footer.php';
    }
    
    /**
     * Rechercher des produits
     */
    public function search() {
        // Récupérer les paramètres de recherche
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;
        $category = isset($_GET['category']) ? (int)$_GET['category'] : null;
        $brand = null;
        if (isset($_GET['brand']) && is_numeric($_GET['brand']) && (int)$_GET['brand'] > 0) {
            $brand = (int)$_GET['brand'];
        }
        $min_price = null;
        $max_price = null;
        if (isset($_GET['price_range']) && is_numeric($_GET['price_range'])) {
            $max_price = (float)$_GET['price_range'];
        }
        $gender = isset($_GET['gender']) ? $_GET['gender'] : null;
        
        // Effectuer la recherche
        $stmt = $this->product_model->search($keyword, $category, $brand, $min_price, $max_price, $gender);
        $products = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        // Récupérer les catégories et marques pour les filtres
        $categories_stmt = $this->product_model->getCategories();
        $categories = $categories_stmt->fetchAll(PDO::FETCH_OBJ);
        
        $brands_stmt = $this->product_model->getBrands();
        $brands = $brands_stmt->fetchAll(PDO::FETCH_OBJ);
        
        // Créer un tableau de données pour la vue
        $data = [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands
        ];
        
        // Charger la vue
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/products/search.php';
        include APP_PATH . '/views/templates/footer.php';
    }
    
    /**
     * Afficher le formulaire d'ajout de produit (admin)
     */
    public function add() {
        // Vérifier si l'utilisateur est connecté et est un administrateur
        global $session;
        
        if (!$session->has('user_id') || !$session->has('is_admin') || !$session->get('is_admin')) {
            // Rediriger vers la page de connexion
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }
        
        // Récupérer les catégories et marques pour le formulaire
        $categories_stmt = $this->product_model->getCategories();
        $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $brands_stmt = $this->product_model->getBrands();
        $brands = $brands_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Charger la vue
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/admin/add_product.php';
        include APP_PATH . '/views/templates/footer.php';
    }
    
    /**
     * Traiter l'ajout d'un produit (admin)
     */
    public function create() {
        // Vérifier si l'utilisateur est connecté et est un administrateur
        global $session;
        
        if (!$session->has('user_id') || !$session->has('is_admin') || !$session->get('is_admin')) {
            // Rediriger vers la page de connexion
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/product/add');
            exit;
        }
        
        // Récupérer les données du formulaire
        $this->product_model->name = $_POST['name'];
        $this->product_model->description = $_POST['description'];
        $this->product_model->price = $_POST['price'];
        $this->product_model->stock_quantity = $_POST['stock_quantity'];
        $this->product_model->category_id = $_POST['category_id'];
        $this->product_model->brand_id = $_POST['brand_id'];
        $this->product_model->volume = $_POST['volume'];
        $this->product_model->concentration = $_POST['concentration'];
        $this->product_model->gender = $_POST['gender'];
        
        // Gérer l'upload de l'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = PUBLIC_PATH . '/images/products/';
            $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
            $upload_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $this->product_model->image = 'images/products/' . $file_name;
            }
        }
        
        // Créer le produit
        if ($this->product_model->create()) {
            // Succès, rediriger vers la liste des produits admin
            $session->setFlash('success', 'Produit ajouté avec succès.');
            header('Location: ' . BASE_URL . '/admin/products');
        } else {
            // Erreur, rediriger vers le formulaire avec message d'erreur
            $session->setFlash('error', 'Erreur lors de l\'ajout du produit.');
            header('Location: ' . BASE_URL . '/product/add');
        }
        
        exit;
    }
    
    /**
     * Afficher le formulaire de modification de produit (admin)
     */
    public function edit($id) {
        // Vérifier si l'utilisateur est connecté et est un administrateur
        global $session;
        
        if (!$session->has('user_id') || !$session->has('is_admin') || !$session->get('is_admin')) {
            // Rediriger vers la page de connexion
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }
        
        // Récupérer le produit
        $this->product_model->product_id = $id;
        $exists = $this->product_model->getSingle();
        
        if (!$exists) {
            // Produit non trouvé, rediriger vers la liste des produits admin
            $session->setFlash('error', 'Produit non trouvé.');
            header('Location: ' . BASE_URL . '/admin/products');
            exit;
        }
        
        // Récupérer les catégories et marques pour le formulaire
        $categories_stmt = $this->product_model->getCategories();
        $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $brands_stmt = $this->product_model->getBrands();
        $brands = $brands_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Charger la vue
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/admin/edit_product.php';
        include APP_PATH . '/views/templates/footer.php';
    }
    
    /**
     * Traiter la modification d'un produit (admin)
     */
    public function update($id) {
        // Vérifier si l'utilisateur est connecté et est un administrateur
        global $session;
        
        if (!$session->has('user_id') || !$session->has('is_admin') || !$session->get('is_admin')) {
            // Rediriger vers la page de connexion
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/product/edit/' . $id);
            exit;
        }
        
        // Récupérer le produit existant
        $this->product_model->product_id = $id;
        $exists = $this->product_model->getSingle();
        
        if (!$exists) {
            // Produit non trouvé, rediriger vers la liste des produits admin
            $session->setFlash('error', 'Produit non trouvé.');
            header('Location: ' . BASE_URL . '/admin/products');
            exit;
        }
        
        // Récupérer les données du formulaire
        $this->product_model->name = $_POST['name'];
        $this->product_model->description = $_POST['description'];
        $this->product_model->price = $_POST['price'];
        $this->product_model->stock_quantity = $_POST['stock_quantity'];
        $this->product_model->category_id = $_POST['category_id'];
        $this->product_model->brand_id = $_POST['brand_id'];
        $this->product_model->volume = $_POST['volume'];
        $this->product_model->concentration = $_POST['concentration'];
        $this->product_model->gender = $_POST['gender'];
        
        // Gérer l'upload de l'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = PUBLIC_PATH . '/images/products/';
            $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
            $upload_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Supprimer l'ancienne image si elle existe
                if ($this->product_model->image && file_exists(PUBLIC_PATH . '/' . $this->product_model->image)) {
                    unlink(PUBLIC_PATH . '/' . $this->product_model->image);
                }
                
                $this->product_model->image = 'images/products/' . $file_name;
            }
        }
        
        // Mettre à jour le produit
        if ($this->product_model->update()) {
            // Succès, rediriger vers la liste des produits admin
            $session->setFlash('success', 'Produit mis à jour avec succès.');
            header('Location: ' . BASE_URL . '/admin/products');
        } else {
            // Erreur, rediriger vers le formulaire avec message d'erreur
            $session->setFlash('error', 'Erreur lors de la mise à jour du produit.');
            header('Location: ' . BASE_URL . '/product/edit/' . $id);
        }
        
        exit;
    }
    
    /**
     * Supprimer un produit (admin)
     */
    public function delete($id) {
        // Vérifier si l'utilisateur est connecté et est un administrateur
        global $session;
        
        if (!$session->has('user_id') || !$session->has('is_admin') || !$session->get('is_admin')) {
            // Rediriger vers la page de connexion
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }
        
        // Récupérer le produit
        $this->product_model->product_id = $id;
        $exists = $this->product_model->getSingle();
        
        if (!$exists) {
            // Produit non trouvé, rediriger vers la liste des produits admin
            $session->setFlash('error', 'Produit non trouvé.');
            header('Location: ' . BASE_URL . '/admin/products');
            exit;
        }
        
        // Supprimer le produit
        if ($this->product_model->delete()) {
            // Supprimer l'image si elle existe
            if ($this->product_model->image && file_exists(PUBLIC_PATH . '/' . $this->product_model->image)) {
                unlink(PUBLIC_PATH . '/' . $this->product_model->image);
            }
            
            // Succès, rediriger vers la liste des produits admin
            $session->setFlash('success', 'Produit supprimé avec succès.');
        } else {
            // Erreur, rediriger vers la liste des produits admin avec message d'erreur
            $session->setFlash('error', 'Erreur lors de la suppression du produit.');
        }
        
        header('Location: ' . BASE_URL . '/admin/products');
        exit;
    }

    /**
     * Handle product rating submission
     */
    public function rate($product_id) {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour noter un produit.";
            header('Location: ' . BASE_URL . '/product/details/' . $product_id);
            exit;
        }

        // Validate input
        if (!isset($_POST['rating']) || !is_numeric($_POST['rating']) || $_POST['rating'] < 1 || $_POST['rating'] > 5) {
            $_SESSION['error'] = "Veuillez sélectionner une note valide.";
            header('Location: ' . BASE_URL . '/product/details/' . $product_id);
            exit;
        }

        $rating = (int)$_POST['rating'];
        $review = isset($_POST['review']) ? trim($_POST['review']) : '';
        $user_id = $_SESSION['user_id'];

        // Get database connection
        $database = new Database();
        $conn = $database->connect();

        // Check if user has already rated this product
        $query = 'SELECT rating_id FROM ratings WHERE user_id = :user_id AND product_id = :product_id';
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Update existing rating
            $query = 'UPDATE ratings SET rating = :rating, review = :review WHERE user_id = :user_id AND product_id = :product_id';
        } else {
            // Insert new rating
            $query = 'INSERT INTO ratings (user_id, product_id, rating, review) VALUES (:user_id, :product_id, :rating, :review)';
        }

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':review', $review);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Merci pour votre avis !";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de l'enregistrement de votre avis.";
        }

        header('Location: ' . BASE_URL . '/product/details/' . $product_id);
        exit;
    }
}