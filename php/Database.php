<?php
namespace Brocante\Base;

/**
 * Database
 * Gère les connexions à la base de données
 */
class Database {
    private $connexion;
    private static $instance = null;
    
    /**
     * Constructor - private to enforce singleton pattern
     */
    private function __construct() {
        try {
            $serveur = "192.168.132.203:3306";
            $utilisateur = "Q240027";
            $motDePasse = "acac755efeefd68d81d093e26fff0dea6cb50163";
            $nomBD = "Q240027";
            
            $this->connexion = new \PDO(
                "mysql:host=$serveur;dbname=$nomBD;charset=utf8",
                $utilisateur,
                $motDePasse,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false, // Use real prepared statements
                    \PDO::MYSQL_ATTR_FOUND_ROWS => true,
                    \PDO::ATTR_PERSISTENT => true // Use persistent connections
                ]
            );
        } catch (\PDOException $e) {
            die('Erreur connexion BD: ' . $e->getMessage());
        }
    }
    
    /**
     * Get database instance (singleton pattern)
     * @return Database The singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get one record from the database
     * @param string $requete SQL query with placeholders
     * @param array $params Parameters for the query
     * @return array|false Single record as associative array or false if no record found
     */
    public function obtenirUn($requete, $params = []) {
        $stmt = $this->connexion->prepare($requete);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    /**
     * Get all records from the database
     * @param string $requete SQL query with placeholders
     * @param array $params Parameters for the query
     * @return array Array of records as associative arrays
     */
    public function obtenirTous($requete, $params = []) {
        $stmt = $this->connexion->prepare($requete);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Count records from the database
     * @param string $requete SQL query with placeholders
     * @param array $params Parameters for the query
     * @return int Count of records
     */
    public function compter($requete, $params = []) {
        $stmt = $this->connexion->prepare($requete);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
    
    /**
     * Execute a query
     * @param string $requete SQL query with placeholders
     * @param array $params Parameters for the query
     * @return bool True on success, false on failure
     */
    public function executer($requete, $params = []) {
        $stmt = $this->connexion->prepare($requete);
        return $stmt->execute($params);
    }
    
    /**
     * Get the last inserted ID
     * @return string Last inserted ID
     */
    public function dernierIdInsere() {
        return $this->connexion->lastInsertId();
    }
    
    /**
     * Begin a transaction
     * @return bool True on success, false on failure
     */
    public function beginTransaction() {
        return $this->connexion->beginTransaction();
    }
    
    /**
     * Commit a transaction
     * @return bool True on success, false on failure
     */
    public function commit() {
        return $this->connexion->commit();
    }
    
    /**
     * Rollback a transaction
     * @return bool True on success, false on failure
     */
    public function rollback() {
        return $this->connexion->rollback();
    }
} 