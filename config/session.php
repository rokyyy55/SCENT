<?php
/**
 * Configuration des sessions pour Scent
 */

// Charger la configuration de la base de données en premier
require_once __DIR__ . '/database.php';

class SessionManager {
    private $db;
    private $session_table = 'sessions';
    private $session_lifetime = 7200; // 2 heures

    public function __construct() {
        // Initialiser la connexion à la base de données
        $this->db = new Database();
        
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() == PHP_SESSION_NONE) {
            // Configuration des paramètres de session
            ini_set('session.use_strict_mode', 1);
            ini_set('session.use_cookies', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS'])); // Secure si HTTPS
            ini_set('session.cookie_samesite', 'Lax');
            ini_set('session.gc_maxlifetime', $this->session_lifetime);
            
            // Définir le gestionnaire personnalisé
            session_set_save_handler(
                [$this, 'open'],
                [$this, 'close'],
                [$this, 'read'],
                [$this, 'write'],
                [$this, 'destroy'],
                [$this, 'gc']
            );
            
            // Démarrer la session
            session_start();
            
            // Régénérer l'ID de session pour éviter les fixations de session
            if (!isset($_SESSION['created'])) {
                $_SESSION['created'] = time();
                session_regenerate_id(true);
            } else if (time() - $_SESSION['created'] > 1800) { // 30 minutes
                // Régénérer l'ID de session après 30 minutes
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            }
        }
    }

    public function open($save_path, $session_name) {
        return true;
    }

    public function close() {
        return true;
    }

    public function read($id) {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("SELECT data FROM {$this->session_table} WHERE session_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['data'] : '';
    }

    public function write($id, $data) {
        $conn = $this->db->connect();
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        $stmt = $conn->prepare("REPLACE INTO {$this->session_table} 
                              (session_id, user_id, data, ip_address, user_agent) 
                              VALUES (?, ?, ?, ?, ?)");
        
        return $stmt->execute([$id, $user_id, $data, $ip, $user_agent]);
    }

    public function destroy($id) {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("DELETE FROM {$this->session_table} WHERE session_id = ?");
        
        return $stmt->execute([$id]);
    }

    public function gc($maxlifetime) {
        $conn = $this->db->connect();
        $old = time() - $maxlifetime;
        $stmt = $conn->prepare("DELETE FROM {$this->session_table} WHERE last_activity < ?");
        
        return $stmt->execute([date('Y-m-d H:i:s', $old)]);
    }

    /**
     * Définir une valeur de session
     */
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    /**
     * Récupérer une valeur de session
     */
    public function get($key, $default = null) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * Supprimer une valeur de session
     */
    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Vérifier si une clé existe
     */
    public function has($key) {
        return isset($_SESSION[$key]);
    }

    /**
     * Vider toute la session
     */
    public function clear() {
        session_unset();
    }

    /**
     * Détruire complètement la session
     */
    public function destroySession() {
        $this->clear();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Définir un message flash (affiché une seule fois)
     */
    public function setFlash($key, $message) {
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Récupérer un message flash
     */
    public function getFlash($key) {
        $message = isset($_SESSION['flash'][$key]) ? $_SESSION['flash'][$key] : null;
        if (isset($_SESSION['flash'][$key])) {
            unset($_SESSION['flash'][$key]);
        }
        return $message;
    }
}

// Instance globale pour une utilisation facile
$session = new SessionManager();