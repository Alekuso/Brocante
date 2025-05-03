<?php
namespace Brocante\Modele;

require_once __DIR__ . '/Database.php';
use Brocante\Base\Database;
require_once __DIR__ . '/Objet.php';

class Categorie {
    public $cid;
    public $intitule;
    
    public function __construct($donnees = []) {
        if (!empty($donnees)) {
            $this->cid = isset($donnees['cid']) ? $donnees['cid'] : null;
            $this->intitule = $donnees['intitule'] ?? '';
        }
    }
    
    /**
     * Récupère une catégorie par son ID
     */
    public static function obtenirParId($id) {
        $db = Database::getInstance();
        $donnees = $db->obtenirUn("SELECT * FROM Categorie WHERE cid = ?", [$id]);
        
        if ($donnees) {
            return new Categorie($donnees);
        }
        return null;
    }
    
    /**
     * Récupère toutes les catégories
     */
    public static function obtenirToutes() {
        $db = Database::getInstance();
        $resultats = $db->obtenirTous("SELECT * FROM Categorie ORDER BY intitule ASC");
        
        $categories = [];
        foreach ($resultats as $donnees) {
            $categories[] = new Categorie($donnees);
        }
        
        return $categories;
    }
    
    /**
     * Récupère les objets dans cette catégorie
     */
    public function obtenirObjets() {
        if (!$this->cid) {
            return [];
        }
        
        return Objet::obtenirParCategorie($this->cid);
    }
    
    /**
     * Enregistre la catégorie dans la base de données
     */
    public function enregistrer() {
        $db = Database::getInstance();
        
        if ($this->cid) {
            // Update
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