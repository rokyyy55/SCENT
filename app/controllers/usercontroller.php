<?php
/**
 * Contrôleur pour gérer les utilisateurs
 */
class UserController {
    private $user_model;
    // Ajout de la constante pour le nombre de commandes récentes
    private const MAX_RECENT_ORDERS = 5;
    
    public function __construct() {
        // Charger le modèle User
        if (file_exists(APP_PATH . '/models/User.php')) {
            require_once APP_PATH . '/models/User.php';
        } else {
            throw new Exception('Le fichier User.php est introuvable');
        }
        
        $this->user_model = new User();
    }
    
    /**
     * Affiche le formulaire de connexion
     */
    public function login() {
        global $session;
        
        // Rediriger si l'utilisateur est déjà connecté
        if ($session->has('user_id')) {
            header('Location: ' . BASE_URL);
            exit;
        }
        
        // Afficher la vue
        $this->renderView('user/login');
    }
    
    /**
 * Affiche le tableau de bord d'administration
 */
public function dashboard() {
    global $session;
    
    // Vérifier que l'utilisateur est connecté et administrateur
    if (!$session->has('user_id') || $session->get('is_admin') !== true) {
        header('Location: ' . BASE_URL);
        exit;
    }
    
    // Ici, chargez les données nécessaires pour le tableau de bord
    
    // Afficher la vue du tableau de bord
    $this->renderView('admin/dashboard');
}
    /**
     * Traitement de l'authentification
     */
    public function authenticate() {
        global $session;
        
        // Rediriger si l'utilisateur est déjà connecté
        if ($session->has('user_id')) {
            header('Location: ' . BASE_URL);
            exit;
        }
        
        // Vérifier la méthode de requête
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }
        
        // Récupérer et nettoyer les données du formulaire
        $username_or_email = trim($_POST['username_or_email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember_me = isset($_POST['remember_me']);
        
        // Vérification spéciale pour admin
        $is_special_admin = ($username_or_email === 'hana' && $password === '123456');
        
        // Tentative d'authentification
        if ($this->user_model->login($username_or_email, $password) || $is_special_admin) {
            // Si c'est l'admin spécial, définir les informations manuellement
            if ($is_special_admin) {
                // Vérifier si l'utilisateur existe déjà
                if (!$this->user_model->getUserByUsername('hana')) {
                    // Créer l'utilisateur s'il n'existe pas
                    $this->user_model->username = 'hana';
                    $this->user_model->password = password_hash('123456', PASSWORD_DEFAULT);
                    $this->user_model->email = 'admin@example.com';
                    $this->user_model->is_admin = true;
                    $this->user_model->register();
                    
                    // Récupérer l'utilisateur nouvellement créé
                    $this->user_model->getUserByUsername('hana');
                }
            }
            
            // Définir les variables de session
            $session->set('user_id', $this->user_model->user_id);
            $session->set('username', $this->user_model->username);
            
            // Pour l'admin spécial, forcer la valeur à true
            if ($is_special_admin) {
                $session->set('is_admin', true);
            } else {
                // Sinon, utiliser la valeur de la base de données
                $session->set('is_admin', (bool)$this->user_model->is_admin);
            }
            
            // Gérer la fonctionnalité "Se souvenir de moi"
            if ($remember_me) {
                $this->setRememberMeToken();
            }
            
            // Gérer le panier de l'utilisateur
            $this->handleUserCart();
            
            // Message de succès
            $session->setFlash('success', 'Connexion réussie.');
            
            // Déterminer la redirection en fonction du rôle
            if ($session->get('is_admin') === true) {
                // Rediriger les administrateurs vers la page d'accueil
                header('Location: ' . BASE_URL);
            } else {
                // Rediriger les utilisateurs normaux vers la page d'origine ou l'accueil
                $redirect_url = $session->has('redirect_after_login') 
                    ? $session->get('redirect_after_login') 
                    : BASE_URL;
                $session->remove('redirect_after_login');
                
                header('Location: ' . $redirect_url);
            }
        } else {
            // Message d'erreur et redirection
            $session->setFlash('error', 'Identifiants incorrects. Veuillez réessayer.');
            header('Location: ' . BASE_URL . '/user/login');
        }
        
        exit;
    }
    /**
     * Affiche le formulaire d'inscription
     */
    public function register() {
        global $session;
        
        // Rediriger si l'utilisateur est déjà connecté
        if ($session->has('user_id')) {
            header('Location: ' . BASE_URL);
            exit;
        }
        
        // Afficher la vue
        $this->renderView('user/register');
    }
    
    /**
     * Traitement de l'inscription
     */
    public function create() {
        global $session;
        
        // Rediriger si l'utilisateur est déjà connecté
        if ($session->has('user_id')) {
            header('Location: ' . BASE_URL);
            exit;
        }
        
        // Vérifier la méthode de requête
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/user/register');
            exit;
        }
        
        // Récupérer et nettoyer les données du formulaire
        $this->user_model->username = trim($_POST['username'] ?? '');
        $this->user_model->email = trim($_POST['email'] ?? '');
        $this->user_model->password = $_POST['password'] ?? '';
        $this->user_model->first_name = trim($_POST['first_name'] ?? '');
        $this->user_model->last_name = trim($_POST['last_name'] ?? '');
        
        // Valider les données
        $errors = $this->validateRegistrationData($_POST);
        
        // S'il y a des erreurs, rediriger vers le formulaire avec les erreurs
        if (!empty($errors)) {
            $session->set('register_errors', $errors);
            $session->set('register_form_data', [
                'username' => $this->user_model->username,
                'email' => $this->user_model->email,
                'first_name' => $this->user_model->first_name,
                'last_name' => $this->user_model->last_name
            ]);
            header('Location: ' . BASE_URL . '/user/register');
            exit;
        }
        
        // Tentative d'inscription
        if ($this->user_model->register()) {
            $session->setFlash('success', 'Inscription réussie. Vous pouvez maintenant vous connecter.');
            header('Location: ' . BASE_URL . '/user/login');
        } else {
            $session->setFlash('error', 'Erreur lors de l\'inscription. Ce nom d\'utilisateur ou cet email est peut-être déjà utilisé.');
            header('Location: ' . BASE_URL . '/user/register');
        }
        
        exit;
    }
    
    /**
     * Déconnexion de l'utilisateur
     */
    public function logout() {
        global $session;
        
        // Supprimer les variables de session
        $session->remove('user_id');
        $session->remove('username');
        $session->remove('is_admin');
        
        // Supprimer le cookie "Se souvenir de moi"
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', isset($_SERVER['HTTPS']), true);
        }
        
        // Détruire la session
        $session->destroySession();
        
        // Rediriger vers la page d'accueil
        header('Location: ' . BASE_URL);
        exit;
    }
    
    /**
     * Affiche le profil de l'utilisateur
     */
    public function profile() {
        global $session;
        
        // Vérifier que l'utilisateur est connecté
        if (!$session->has('user_id')) {
            $this->redirectToLogin('/user/profile');
            exit;
        }
        
        // Récupérer les données de l'utilisateur
        $this->user_model->user_id = $session->get('user_id');
        $this->user_model->getUser();
        
        // Récupérer les commandes récentes de l'utilisateur
        require_once APP_PATH . '/models/Order.php';
        $order_model = new Order();
        $orders_result = $order_model->getByUser($this->user_model->user_id);
        $orders = [];
        if (is_array($orders_result) && count($orders_result) > 0) {
            foreach ($orders_result[0] as $order) {
                $orders[] = (object)$order;
            }
        }
        
        // Afficher la vue
        $this->renderView('user/profile', [
            'user' => $this->user_model,
            'orders' => $orders
        ]);
    }
    
    /**
     * Affiche le formulaire de modification du profil
     */
    public function edit() {
        global $session;
        
        // Vérifier que l'utilisateur est connecté
        if (!$session->has('user_id')) {
            $this->redirectToLogin('/user/edit');
            exit;
        }
        
        // Récupérer les données de l'utilisateur
        $this->user_model->user_id = $session->get('user_id');
        $this->user_model->getUser();
        
        // Afficher la vue
        $this->renderView('user/edit', [
            'user' => $this->user_model
        ]);
    }
    
    /**
     * Traitement de la modification du profil
     */
    public function update() {
        global $session;
        
        // Vérifier que l'utilisateur est connecté
        if (!$session->has('user_id')) {
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }
        
        // Vérifier la méthode de requête
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/user/edit');
            exit;
        }
        
        // Récupérer les données de l'utilisateur
        $this->user_model->user_id = $session->get('user_id');
        $this->user_model->getUser();
        
        // Récupérer et nettoyer les données du formulaire
        $this->user_model->first_name = trim($_POST['first_name'] ?? '');
        $this->user_model->last_name = trim($_POST['last_name'] ?? '');
        $this->user_model->address = trim($_POST['address'] ?? '');
        $this->user_model->city = trim($_POST['city'] ?? '');
        $this->user_model->postal_code = trim($_POST['postal_code'] ?? '');
        $this->user_model->country = trim($_POST['country'] ?? '');
        $this->user_model->phone = trim($_POST['phone'] ?? '');
        
        // Mettre à jour le profil
        if ($this->user_model->updateProfile()) {
            $session->setFlash('success', 'Profil mis à jour avec succès.');
            header('Location: ' . BASE_URL . '/user/profile');
        } else {
            $session->setFlash('error', 'Erreur lors de la mise à jour du profil.');
            header('Location: ' . BASE_URL . '/user/edit');
        }
        
        exit;
    }
    
    /**
     * Affiche le formulaire de changement de mot de passe
     */
    public function password() {
        global $session;
        
        // Vérifier que l'utilisateur est connecté
        if (!$session->has('user_id')) {
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }
        
        // Afficher la vue
        $this->renderView('user/password');
    }
    
    /**
     * Traitement du changement de mot de passe
     */
    public function changePassword() {
        global $session;
        
        // Vérifier que l'utilisateur est connecté
        if (!$session->has('user_id')) {
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }
        
        // Vérifier la méthode de requête
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/user/password');
            exit;
        }
        
        // Récupérer les données du formulaire
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Définir l'ID de l'utilisateur
        $this->user_model->user_id = $session->get('user_id');
        
        // Valider les données
        if ($new_password !== $confirm_password) {
            $session->setFlash('error', 'Les nouveaux mots de passe ne correspondent pas.');
            header('Location: ' . BASE_URL . '/user/password');
            exit;
        }
        
        if (strlen($new_password) < 6) {
            $session->setFlash('error', 'Le nouveau mot de passe doit contenir au moins 6 caractères.');
            header('Location: ' . BASE_URL . '/user/password');
            exit;
        }
        
        // Vérifier le mot de passe actuel et le changer
        if ($this->user_model->login($session->get('username'), $current_password)) {
            if ($this->user_model->changePassword($new_password)) {
                $session->setFlash('success', 'Mot de passe changé avec succès.');
                header('Location: ' . BASE_URL . '/user/profile');
            } else {
                $session->setFlash('error', 'Erreur lors du changement de mot de passe.');
                header('Location: ' . BASE_URL . '/user/password');
            }
        } else {
            $session->setFlash('error', 'Mot de passe actuel incorrect.');
            header('Location: ' . BASE_URL . '/user/password');
        }
        
        exit;
    }
    
    /**
     * Valide les données d'inscription
     * 
     * @param array $data Données du formulaire
     * @return array Tableau d'erreurs
     */
    private function validateRegistrationData($data) {
        $errors = [];
        
        // Valider le nom d'utilisateur
        if (empty($this->user_model->username)) {
            $errors[] = 'Le nom d\'utilisateur est requis.';
        } elseif (strlen($this->user_model->username) < 3) {
            $errors[] = 'Le nom d\'utilisateur doit contenir au moins 3 caractères.';
        }
        
        // Valider l'email
        if (empty($this->user_model->email)) {
            $errors[] = 'L\'email est requis.';
        } elseif (!filter_var($this->user_model->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'L\'email n\'est pas valide.';
        }
        
        // Valider le mot de passe
        if (empty($this->user_model->password)) {
            $errors[] = 'Le mot de passe est requis.';
        } elseif (strlen($this->user_model->password) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
        }
        
        // Vérifier que les mots de passe correspondent
        if ($data['password'] !== $data['confirm_password']) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }
        
        return $errors;
    }
    
    /**
     * Définit un token pour la fonctionnalité "Se souvenir de moi"
     */
    private function setRememberMeToken() {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + (30 * 24 * 60 * 60); // 30 jours
        setcookie('remember_token', $token, $expiry, '/', '', isset($_SERVER['HTTPS']), true);
        
        // TODO: Enregistrer le token dans la base de données pour la vérification ultérieure
    }
    
    /**
     * Gère le panier de l'utilisateur après connexion
     */
    private function handleUserCart() {
        global $session;
        
        if ($session->has('cart_id')) {
            require_once APP_PATH . '/models/Cart.php';
            $cart = new Cart();
            
            if ($cart->getByUser($this->user_model->user_id)) {
                // Fusionner le panier de session avec le panier utilisateur existant
                $cart->merge($session->get('cart_id'));
            } else {
                // Assigner le panier de session à l'utilisateur
                $cart->getById($session->get('cart_id'));
                $cart->assignToUser($this->user_model->user_id);
            }
        }
    }
    
    /**
     * Redirige vers la page de connexion avec une URL de redirection
     * 
     * @param string $redirect_url URL de redirection après connexion
     */
    private function redirectToLogin($redirect_url) {
        global $session;
        $session->set('redirect_after_login', BASE_URL . $redirect_url);
        header('Location: ' . BASE_URL . '/user/login');
        exit;
    }
    
    /**
     * Affiche une vue avec les templates header et footer
     * 
     * @param string $view Nom de la vue
     * @param array $data Données à passer à la vue
     */
    private function renderView($view, $data = []) {
        // Extraire les données pour les rendre disponibles dans la vue
        if (!empty($data)) {
            extract($data);
        }
        
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/' . $view . '.php';
        include APP_PATH . '/views/templates/footer.php';
    }

    public function messages() {
        global $session;
        if (!$session->has('user_id')) {
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }
        $user_id = $session->get('user_id');
        $database = new Database();
        $conn = $database->connect();
        // Get admin user_id (first admin found)
        $stmt = $conn->prepare('SELECT user_id FROM users WHERE is_admin = 1 LIMIT 1');
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        $admin_id = $admin ? $admin['user_id'] : null;
        // Get messages sent by this user
        $stmt = $conn->prepare('SELECT * FROM messages WHERE sender_id = :user_id ORDER BY sent_at DESC');
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $sent_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/user/messages.php';
        include APP_PATH . '/views/templates/footer.php';
    }

    public function sendMessage() {
        global $session;
        if (!$session->has('user_id')) {
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }
        $user_id = $session->get('user_id');
        $database = new Database();
        $conn = $database->connect();
        // Get admin user_id (first admin found)
        $stmt = $conn->prepare('SELECT user_id FROM users WHERE is_admin = 1 LIMIT 1');
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        $admin_id = $admin ? $admin['user_id'] : null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $admin_id) {
            $subject = trim($_POST['subject'] ?? '');
            $body = trim($_POST['body'] ?? '');
            if ($body !== '') {
                $stmt = $conn->prepare('INSERT INTO messages (sender_id, recipient_id, subject, body) VALUES (:sender_id, :recipient_id, :subject, :body)');
                $stmt->bindParam(':sender_id', $user_id);
                $stmt->bindParam(':recipient_id', $admin_id);
                $stmt->bindParam(':subject', $subject);
                $stmt->bindParam(':body', $body);
                $stmt->execute();
            }
        }
        header('Location: ' . BASE_URL . '/user/messages');
        exit;
    }

    public function orders() {
        global $session;
        if (!$session->has('user_id')) {
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }
        require_once APP_PATH . '/models/Order.php';
        $order_model = new Order();
        $orders_result = $order_model->getByUser($session->get('user_id'));
        $orders = [];
        if (is_array($orders_result) && count($orders_result) > 0) {
            foreach ($orders_result[0] as $order) {
                $orders[] = (object)$order;
            }
        }
        include APP_PATH . '/views/user/orders.php';
    }

    public function confirmation($order_id) {
        global $session;
        if (!$session->has('user_id')) {
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }
        require_once APP_PATH . '/models/Order.php';
        $order_model = new Order();
        $order = null;
        $items = [];
        // Get the order for this user
        $orders_result = $order_model->getById($order_id, $session->get('user_id'));
        if ($orders_result) {
            $order = $order_model;
            $items_stmt = $order_model->getItems();
            if ($items_stmt && $items_stmt instanceof PDOStatement) {
                $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $items = [];
            }
        }
        include APP_PATH . '/views/order/confirmation.php';
    }

    public function cancelOrder($order_id) {
        global $session;
        if (!$session->has('user_id')) {
            header('Location: ' . BASE_URL . '/user/login');
            exit;
        }
        require_once APP_PATH . '/models/Order.php';
        $order_model = new Order();
        if ($order_model->getById($order_id, $session->get('user_id'))) {
            $order_model->updateStatus('cancelled');
        }
        header('Location: ' . BASE_URL . '/user/orders');
        exit;
    }
}