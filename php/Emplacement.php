<?php
namespace Brocante\Modele;

require_once __DIR__ . '/Database.php';
use Brocante\Base\Database;
require_once __DIR__ . '/Zone.php';
require_once __DIR__ . '/Brocanteur.php';

/**
 * Classe Emplacement
 * Représente un emplacement dans une zone de la brocante
 */
class Emplacement {
    public $eid;
    public $code;
    public $zid;
    public $bid;
    
    /**
     * Constructeur
     */
    public function __construct($donnees = []) {
        if (!empty($donnees)) {
            $this->eid = isset($donnees['eid']) ? $donnees['eid'] : null;
            $this->code = $donnees['code'] ?? null;
            $this->zid = $donnees['zid'] ?? null;
            $this->bid = $donnees['bid'] ?? null;
        }
    }
    
    /**
     * Récupère un emplacement par son ID
     * 
     * @param int $id L'ID de l'emplacement
     * @return Emplacement|null L'emplacement ou null s'il n'existe pas
     */
    public static function obtenirParId($id) {
        $db = new Database();
        $donnees = $db->obtenirUn("SELECT * FROM Emplacement WHERE eid = ?", [$id]);
        
        if ($donnees) {
            return new Emplacement($donnees);
        }
        return null;
    }
    
    /**
     * Récupère tous les emplacements
     * 
     * @return array Tableau d'emplacements
     */
    public static function obtenirTous() {
        $db = new Database();
        $resultats = $db->obtenirTous("SELECT * FROM Emplacement ORDER BY eid ASC");
        
        $emplacements = [];
        foreach ($resultats as $donnees) {
            $emplacements[] = new Emplacement($donnees);
        }
        
        return $emplacements;
    }
    
    /**
     * Récupère la zone de cet emplacement
     * 
     * @return Zone|null La zone ou null en cas d'erreur
     */
    public function obtenirZone() {
        if (!$this->zid) {
            return null;
        }
        
        return Zone::obtenirParId($this->zid);
    }
    
    /**
     * Récupère le brocanteur de cet emplacement
     * 
     * @return Brocanteur|null Le brocanteur ou null en cas d'erreur
     */
    public function obtenirBrocanteur() {
        if (!$this->bid) {
            return null;
        }
        
        return Brocanteur::obtenirParId($this->bid);
    }
    
    /**
     * Enregistre l'emplacement dans la base de données
     * 
     * @return bool Succès de l'opération
     */
    public function enregistrer() {
        $db = new Database();
        
        if ($this->eid) {
            // Mise à jour
            $db->executer(
                "UPDATE Emplacement SET code = ?, zid = ?, bid = ? WHERE eid = ?",
                [$this->code, $this->zid, $this->bid, $this->eid]
            );
        } else {
            // Insertion
            $db->executer(
                "INSERT INTO Emplacement (code, zid, bid) VALUES (?, ?, ?)",
                [$this->code, $this->zid, $this->bid]
            );
            $this->eid = $db->dernierIdInsere();
        }
        
        return true;
    }
} 