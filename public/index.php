<?php
/**
 * Point d'entrée principal de l'application Scent
 */

// Charger les configurations
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

// Charger les helpers
require_once __DIR__ . '/../app/helpers/CookieHelper.php';
require_once __DIR__ . '/../app/helpers/ValidationHelper.php';

// Définir les constantes de base
define('BASE_URL', '/scent');
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Fonction simple de routage
function route($uri) {
    // Supprimer le message de débogage en production
    // echo "<p>URI à router : " . $uri . "</p>";
    
    // Vérifier si la requête concerne un fichier statique
    if (preg_match('/\.(jpg|jpeg|png|gif|css|js)$/i', $uri)) {
        // C'est un fichier statique, laisser le serveur web le gérer
        return;
    }
    
    // Supprimer les paramètres de requête
    $uri = strtok($uri, '?');
    
    // Supprimer le chemin de base de l'URL si nécessaire
    $uri = str_replace(BASE_URL, '', $uri);
    
    // Supprimer les slashes de début et de fin
    $uri = trim($uri, '/');
    
    // Si l'URI est vide, charger la page d'accueil
    if (empty($uri)) {
        include APP_PATH . '/controllers/ProductController.php';
        $controller = new ProductController();
        $controller->index();
        return;
    }
    
    // Diviser l'URI en segments
    $segments = explode('/', $uri);
    $controller = ucfirst($segments[0]) . 'Controller';
    
    // If /admin/products/get, call AdminController->getProductAjax()
    if (
        isset($segments[0]) && strtolower($segments[0]) === 'admin' &&
        isset($segments[1]) && strtolower($segments[1]) === 'products' &&
        isset($segments[2]) && strtolower($segments[2]) === 'get'
    ) {
        $controller_path = APP_PATH . '/controllers/' . $controller . '.php';
        if (file_exists($controller_path)) {
            include $controller_path;
            if (class_exists($controller)) {
                $controller_instance = new $controller();
                $controller_instance->getProductAjax();
                return;
            }
        }
    }

    // If /admin/products/update and POST, call AdminController->updateProductAjax($_POST)
    if (
        isset($segments[0]) && strtolower($segments[0]) === 'admin' &&
        isset($segments[1]) && strtolower($segments[1]) === 'products' &&
        isset($segments[2]) && strtolower($segments[2]) === 'update' &&
        $_SERVER['REQUEST_METHOD'] === 'POST'
    ) {
        $controller_path = APP_PATH . '/controllers/' . $controller . '.php';
        if (file_exists($controller_path)) {
            include $controller_path;
            if (class_exists($controller)) {
                $controller_instance = new $controller();
                $controller_instance->updateProductAjax($_POST);
                return;
            }
        }
    }

    // If /admin/products/delete/357, call AdminController->delete(357) or delete(357, $_POST) for POST
    if (
        isset($segments[0]) && strtolower($segments[0]) === 'admin' &&
        isset($segments[1]) && strtolower($segments[1]) === 'products' &&
        isset($segments[2]) && strtolower($segments[2]) === 'delete' &&
        isset($segments[3]) && is_numeric($segments[3])
    ) {
        $controller_path = APP_PATH . '/controllers/' . $controller . '.php';
        if (file_exists($controller_path)) {
            include $controller_path;
            if (class_exists($controller)) {
                $controller_instance = new $controller();
                // Pass POST data if it's a POST request
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller_instance->delete($segments[3], $_POST);
                } else {
                    $controller_instance->delete($segments[3]);
                }
                return;
            }
        }
    }

    $method = isset($segments[1]) ? $segments[1] : 'index';
    $params = array_slice($segments, 2);
    
    // Vérifier si le contrôleur existe
    $controller_path = APP_PATH . '/controllers/' . $controller . '.php';
    if (file_exists($controller_path)) {
        include $controller_path;
        
        if (class_exists($controller)) {
            $controller_instance = new $controller();
            
            if (method_exists($controller_instance, $method)) {
                // If it's a POST request, pass POST data as the last parameter
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $params[] = $_POST;
                }
                call_user_func_array([$controller_instance, $method], $params);
            } else {
                // Méthode introuvable
                include APP_PATH . '/views/templates/header.php';
                include APP_PATH . '/views/errors/404.php';
                include APP_PATH . '/views/templates/footer.php';
            }
        } else {
            // Classe introuvable
            include APP_PATH . '/views/templates/header.php';
            include APP_PATH . '/views/errors/404.php';
            include APP_PATH . '/views/templates/footer.php';
        }
    } else {
        // Contrôleur introuvable
        include APP_PATH . '/views/templates/header.php';
        include APP_PATH . '/views/errors/404.php';
        include APP_PATH . '/views/templates/footer.php';
    }
}

// Gérer les requêtes
$request_uri = $_SERVER['REQUEST_URI'];
route($request_uri);