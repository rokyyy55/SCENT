<?php
/**
 * Modèle User pour gérer les utilisateurs
 */
class User {
    private $conn;
    private $table = 'users';

    // Propriétés
    public $user_id;
    public $username;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $address;
    public $city;
    public $postal_code;
    public $country;
    public $phone;
    public $is_admin;
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
     * Enregistrer un nouvel utilisateur
     */
    public function register() {
        // Vérifier si l'email ou le nom d'utilisateur existe déjà
        if ($this->emailExists() || $this->usernameExists()) {
            return false;
        }

        $query = 'INSERT INTO ' . $this->table . '
                (username, email, password, first_name, last_name) 
                VALUES
                (:username, :email, :password, :first_name, :last_name)';

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        
        // Hasher le mot de passe
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        // Lier les données
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);

        if ($stmt->execute()) {
            $this->user_id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Connexion d'un utilisateur
     */
    public function login($username_or_email, $password) {
        $query = 'SELECT 
                    user_id, username, email, password, first_name, last_name, is_admin
                FROM 
                    ' . $this->table . ' 
                WHERE 
                    username = :identifier OR email = :identifier
                LIMIT 0,1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':identifier', $username_or_email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Vérifier le mot de passe
            if (password_verify($password, $row['password'])) {
                $this->user_id = $row['user_id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->first_name = $row['first_name'];
                $this->last_name = $row['last_name'];
                $this->is_admin = $row['is_admin'];
                
                return true;
            }
        }

        return false;
    }

    /**
     * Obtenir les informations d'un utilisateur
     */
    public function getUser() {
        $query = 'SELECT 
                    user_id, username, email, first_name, last_name, 
                    address, city, postal_code, country, phone, is_admin
                FROM 
                    ' . $this->table . ' 
                WHERE 
                    user_id = :user_id
                LIMIT 0,1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Définir les propriétés
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->address = $row['address'];
            $this->city = $row['city'];
            $this->postal_code = $row['postal_code'];
            $this->country = $row['country'];
            $this->phone = $row['phone'];
            $this->is_admin = $row['is_admin'];
            
            return true;
        }

        return false;
    }

    /**
     * Mettre à jour le profil utilisateur
     */
    public function updateProfile() {
        $query = 'UPDATE ' . $this->table . '
                SET 
                    first_name = :first_name, 
                    last_name = :last_name, 
                    address = :address, 
                    city = :city, 
                    postal_code = :postal_code, 
                    country = :country, 
                    phone = :phone
                WHERE 
                    user_id = :user_id';

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->city = htmlspecialchars(strip_tags($this->city));
        $this->postal_code = htmlspecialchars(strip_tags($this->postal_code));
        $this->country = htmlspecialchars(strip_tags($this->country));
        $this->phone = htmlspecialchars(strip_tags($this->phone));

        // Lier les données
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':postal_code', $this->postal_code);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':phone', $this->phone);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword($new_password) {
        $query = 'UPDATE ' . $this->table . '
                SET password = :password
                WHERE user_id = :user_id';

        $stmt = $this->conn->prepare($query);

        // Hasher le nouveau mot de passe
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Lier les données
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':password', $hashed_password);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Récupère un utilisateur par son nom d'utilisateur
     * 
     * @param string $username Nom d'utilisateur
     * @return bool Succès ou échec
     */
    public function getUserByUsername($username) {
        $query = 'SELECT
                     user_id, username, email, password, first_name, last_name,
                     address, city, postal_code, country, phone, is_admin
                 FROM
                     ' . $this->table . '
                 WHERE
                     username = :username
                 LIMIT 0,1';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Définir les propriétés
            $this->user_id = $row['user_id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->password= $row['password']; // Assurez-vous que le champ s'appelle 'password' dans votre base de données
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->address = $row['address'];
            $this->city = $row['city'];
            $this->postal_code = $row['postal_code'];
            $this->country = $row['country'];
            $this->phone = $row['phone'];
            $this->is_admin = $row['is_admin'];
            
            return true;
        }
        
        return false;
    }

    /**
     * Vérifier si l'email existe déjà
     */
    private function emailExists() {
        $query = 'SELECT user_id FROM ' . $this->table . ' WHERE email = :email LIMIT 0,1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Vérifier si le nom d'utilisateur existe déjà
     */
    private function usernameExists() {
        $query = 'SELECT user_id FROM ' . $this->table . ' WHERE username = :username LIMIT 0,1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Vérifier si l'utilisateur est un administrateur
     */
    public function isAdmin() {
        return (bool) $this->is_admin;
    }

    /**
     * Récupère le nombre total d'utilisateurs
     */
    public function getTotalCount() {
        $query = 'SELECT COUNT(*) as total FROM ' . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Récupère tous les utilisateurs
     */
    public function getAll() {
        $query = 'SELECT 
                    user_id, 
                    username, 
                    email, 
                    first_name, 
                    last_name,
                    address,
                    city,
                    postal_code,
                    country,
                    phone,
                    is_admin,
                    created_at,
                    1 as is_active
                FROM 
                    ' . $this->table . '
                ORDER BY 
                    created_at DESC';
                    
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}