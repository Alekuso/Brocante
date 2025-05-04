<?php
namespace Brocante\Base;

class Database {
    private $connexion;
    private static $instance = null;
    
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
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::MYSQL_ATTR_FOUND_ROWS => true
                ]
            );
        } catch (\PDOException $e) {
            die('Erreur de connexion à la base de données');
        }
    }
    
    /**
     * Récupère l'instance de la base de données
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Récupère un enregistrement de la base de données
     */
    public function obtenirUn($requete, $params = []) {
        $stmt = $this->connexion->prepare($requete);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    /**
     * Récupère tous les enregistrements de la base de données
     */
    public function obtenirTous($requete, $params = []) {
        $stmt = $this->connexion->prepare($requete);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Compte les enregistrements
     */
    public function compter($requete, $params = []) {
        $stmt = $this->connexion->prepare($requete);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
    
    /**
     * Exécute une requête
     */
    public function executer($requete, $params = []) {
        $stmt = $this->connexion->prepare($requete);
        return $stmt->execute($params);
    }
    
    /**
     * Récupère le dernier ID inséré
     */
    public function dernierIdInsere() {
        return $this->connexion->lastInsertId();
    }
    
    /**
     * Démarre une transaction
     */
    public function beginTransaction() {
        return $this->connexion->beginTransaction();
    }
    
    /**
     * Valide une transaction
     */
    public function commit() {
        return $this->connexion->commit();
    }
    
    /**
     * Annule une transaction
     */
    public function rollback() {
        return $this->connexion->rollback();
    }
} 