<?php
namespace Brocante\Modele;

require_once __DIR__ . '/Database.php';
use Brocante\Base\Database;

class Brocanteur {
    public $bid;
    public $nom;
    public $prenom;
    public $courriel;
    public $description;
    public $photo;
    public $visible;
    public $est_administrateur;
    
    public function __construct($donnees = []) {
        if (!empty($donnees)) {
            $this->bid = isset($donnees['bid']) ? $donnees['bid'] : null;
            $this->nom = $donnees['nom'] ?? '';
            $this->prenom = $donnees['prenom'] ?? '';
            $this->courriel = $donnees['courriel'] ?? '';
            $this->description = $donnees['description'] ?? '';
            $this->photo = $donnees['photo'] ?? null;
            $this->visible = isset($donnees['visible']) ? (bool)$donnees['visible'] : false;
            $this->est_administrateur = isset($donnees['est_administrateur']) ? (bool)$donnees['est_administrateur'] : false;
        }
    }
    
    /**
     * Récupère un brocanteur par son ID
     */
    public static function obtenirParId($id) {
        $db = Database::getInstance();
        $donnees = $db->obtenirUn("SELECT * FROM Brocanteur WHERE bid = ?", [$id]);
        
        if ($donnees) {
            return new Brocanteur($donnees);
        }
        return null;
    }
    
    /**
     * Récupère tous les brocanteurs visibles
     */
    public static function obtenirTousVisibles() {
        $db = Database::getInstance();
        $resultats = $db->obtenirTous("SELECT * FROM Brocanteur WHERE visible = 1 ORDER BY nom, prenom");
        
        $brocanteurs = [];
        foreach ($resultats as $donnees) {
            $brocanteurs[] = new Brocanteur($donnees);
        }
        
        return $brocanteurs;
    }
    
    /**
     * Récupère tous les objets de ce brocanteur
     */
    public function obtenirObjets() {
        if (!$this->bid) {
            return [];
        }
        
        $db = Database::getInstance();
        $resultats = $db->obtenirTous("SELECT * FROM Objet WHERE bid = ?", [$this->bid]);
        
        $objets = [];
        foreach ($resultats as $donnees) {
            require_once __DIR__ . '/Objet.php';
            $objets[] = new \Brocante\Modele\Objet($donnees);
        }
        
        return $objets;
    }
    
    /**
     * Récupère l'emplacement du brocanteur
     */
    public function obtenirEmplacement() {
        if (!$this->bid) {
            return null;
        }
        
        $db = Database::getInstance();
        $donnees = $db->obtenirUn("SELECT e.*, z.nom AS zone_nom 
                                    FROM Emplacement e 
                                    JOIN Zone z ON e.zid = z.zid 
                                    WHERE e.bid = ?", [$this->bid]);
        
        if ($donnees) {
            require_once __DIR__ . '/Emplacement.php';
            return new \Brocante\Modele\Emplacement($donnees);
        }
        
        return null;
    }
    
    /**
     * Récupère la zone du brocanteur via son emplacement
     */
    public function obtenirZone() {
        $emplacement = $this->obtenirEmplacement();
        
        if ($emplacement) {
            return $emplacement->obtenirZone();
        }
        
        return null;
    }
    
    /**
     * Enregistre le brocanteur dans la base de données
     */
    public function enregistrer() {
        $db = Database::getInstance();
        
        // Filtre les données
        $nom = htmlspecialchars($this->nom);
        $prenom = htmlspecialchars($this->prenom);
        $courriel = filter_var($this->courriel, FILTER_SANITIZE_EMAIL);
        $description = htmlspecialchars($this->description);
        
        if ($this->bid) {
            // Mise à jour
            $db->executer(
                "UPDATE Brocanteur SET nom = ?, prenom = ?, courriel = ?, description = ?, 
                photo = ?, visible = ?, est_administrateur = ? WHERE bid = ?",
                [
                    $nom, $prenom, $courriel, $description,
                    $this->photo, $this->visible, $this->est_administrateur, $this->bid
                ]
            );
        } else {
            // Insertion
            $db->executer(
                "INSERT INTO Brocanteur (nom, prenom, courriel, mot_passe, description, photo, visible, est_administrateur) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $nom, $prenom, $courriel, 
                    password_hash('motdepasse', PASSWORD_DEFAULT),
                    $description, $this->photo, $this->visible, 
                    $this->est_administrateur
                ]
            );
            $this->bid = $db->dernierIdInsere();
        }
        
        return true;
    }
    
    /**
     * Vérifie les identifiants d'un brocanteur
     */
    public static function connecter($courriel, $motDePasse) {
        $db = Database::getInstance();
        $courriel = filter_var($courriel, FILTER_SANITIZE_EMAIL);
        $donnees = $db->obtenirUn("SELECT * FROM Brocanteur WHERE courriel = ?", [$courriel]);
        
        if ($donnees && password_verify($motDePasse, $donnees['mot_passe'])) {
            // Si admin, peut toujours se connecter, sinon doit être visible
            if ($donnees['est_administrateur'] || $donnees['visible']) {
                return new Brocanteur($donnees);
            }
        }
        
        return null;
    }
    
    /**
     * Recherche des brocanteurs selon leur nom et prénom
     */
    public static function rechercher($nom = '', $prenom = '') {
        $db = Database::getInstance();
        $params = [];
        $sql = "SELECT * FROM Brocanteur WHERE visible = 1";
        
        if (!empty($nom)) {
            $sql .= " AND nom LIKE ?";
            $params[] = "%$nom%";
        }
        
        if (!empty($prenom)) {
            $sql .= " AND prenom LIKE ?";
            $params[] = "%$prenom%";
        }
        
        $sql .= " ORDER BY nom ASC, prenom ASC";
        
        $resultats = $db->obtenirTous($sql, $params);
        
        $brocanteurs = [];
        foreach ($resultats as $donnees) {
            $brocanteurs[] = new Brocanteur($donnees);
        }
        
        return $brocanteurs;
    }
    

    public static function demarrerSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function estConnecte() {
        self::demarrerSession();
        return isset($_SESSION['bid']);
    }

    public static function estAdmin() {
        self::demarrerSession();
        return isset($_SESSION['est_admin']) && $_SESSION['est_admin'] === true;
    }

    public static function connecterUtilisateur($brocanteur) {
        self::demarrerSession();
        $_SESSION['bid'] = $brocanteur->bid;
        $_SESSION['nom'] = $brocanteur->nom;
        $_SESSION['prenom'] = $brocanteur->prenom;
        $_SESSION['est_admin'] = $brocanteur->est_administrateur;
    }
    
    public static function deconnecter() {
        self::demarrerSession();
        session_unset();
        session_destroy();
    }
    
    public static function obtenirConnecte() {
        if (!self::estConnecte()) {
            return null;
        }
        return self::obtenirParId($_SESSION['bid']);
    }
    
    public static function validerInscription($donnees) {
        $erreurs = [];
        
        // Vérification des champs obligatoires
        if (empty($donnees['nom'])) {
            $erreurs['nom'] = "Le nom est obligatoire";
        }
        
        if (empty($donnees['prenom'])) {
            $erreurs['prenom'] = "Le prénom est obligatoire";
        }
        
        if (empty($donnees['email'])) {
            $erreurs['email'] = "L'email est obligatoire";
        } elseif (!filter_var($donnees['email'], FILTER_VALIDATE_EMAIL)) {
            $erreurs['email'] = "L'email n'est pas valide";
        } else {
            // Vérification si l'email existe déjà
            $db = Database::getInstance();
            $existant = $db->obtenirUn("SELECT courriel FROM Brocanteur WHERE courriel = ?", [$donnees['email']]);
            if ($existant) {
                $erreurs['email'] = "Cet email est déjà utilisé";
            }
        }
        
        if (empty($donnees['password'])) {
            $erreurs['password'] = "Le mot de passe est obligatoire";
        } elseif (strlen($donnees['password']) < 6) {
            $erreurs['password'] = "Le mot de passe doit contenir au moins 6 caractères";
        }
        
        if ($donnees['password'] !== $donnees['password_confirm']) {
            $erreurs['password_confirm'] = "Les mots de passe ne correspondent pas";
        }
        
        if (empty($donnees['description'])) {
            $erreurs['description'] = "La description est obligatoire";
        }
        
        return $erreurs;
    }
    
    public static function inscrire($donnees, $photo = null) {
        $db = Database::getInstance();
        
        // Création du nouvel utilisateur
        $hashedPassword = password_hash($donnees['password'], PASSWORD_DEFAULT);
        
        $db->executer(
            "INSERT INTO Brocanteur (nom, prenom, courriel, mot_passe, description, photo, visible, est_administrateur) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                htmlspecialchars($donnees['nom']),
                htmlspecialchars($donnees['prenom']),
                filter_var($donnees['email'], FILTER_SANITIZE_EMAIL),
                $hashedPassword,
                htmlspecialchars($donnees['description']),
                $photo,
                0, // Non visible par défaut, en attente de validation admin
                0  // Non admin par défaut
            ]
        );
        
        return $db->dernierIdInsere();
    }
    
    /**
     * Récupère les brocanteurs en attente de validation
     */
    public static function obtenirEnAttente() {
        $db = Database::getInstance();
        $resultats = $db->obtenirTous("SELECT * FROM Brocanteur WHERE visible = 0 ORDER BY nom, prenom");
        
        $brocanteurs = [];
        foreach ($resultats as $donnees) {
            $brocanteurs[] = new Brocanteur($donnees);
        }
        
        return $brocanteurs;
    }
    
    /**
     * Approuve un brocanteur (le rend visible)
     */
    public function approuver() {
        if (!$this->bid) return false;
        
        $this->visible = true;
        
        $db = Database::getInstance();
        return $db->executer("UPDATE Brocanteur SET visible = 1 WHERE bid = ?", [$this->bid]);
    }
    
    /**
     * Rejette un brocanteur (le supprime)
     */
    public function rejeter() {
        if (!$this->bid) return false;
        
        $db = Database::getInstance();
        return $db->executer("DELETE FROM Brocanteur WHERE bid = ?", [$this->bid]);
    }
} 