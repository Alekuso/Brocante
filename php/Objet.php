<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Brocanteur.php';
require_once __DIR__ . '/Categorie.php';

/**
 * Classe Objet
 * Représente un objet à vendre dans la brocante
 */
class Objet {
    public $oid;
    public $intitule;
    public $prix;
    public $description;
    public $image;
    public $bid;
    public $cid;
    
    /**
     * Constructeur
     */
    public function __construct($donnees = []) {
        if (!empty($donnees)) {
            $this->oid = isset($donnees['oid']) ? $donnees['oid'] : null;
            $this->intitule = $donnees['intitule'] ?? '';
            $this->prix = $donnees['prix'] ?? 0.0;
            $this->description = $donnees['description'] ?? '';
            $this->image = $donnees['image'] ?? null;
            $this->bid = $donnees['bid'] ?? null;
            $this->cid = $donnees['cid'] ?? null;
        }
    }
    
    /**
     * Récupère un objet par son ID
     * 
     * @param int $id L'ID de l'objet
     * @return Objet|null L'objet ou null s'il n'existe pas
     */
    public static function obtenirParId($id) {
        $db = new Database();
        $donnees = $db->obtenirUn("SELECT * FROM Objet WHERE oid = ?", [$id]);
        
        if ($donnees) {
            return new Objet($donnees);
        }
        return null;
    }
    
    /**
     * Récupère tous les objets
     * 
     * @return array Tableau d'objets
     */
    public static function obtenirTous() {
        $db = new Database();
        $resultats = $db->obtenirTous("SELECT * FROM Objet");
        
        $objets = [];
        foreach ($resultats as $donnees) {
            $objets[] = new Objet($donnees);
        }
        
        return $objets;
    }
    
    /**
     * Récupère des objets aléatoires
     * 
     * @param int $nombre Le nombre d'objets à récupérer
     * @return array Tableau d'objets
     */
    public static function obtenirAleatoires($nombre = 3) {
        $db = new Database();
        $resultats = $db->obtenirTous("SELECT * FROM Objet ORDER BY RAND() LIMIT " . intval($nombre));
        
        $objets = [];
        foreach ($resultats as $donnees) {
            $objets[] = new Objet($donnees);
        }
        
        return $objets;
    }
    
    /**
     * Récupère tous les objets d'une catégorie
     * 
     * @param int $categorieId L'ID de la catégorie
     * @return array Tableau d'objets
     */
    public static function obtenirParCategorie($categorieId) {
        $db = new Database();
        $resultats = $db->obtenirTous("SELECT * FROM Objet WHERE cid = ?", [$categorieId]);
        
        $objets = [];
        foreach ($resultats as $donnees) {
            $objets[] = new Objet($donnees);
        }
        
        return $objets;
    }
    
    /**
     * Récupère le brocanteur de cet objet
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
     * Récupère la catégorie de cet objet
     * 
     * @return Categorie|null La catégorie ou null en cas d'erreur
     */
    public function obtenirCategorie() {
        if (!$this->cid) {
            return null;
        }
        
        return Categorie::obtenirParId($this->cid);
    }
    
    /**
     * Formate le prix pour l'affichage
     * 
     * @return string Le prix formaté avec le symbole €
     */
    public function prixFormate() {
        return number_format($this->prix, 2, ',', ' ') . ' €';
    }
    
    /**
     * Enregistre l'objet dans la base de données
     * 
     * @return bool Succès de l'opération
     */
    public function enregistrer() {
        $db = new Database();
        
        if ($this->oid) {
            // Mise à jour
            $db->executer(
                "UPDATE Objet SET intitule = ?, prix = ?, description = ?, image = ?, bid = ?, cid = ? WHERE oid = ?",
                [$this->intitule, $this->prix, $this->description, $this->image, $this->bid, $this->cid, $this->oid]
            );
        } else {
            // Insertion
            $db->executer(
                "INSERT INTO Objet (intitule, prix, description, image, bid, cid) VALUES (?, ?, ?, ?, ?, ?)",
                [$this->intitule, $this->prix, $this->description, $this->image, $this->bid, $this->cid]
            );
            $this->oid = $db->dernierIdInsere();
        }
        
        return true;
    }
    
    /**
     * Supprime l'objet de la base de données
     * 
     * @return bool Succès de l'opération
     */
    public function supprimer() {
        if (!$this->oid) {
            return false;
        }
        
        $db = new Database();
        $db->executer("DELETE FROM Objet WHERE oid = ?", [$this->oid]);
        return true;
    }
    
    /**
     * Recherche des objets selon différents critères
     * 
     * @param string $nom Partie du nom à rechercher (optionnel)
     * @param int $categorieId ID de la catégorie (optionnel)
     * @param string $triPrix Ordre de tri par prix ('asc' ou 'desc')
     * @return array Tableau d'objets correspondant aux critères
     */
    public static function rechercher($nom = '', $categorieId = null, $triPrix = 'asc') {
        $db = new Database();
        $params = [];
        $sql = "SELECT * FROM Objet WHERE 1=1";
        
        // Filtre par nom
        if (!empty($nom)) {
            $sql .= " AND intitule LIKE ?";
            $params[] = "%$nom%";
        }
        
        // Filtre par catégorie
        if (!empty($categorieId)) {
            $sql .= " AND cid = ?";
            $params[] = $categorieId;
        }
        
        // Tri par prix
        $ordre = ($triPrix === 'desc') ? 'DESC' : 'ASC';
        $sql .= " ORDER BY prix $ordre";
        
        $resultats = $db->obtenirTous($sql, $params);
        
        $objets = [];
        foreach ($resultats as $donnees) {
            $objets[] = new Objet($donnees);
        }
        
        return $objets;
    }
} 