<?php
namespace Brocante\Modele;

require_once __DIR__ . '/Database.php';
use Brocante\Base\Database;
require_once __DIR__ . '/Objet.php';

/**
 * Classe Categorie
 * Représente une catégorie d'objets
 */
class Categorie {
    public $cid;
    public $intitule;
    
    /**
     * Constructeur
     */
    public function __construct($donnees = []) {
        if (!empty($donnees)) {
            $this->cid = isset($donnees['cid']) ? $donnees['cid'] : null;
            $this->intitule = $donnees['intitule'] ?? '';
        }
    }
    
    /**
     * Récupère une catégorie par son ID
     * 
     * @param int $id L'ID de la catégorie
     * @return Categorie|null La catégorie ou null si elle n'existe pas
     */
    public static function obtenirParId($id) {
        $db = new Database();
        $donnees = $db->obtenirUn("SELECT * FROM Categorie WHERE cid = ?", [$id]);
        
        if ($donnees) {
            return new Categorie($donnees);
        }
        return null;
    }
    
    /**
     * Récupère toutes les catégories
     * 
     * @return array Tableau de catégories
     */
    public static function obtenirToutes() {
        $db = new Database();
        $resultats = $db->obtenirTous("SELECT * FROM Categorie ORDER BY intitule ASC");
        
        $categories = [];
        foreach ($resultats as $donnees) {
            $categories[] = new Categorie($donnees);
        }
        
        return $categories;
    }
    
    /**
     * Récupère les objets dans cette catégorie
     * 
     * @return array Tableau d'objets
     */
    public function obtenirObjets() {
        if (!$this->cid) {
            return [];
        }
        
        return Objet::obtenirParCategorie($this->cid);
    }
    
    /**
     * Enregistre la catégorie dans la base de données
     * 
     * @return bool Succès de l'opération
     */
    public function enregistrer() {
        $db = new Database();
        
        if ($this->cid) {
            // Mise à jour
            $db->executer(
                "UPDATE Categorie SET intitule = ? WHERE cid = ?",
                [$this->intitule, $this->cid]
            );
        } else {
            // Insertion
            $db->executer(
                "INSERT INTO Categorie (intitule) VALUES (?)",
                [$this->intitule]
            );
            $this->cid = $db->dernierIdInsere();
        }
        
        return true;
    }
} 