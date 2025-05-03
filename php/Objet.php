<?php
namespace Brocante\Modele;

require_once __DIR__ . '/Database.php';
use Brocante\Base\Database;
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
            $this->prix = $donnees['prix'] ?? 0;
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
        $db = Database::getInstance();
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
        $db = Database::getInstance();
        $sql = "SELECT o.*, c.intitule as categorie_nom, b.nom as brocanteur_nom, b.prenom as brocanteur_prenom
                FROM Objet o
                LEFT JOIN Categorie c ON o.cid = c.cid
                LEFT JOIN Brocanteur b ON o.bid = b.bid
                WHERE b.visible = 1
                ORDER BY o.intitule ASC";
        
        $resultats = $db->obtenirTous($sql);
        
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
        $db = Database::getInstance();
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
        $db = Database::getInstance();
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
        $db = Database::getInstance();
        
        // Filtrer les données
        $intitule = htmlspecialchars($this->intitule);
        $description = htmlspecialchars($this->description);
        
        if ($this->oid) {
            // Mise à jour
            $success = $db->executer(
                "UPDATE Objet SET intitule = ?, prix = ?, description = ?, image = ?, bid = ?, cid = ? WHERE oid = ?",
                [$intitule, $this->prix, $description, $this->image, $this->bid, $this->cid, $this->oid]
            );
        } else {
            // Insertion
            $success = $db->executer(
                "INSERT INTO Objet (intitule, prix, description, image, bid, cid) VALUES (?, ?, ?, ?, ?, ?)",
                [$intitule, $this->prix, $description, $this->image, $this->bid, $this->cid]
            );
            
            if ($success) {
                $this->oid = $db->dernierIdInsere();
            }
        }
        
        return $success;
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
        
        $db = Database::getInstance();
        return $db->executer("DELETE FROM Objet WHERE oid = ?", [$this->oid]);
    }
    
    /**
     * Recherche des objets selon différents critères
     * 
     * @param string $nom Partie du nom à rechercher (optionnel)
     * @param int $categorieId ID de la catégorie (optionnel)
     * @param string $prixFiltre Ordre de tri par prix ('asc' ou 'desc')
     * @return array Tableau d'objets correspondant aux critères
     */
    public static function rechercher($nom = '', $categorieId = null, $prixFiltre = 'asc') {
        $db = Database::getInstance();
        $params = [];
        
        $sql = "SELECT o.*, c.intitule as categorie_nom, b.nom as brocanteur_nom, b.prenom as brocanteur_prenom
                FROM Objet o
                LEFT JOIN Categorie c ON o.cid = c.cid
                LEFT JOIN Brocanteur b ON o.bid = b.bid
                WHERE b.visible = 1";
        
        if (!empty($nom)) {
            $sql .= " AND (o.intitule LIKE ? OR o.description LIKE ?)";
            $params[] = "%$nom%";
            $params[] = "%$nom%";
        }
        
        if (!empty($categorieId)) {
            $sql .= " AND o.cid = ?";
            $params[] = $categorieId;
        }
        
        $sql .= " ORDER BY o.prix " . ($prixFiltre === 'desc' ? 'DESC' : 'ASC');
        
        $resultats = $db->obtenirTous($sql, $params);
        
        $objets = [];
        foreach ($resultats as $donnees) {
            $objets[] = new Objet($donnees);
        }
        
        // Sauvegarder les critères de recherche dans un cookie (valable 30 jours)
        setcookie('recherche_objet_nom', $nom, time() + 30 * 24 * 3600, '/');
        setcookie('recherche_objet_categorie', $categorieId, time() + 30 * 24 * 3600, '/');
        setcookie('recherche_objet_prix', $prixFiltre, time() + 30 * 24 * 3600, '/');
        
        return $objets;
    }
    
    public static function validerFormulaire($donnees) {
        $erreurs = [];
        
        // Validation de l'intitulé
        if (empty($donnees['intitule'])) {
            $erreurs['intitule'] = "L'intitulé est obligatoire";
        }
        
        // Validation du prix
        if (empty($donnees['prix'])) {
            $erreurs['prix'] = "Le prix est obligatoire";
        } elseif (!is_numeric($donnees['prix']) || $donnees['prix'] < 0) {
            $erreurs['prix'] = "Le prix doit être un nombre positif";
        }
        
        // Validation de la description
        if (empty($donnees['description'])) {
            $erreurs['description'] = "La description est obligatoire";
        }
        
        // Validation de la catégorie
        if (empty($donnees['categorie'])) {
            $erreurs['categorie'] = "La catégorie est obligatoire";
        }
        
        // Validation de l'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($extension, $allowed)) {
                $erreurs['image'] = "Format de fichier non autorisé. Utilisez JPG, PNG ou GIF";
            } elseif ($_FILES['image']['size'] > 5000000) { // 5MB
                $erreurs['image'] = "Le fichier est trop volumineux (max 5MB)";
            }
        }
        
        return $erreurs;
    }
} 