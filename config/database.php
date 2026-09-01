<?php
/**
 * Configuration de la base de données pour Scent
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'scent_db';
    private $username = 'root';
    private $password = ''; // À modifier selon votre configuration
    private $conn;

    /**
     * Connexion à la base de données
     *
     * @return PDO|null
     */
    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo 'Erreur de connexion: ' . $e->getMessage();
        }

        return $this->conn;
    }

    /**
     * Exécute une procédure stockée
     *
     * @param string $procedure Nom de la procédure
     * @param array $params Paramètres de la procédure
     * @return array Résultats
     */
    public function callProcedure($procedure, $params = []) {
        $conn = $this->connect();
        
        // Construire la chaîne d'appel de procédure
        $placeholders = implode(',', array_fill(0, count($params), '?'));
        $query = "CALL $procedure($placeholders)";
        
        // Préparer et exécuter la requête
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        
        // Récupérer tous les résultats
        $results = [];
        do {
            $results[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } while ($stmt->nextRowset());
        
        return $results;
    }
}