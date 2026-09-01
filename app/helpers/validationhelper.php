<?php
/**
 * Helper pour la validation des données
 */
class ValidationHelper {
    /**
     * Tableau d'erreurs
     */
    private static $errors = [];
    
    /**
     * Vérifier si une valeur est vide
     *
     * @param mixed $value Valeur à vérifier
     * @param string $field Nom du champ
     * @param string $message Message d'erreur
     * @return bool True si la validation passe, false sinon
     */
    public static function required($value, $field, $message = null) {
        $result = !empty(trim($value));
        
        if (!$result) {
            self::addError($field, $message ?: "Le champ $field est requis.");
        }
        
        return $result;
    }
    
    /**
     * Vérifier la longueur minimale d'une chaîne
     *
     * @param string $value Valeur à vérifier
     * @param string $field Nom du champ
     * @param int $min Longueur minimale
     * @param string $message Message d'erreur
     * @return bool True si la validation passe, false sinon
     */
    public static function minLength($value, $field, $min, $message = null) {
        $result = mb_strlen(trim($value)) >= $min;
        
        if (!$result) {
            self::addError($field, $message ?: "Le champ $field doit contenir au moins $min caractères.");
        }
        
        return $result;
    }
    
    /**
     * Vérifier la longueur maximale d'une chaîne
     *
     * @param string $value Valeur à vérifier
     * @param string $field Nom du champ
     * @param int $max Longueur maximale
     * @param string $message Message d'erreur
     * @return bool True si la validation passe, false sinon
     */
    public static function maxLength($value, $field, $max, $message = null) {
        $result = mb_strlen(trim($value)) <= $max;
        
        if (!$result) {
            self::addError($field, $message ?: "Le champ $field ne doit pas dépasser $max caractères.");
        }
        
        return $result;
    }
    
    /**
     * Vérifier si une valeur est un email valide
     *
     * @param string $value Valeur à vérifier
     * @param string $field Nom du champ
     * @param string $message Message d'erreur
     * @return bool True si la validation passe, false sinon
     */
    public static function email($value, $field, $message = null) {
        $result = filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
        
        if (!$result) {
            self::addError($field, $message ?: "Le champ $field doit être une adresse email valide.");
        }
        
        return $result;
    }
    
    /**
     * Vérifier si une valeur est numérique
     *
     * @param mixed $value Valeur à vérifier
     * @param string $field Nom du champ
     * @param string $message Message d'erreur
     * @return bool True si la validation passe, false sinon
     */
    public static function numeric($value, $field, $message = null) {
        $result = is_numeric($value);
        
        if (!$result) {
            self::addError($field, $message ?: "Le champ $field doit être un nombre.");
        }
        
        return $result;
    }
    
    /**
     * Vérifier si une valeur est un entier
     *
     * @param mixed $value Valeur à vérifier
     * @param string $field Nom du champ
     * @param string $message Message d'erreur
     * @return bool True si la validation passe, false sinon
     */
    public static function integer($value, $field, $message = null) {
        $result = filter_var($value, FILTER_VALIDATE_INT) !== false;
        
        if (!$result) {
            self::addError($field, $message ?: "Le champ $field doit être un nombre entier.");
        }
        
        return $result;
    }
    
    /**
     * Vérifier si une valeur est comprise dans un intervalle
     *
     * @param mixed $value Valeur à vérifier
     * @param string $field Nom du champ
     * @param int $min Valeur minimale
     * @param int $max Valeur maximale
     * @param string $message Message d'erreur
     * @return bool True si la validation passe, false sinon
     */
    public static function range($value, $field, $min, $max, $message = null) {
        $result = $value >= $min && $value <= $max;
        
        if (!$result) {
            self::addError($field, $message ?: "Le champ $field doit être compris entre $min et $max.");
        }
        
        return $result;
    }
    
    /**
     * Vérifier si une valeur correspond à une expression régulière
     *
     * @param string $value Valeur à vérifier
     * @param string $field Nom du champ
     * @param string $pattern Expression régulière
     * @param string $message Message d'erreur
     * @return bool True si la validation passe, false sinon
     */
    public static function regex($value, $field, $pattern, $message = null) {
        $result = preg_match($pattern, $value) === 1;
        
        if (!$result) {
            self::addError($field, $message ?: "Le champ $field n'est pas valide.");
        }
        
        return $result;
    }
    
    /**
     * Vérifier si une valeur est égale à une autre
     *
     * @param mixed $value Valeur à vérifier
     * @param mixed $compare Valeur de comparaison
     * @param string $field Nom du champ
     * @param string $message Message d'erreur
     * @return bool True si la validation passe, false sinon
     */
    public static function equals($value, $compare, $field, $message = null) {
        $result = $value === $compare;
        
        if (!$result) {
            self::addError($field, $message ?: "Le champ $field n'est pas valide.");
        }
        
        return $result;
    }
    
    /**
     * Vérifier si une valeur est dans un tableau de valeurs
     *
     * @param mixed $value Valeur à vérifier
     * @param array $allowed Valeurs autorisées
     * @param string $field Nom du champ
     * @param string $message Message d'erreur
     * @return bool True si la validation passe, false sinon
     */
    public static function in($value, $allowed, $field, $message = null) {
        $result = in_array($value, $allowed);
        
        if (!$result) {
            self::addError($field, $message ?: "La valeur du champ $field n'est pas valide.");
        }
        
        return $result;
    }
    
    /**
     * Vérifier si un fichier a été correctement uploadé
     *
     * @param array $file Tableau $_FILES
     * @param string $field Nom du champ
     * @param string $message Message d'erreur
     * @return bool True si la validation passe, false sinon
     */
    public static function uploadedFile($file, $field, $message = null) {
        $result = isset($file['error']) && $file['error'] === UPLOAD_ERR_OK;
        
        if (!$result) {
            self::addError($field, $message ?: "L'upload du fichier a échoué.");
        }
        
        return $result;
    }
    
    /**
     * Vérifier le type MIME d'un fichier
     *
     * @param array $file Tableau $_FILES
     * @param array $mimeTypes Types MIME autorisés
     * @param string $field Nom du champ
     * @param string $message Message d'erreur
     * @return bool True si la validation passe, false sinon
     */
    public static function mimeType($file, $mimeTypes, $field, $message = null) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        
        $result = in_array($mime, $mimeTypes);
        
        if (!$result) {
            self::addError($field, $message ?: "Le type de fichier n'est pas autorisé.");
        }
        
        return $result;
    }
    
    /**
     * Vérifier la taille d'un fichier
     *
     * @param array $file Tableau $_FILES
     * @param int $maxSize Taille maximale en octets
     * @param string $field Nom du champ
     * @param string $message Message d'erreur
     * @return bool True si la validation passe, false sinon
     */
    public static function fileSize($file, $maxSize, $field, $message = null) {
        $result = $file['size'] <= $maxSize;
        
        if (!$result) {
            $size = self::formatSize($maxSize);
            self::addError($field, $message ?: "Le fichier ne doit pas dépasser $size.");
        }
        
        return $result;
    }
    
    /**
     * Formater une taille en octets en une chaîne lisible
     *
     * @param int $size Taille en octets
     * @return string Taille formatée
     */
    private static function formatSize($size) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }
    
    /**
     * Ajouter une erreur
     *
     * @param string $field Nom du champ
     * @param string $message Message d'erreur
     */
    private static function addError($field, $message) {
        self::$errors[$field] = $message;
    }
    
    /**
     * Récupérer toutes les erreurs
     *
     * @return array Tableau d'erreurs
     */
    public static function getErrors() {
        return self::$errors;
    }
    
    /**
     * Récupérer les erreurs d'un champ
     *
     * @param string $field Nom du champ
     * @return string|null Message d'erreur ou null si pas d'erreur
     */
    public static function getError($field) {
        return isset(self::$errors[$field]) ? self::$errors[$field] : null;
    }
    
    /**
     * Vérifier s'il y a des erreurs
     *
     * @return bool True s'il y a des erreurs, false sinon
     */
    public static function hasErrors() {
        return !empty(self::$errors);
    }
    
    /**
     * Réinitialiser les erreurs
     */
    public static function resetErrors() {
        self::$errors = [];
    }
    
    /**
     * Nettoyer une valeur pour éviter les injections XSS
     *
     * @param mixed $value Valeur à nettoyer
     * @return mixed Valeur nettoyée
     */
    public static function sanitize($value) {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = self::sanitize($val);
            }
        } else {
            $value = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
        
        return $value;
    }
}