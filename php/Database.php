<?php
/**
 * Classe Base de données
 * Gère la connexion et les requêtes à la base de données
 */
class Database {
    public $pdo;

    /**
     * Constructeur: établit la connexion à la base de données
     */
    public function __construct() {
        $serveur = "192.168.132.203:3306";
        $utilisateur = "Q240027";
        $motDePasse = "acac755efeefd68d81d093e26fff0dea6cb50163";
        $nomBD = "Q240027";
        
        $this->pdo = new PDO("mysql:host=$serveur;dbname=$nomBD;charset=utf8", $utilisateur, $motDePasse);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Exécute une requête SQL et retourne le résultat
     * 
     * @param string $requete La requête SQL
     * @param array $parametres Les paramètres de la requête
     * @return PDOStatement L'objet statement après exécution
     */
    public function executer($requete, $parametres = []) {
        $stmt = $this->pdo->prepare($requete);
        $stmt->execute($parametres);
        return $stmt;
    }
    
    /**
     * Retourne une seule ligne de résultat
     * 
     * @param string $requete La requête SQL
     * @param array $parametres Les paramètres de la requête
     * @return array|false Un tableau associatif contenant la ligne ou false si aucun résultat
     */
    public function obtenirUn($requete, $parametres = []) {
        $stmt = $this->executer($requete, $parametres);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Retourne toutes les lignes de résultat
     * 
     * @param string $requete La requête SQL
     * @param array $parametres Les paramètres de la requête
     * @return array Un tableau de tableaux associatifs contenant les lignes
     */
    public function obtenirTous($requete, $parametres = []) {
        $stmt = $this->executer($requete, $parametres);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Retourne le dernier ID inséré
     * 
     * @return string L'ID de la dernière insertion
     */
    public function dernierIdInsere() {
        return $this->pdo->lastInsertId();
    }
} 