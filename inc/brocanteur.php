<?php
class Brocanteur {
    public $id, $nom, $prenom, $email, $description, $photo, $visible, $eid;

    public function __construct($id, $nom, $prenom, $email, $description, $photo, $visible, $eid) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->description = $description;
        $this->photo = $photo;
        $this->visible = $visible;
        $this->eid = $eid;
    }

    // Récupérer tous les objets de ce brocanteur
    public function getObjets($pdo) {
        $stmt = $pdo->prepare("SELECT * FROM objet WHERE brocanteur_id = ?");
        $stmt->execute([$this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode statique pour récupérer un brocanteur depuis un ID
    public static function getById($pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM brocanteur WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return new Brocanteur(...array_values($data));
        }
        return null;
    }
}
