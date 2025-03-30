<?php
class DB {
    public $pdo;

    public function __construct() {
        $url = "192.168.132.203:3306";
        $user = "Q240027";
        $pass = "acac755efeefd68d81d093e26fff0dea6cb50163"; // OoOoOo super secret password!

        try {
            $pdo = new PDO("mysql:host=$url;dbname=$user;charset=utf8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Impossible de se connecter à la DB : " . $e->getMessage());
        }

        $this->pdo = $pdo;
    }

    public function getRandomObjets($limit = 3) {
        $stmt = $this->pdo->prepare("SELECT * FROM Objet ORDER BY RAND() LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategories() {
        $stmt = $this->pdo->prepare("SELECT * FROM Categorie");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBrocanteurs() {
        $stmt = $this->pdo->prepare("SELECT * FROM Brocanteur");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getObjetById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Objet WHERE oid = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getBrocanteurById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Brocanteur WHERE bid = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}