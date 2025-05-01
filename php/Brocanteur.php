<?php
require_once __DIR__ . '/Database.php';

/**
 * Classe Brocanteur
 * Représente un brocanteur dans le système
 */
class Brocanteur {
    public $bid;
    public $nom;
    public $prenom;
    public $courriel;
    public $description;
    public $photo;
    public $visible;
    public $est_administrateur;
    
    /**
     * Constructeur
     */
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
     * 
     * @param int $id L'ID du brocanteur
     * @return Brocanteur|null Le brocanteur ou null s'il n'existe pas
     */
    public static function obtenirParId($id) {
        $db = new Database();
        $donnees = $db->obtenirUn("SELECT * FROM Brocanteur WHERE bid = ?", [$id]);
        
        if ($donnees) {
            return new Brocanteur($donnees);
        }
        return null;
    }
    
    /**
     * Récupère tous les brocanteurs visibles
     * 
     * @return array Tableau de brocanteurs
     */
    public static function obtenirTousVisibles() {
        $db = new Database();
        $resultats = $db->obtenirTous("SELECT * FROM Brocanteur WHERE visible = 1");
        
        $brocanteurs = [];
        foreach ($resultats as $donnees) {
            $brocanteurs[] = new Brocanteur($donnees);
        }
        
        return $brocanteurs;
    }
    
    /**
     * Récupère tous les objets de ce brocanteur
     * 
     * @return array Tableau d'objets
     */
    public function obtenirObjets() {
        if (!$this->bid) {
            return [];
        }
        
        $db = new Database();
        $resultats = $db->obtenirTous("SELECT * FROM Objet WHERE bid = ?", [$this->bid]);
        
        $objets = [];
        foreach ($resultats as $donnees) {
            require_once __DIR__ . '/Objet.php';
            $objets[] = new Objet($donnees);
        }
        
        return $objets;
    }
    
    /**
     * Récupère l'emplacement du brocanteur
     * 
     * @return Emplacement|null L'emplacement ou null s'il n'existe pas
     */
    public function obtenirEmplacement() {
        if (!$this->bid) {
            return null;
        }
        
        $db = new Database();
        $donnees = $db->obtenirUn("SELECT * FROM Emplacement WHERE bid = ?", [$this->bid]);
        
        if ($donnees) {
            require_once __DIR__ . '/Emplacement.php';
            return new Emplacement($donnees);
        }
        
        return null;
    }
    
    /**
     * Récupère la zone du brocanteur via son emplacement
     * 
     * @return Zone|null La zone ou null s'il n'existe pas
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
     * 
     * @return bool Succès de l'opération
     */
    public function enregistrer() {
        $db = new Database();
        
        if ($this->bid) {
            // Mise à jour
            $db->executer(
                "UPDATE Brocanteur SET nom = ?, prenom = ?, courriel = ?, description = ?, 
                photo = ?, visible = ?, est_administrateur = ? WHERE bid = ?",
                [
                    $this->nom, $this->prenom, $this->courriel, $this->description,
                    $this->photo, $this->visible, $this->est_administrateur, $this->bid
                ]
            );
        } else {
            // Insertion
            $db->executer(
                "INSERT INTO Brocanteur (nom, prenom, courriel, mot_passe, description, photo, visible, est_administrateur) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $this->nom, $this->prenom, $this->courriel, 
                    password_hash('motdepasse', PASSWORD_DEFAULT), // Mot de passe par défaut
                    $this->description, $this->photo, $this->visible, 
                    $this->est_administrateur
                ]
            );
            $this->bid = $db->dernierIdInsere();
        }
        
        return true;
    }
    
    /**
     * Vérifie les identifiants d'un brocanteur
     * 
     * @param string $courriel Le courriel du brocanteur
     * @param string $motDePasse Le mot de passe du brocanteur
     * @return Brocanteur|null Le brocanteur connecté ou null si échec
     */
    public static function connecter($courriel, $motDePasse) {
        $db = new Database();
        $donnees = $db->obtenirUn("SELECT * FROM Brocanteur WHERE courriel = ?", [$courriel]);
        
        if ($donnees && password_verify($motDePasse, $donnees['mot_passe'])) {
            return new Brocanteur($donnees);
        }
        
        return null;
    }
    
    /**
     * Recherche des brocanteurs selon leur nom et prénom
     * 
     * @param string $nom Partie du nom à rechercher (optionnel)
     * @param string $prenom Partie du prénom à rechercher (optionnel)
     * @return array Tableau de brocanteurs correspondant aux critères
     */
    public static function rechercher($nom = '', $prenom = '') {
        $db = new Database();
        $params = [];
        $sql = "SELECT * FROM Brocanteur WHERE visible = 1";
        
        // Filtre par nom
        if (!empty($nom)) {
            $sql .= " AND nom LIKE ?";
            $params[] = "%$nom%";
        }
        
        // Filtre par prénom
        if (!empty($prenom)) {
            $sql .= " AND prenom LIKE ?";
            $params[] = "%$prenom%";
        }
        
        // Tri par nom puis prénom
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
        $_SESSION['prenom'] = $brocanteur->prenom;
        $_SESSION['nom'] = $brocanteur->nom;
        $_SESSION['est_admin'] = $brocanteur->est_administrateur;
    }
    
    public static function deconnecter() {
        self::demarrerSession();
        session_unset();
        session_destroy();
    }

    public static function obtenirConnecte() {
        if (self::estConnecte()) {
            return self::obtenirParId($_SESSION['bid']);
        }
        return null;
    }
} 