<?php
class Objet {
    public $id, $titre, $description, $image, $categorie_id, $brocanteur_id;

    public function __construct($id, $titre, $description, $image, $categorie_id, $brocanteur_id) {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->image = $image;
        $this->categorie_id = $categorie_id;
        $this->brocanteur_id = $brocanteur_id;
    }

    // Récupérer le brocanteur de cet objet
    public function getBrocanteur($pdo) {
        return Brocanteur::getById($pdo, $this->brocanteur_id);
    }

    // Méthode statique pour récupérer un objet par ID
    public static function getById($pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM objet WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return new Objet(...array_values($data));
        }
        return null;
    }
}
