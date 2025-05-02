<?php
namespace Brocante\Modele;

require_once __DIR__ . '/Database.php';
use Brocante\Base\Database;

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
        $resultats = $db->obtenirTous("SELECT * FROM Brocanteur WHERE visible = 1 ORDER BY nom, prenom");
        
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
            $objets[] = new \Brocante\Modele\Objet($donnees);
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
        
        // Sécurité: Filtrer les données
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
     * 
     * @param string $courriel Le courriel du brocanteur
     * @param string $motDePasse Le mot de passe du brocanteur
     * @return Brocanteur|null Le brocanteur connecté ou null si échec
     */
    public static function connecter($courriel, $motDePasse) {
        $db = new Database();
        // Sécurité: sanitize l'email
        $courriel = filter_var($courriel, FILTER_SANITIZE_EMAIL);
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
    
    public static function validerInscription($donnees) {
        $erreurs = [];
        
        // Validation du nom
        if (empty($donnees['nom'])) {
            $erreurs['nom'] = "Le nom est obligatoire";
        }
        
        // Validation du prénom
        if (empty($donnees['prenom'])) {
            $erreurs['prenom'] = "Le prénom est obligatoire";
        }
        
        // Validation du courriel
        if (empty($donnees['courriel'])) {
            $erreurs['courriel'] = "Le courriel est obligatoire";
        } elseif (!filter_var($donnees['courriel'], FILTER_VALIDATE_EMAIL)) {
            $erreurs['courriel'] = "Format de courriel invalide";
        } else {
            // Vérifier si le courriel existe déjà
            $db = new Database();
            $existant = $db->obtenirUn("SELECT bid FROM Brocanteur WHERE courriel = ?", [$donnees['courriel']]);
            if ($existant) {
                $erreurs['courriel'] = "Ce courriel est déjà utilisé";
            }
        }
        
        // Validation du mot de passe
        if (empty($donnees['mot_passe'])) {
            $erreurs['mot_passe'] = "Le mot de passe est obligatoire";
        } elseif (strlen($donnees['mot_passe']) < 6) {
            $erreurs['mot_passe'] = "Le mot de passe doit contenir au moins 6 caractères";
        }
        
        // Validation de la confirmation du mot de passe
        if (empty($donnees['confirmation_mot_passe'])) {
            $erreurs['confirmation_mot_passe'] = "La confirmation du mot de passe est obligatoire";
        } elseif ($donnees['mot_passe'] !== $donnees['confirmation_mot_passe']) {
            $erreurs['confirmation_mot_passe'] = "Les mots de passe ne correspondent pas";
        }
        
        // Validation de la description
        if (empty($donnees['description'])) {
            $erreurs['description'] = "La description est obligatoire";
        }
        
        // Validation de la photo si présente
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($extension, $allowed)) {
                $erreurs['photo'] = "Format de fichier non autorisé. Utilisez JPG, PNG ou GIF";
            } elseif ($_FILES['photo']['size'] > 5000000) { // 5MB
                $erreurs['photo'] = "Le fichier est trop volumineux (max 5MB)";
            }
        }
        
        return $erreurs;
    }
} 