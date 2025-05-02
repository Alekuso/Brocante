<?php
namespace Brocante\Base;

/**
 * Database
 * Gère les connexions à la base de données
 */
class Database {
    private $connexion;
    
    public function __construct() {
        try {
            $serveur = "192.168.132.203:3306";
            $utilisateur = "Q240027";
            $motDePasse = "acac755efeefd68d81d093e26fff0dea6cb50163";
            $nomBD = "Q240027";
            
            $this->connexion = new \PDO(
                "mysql:host=$serveur;dbname=$nomBD;charset=utf8",
                $utilisateur,
                $motDePasse,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
        } catch (\PDOException $e) {
            die('Erreur connexion BD: ' . $e->getMessage());
        }
    }
    
    public function obtenirUn($requete, $params = []) {
        $stmt = $this->connexion->prepare($requete);
        $stmt->execute($params);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function obtenirTous($requete, $params = []) {
        $stmt = $this->connexion->prepare($requete);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function compter($requete, $params = []) {
        $stmt = $this->connexion->prepare($requete);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
    
    public function executer($requete, $params = []) {
        $stmt = $this->connexion->prepare($requete);
        return $stmt->execute($params);
    }
    
    public function dernierIdInsere() {
        return $this->connexion->lastInsertId();
    }
} 