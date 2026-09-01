<?php
/**
 * Helper pour la gestion des cookies
 */
class CookieHelper {
    /**
     * Définir un cookie sécurisé
     *
     * @param string $name Nom du cookie
     * @param string $value Valeur du cookie
     * @param int $expire Durée de vie en secondes (par défaut 30 jours)
     * @param bool $httpOnly Accès uniquement via HTTP (par défaut true)
     * @param string $sameSite Politique SameSite (par défaut Lax)
     */
    public static function set($name, $value, $expire = 2592000, $httpOnly = true, $sameSite = 'Lax') {
        $secure = isset($_SERVER['HTTPS']);
        $path = '/';
        
        // Calculer la date d'expiration
        $expiration = time() + $expire;
        
        // Définir les options du cookie
        $options = [
            'expires' => $expiration,
            'path' => $path,
            'domain' => '',
            'secure' => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite
        ];
        
        // Définir le cookie
        setcookie($name, $value, $options);
    }
    
    /**
     * Récupérer la valeur d'un cookie
     *
     * @param string $name Nom du cookie
     * @param mixed $default Valeur par défaut si le cookie n'existe pas
     * @return mixed Valeur du cookie ou valeur par défaut
     */
    public static function get($name, $default = null) {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
    }
    
    /**
     * Vérifier si un cookie existe
     *
     * @param string $name Nom du cookie
     * @return bool True si le cookie existe, false sinon
     */
    public static function has($name) {
        return isset($_COOKIE[$name]);
    }
    
    /**
     * Supprimer un cookie
     *
     * @param string $name Nom du cookie
     */
    public static function delete($name) {
        if (self::has($name)) {
            // Pour supprimer un cookie, on le définit avec une date d'expiration dans le passé
            setcookie($name, '', time() - 3600, '/');
            unset($_COOKIE[$name]);
        }
    }
    
    /**
     * Générer un token CSRF
     *
     * @return string Token CSRF
     */
    public static function generateCsrfToken() {
        $token = bin2hex(random_bytes(32));
        self::set('csrf_token', $token, 7200); // 2 heures
        return $token;
    }
    
    /**
     * Vérifier un token CSRF
     *
     * @param string $token Token à vérifier
     * @return bool True si le token est valide, false sinon
     */
    public static function verifyCsrfToken($token) {
        return self::has('csrf_token') && hash_equals(self::get('csrf_token'), $token);
    }
}