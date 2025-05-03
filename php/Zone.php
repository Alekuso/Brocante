<?php
namespace Brocante\Modele;

require_once __DIR__ . '/Database.php';
use Brocante\Base\Database;
require_once __DIR__ . '/Emplacement.php';
require_once __DIR__ . '/Brocanteur.php';

/**
 * Classe Zone
 * Représente une zone de la brocante
 */
class Zone {
    public $zid;
    public $nom;
    public $description;
    
    /**
     * Constructeur
     */
    public function __construct($donnees = []) {
        if (!empty($donnees)) {
            $this->zid = isset($donnees['zid']) ? $donnees['zid'] : null;
            $this->nom = $donnees['nom'] ?? '';
            $this->description = $donnees['description'] ?? '';
        }
    }
    
    /**
     * Récupère une zone par son ID
     * 
     * @param int $id L'ID de la zone
     * @return Zone|null La zone ou null si elle n'existe pas
     */
    public static function obtenirParId($id) {
        $db = Database::getInstance();
        $donnees = $db->obtenirUn("SELECT * FROM Zone WHERE zid = ?", [$id]);
        
        if ($donnees) {
            return new Zone($donnees);
        }
        return null;
    }
    
    /**
     * Récupère toutes les zones
     * 
     * @return array Tableau de zones
     */
    public static function obtenirToutes() {
        $db = Database::getInstance();
        $resultats = $db->obtenirTous("SELECT * FROM Zone ORDER BY nom ASC");
        
        $zones = [];
        foreach ($resultats as $donnees) {
            $zones[] = new Zone($donnees);
        }
        
        return $zones;
    }
    
    /**
     * Récupère les emplacements dans cette zone
     * 
     * @return array Tableau d'emplacements
     */
    public function obtenirEmplacements() {
        if (!$this->zid) {
            return [];
        }
        
        $db = Database::getInstance();
        $resultats = $db->obtenirTous("SELECT * FROM Emplacement WHERE zid = ? ORDER BY code ASC", [$this->zid]);
        
        $emplacements = [];
        foreach ($resultats as $donnees) {
            $emplacements[] = new Emplacement($donnees);
        }
        
        return $emplacements;
    }
    
    /**
     * Récupère les brocanteurs dans cette zone
     * 
     * @return array Tableau de brocanteurs
     */
    public function obtenirBrocanteurs() {
        if (!$this->zid) {
            return [];
        }
        
        $db = Database::getInstance();
        $resultats = $db->obtenirTous(
            "SELECT b.* FROM Brocanteur b 
            JOIN Emplacement e ON b.bid = e.bid 
            WHERE e.zid = ? AND b.visible = 1", 
            [$this->zid]
        );
        
        $brocanteurs = [];
        foreach ($resultats as $donnees) {
            $brocanteurs[] = new Brocanteur($donnees);
        }
        
        return $brocanteurs;
    }
    
    /**
     * Enregistre la zone dans la base de données
     * 
     * @return bool Succès de l'opération
     */
    public function enregistrer() {
        $db = Database::getInstance();
        
        if ($this->zid) {
            // Mise à jour
            $db->executer(
                "UPDATE Zone SET nom = ?, description = ? WHERE zid = ?",
                [$this->nom, $this->description, $this->zid]
            );
        } else {
            // Insertion
            $db->executer(
                "INSERT INTO Zone (nom, description) VALUES (?, ?)",
                [$this->nom, $this->description]
            );
            $this->zid = $db->dernierIdInsere();
        }
        
        return true;
    }
} 