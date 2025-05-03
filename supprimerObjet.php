<?php
include_once 'php/Brocanteur.php';
include_once 'php/Database.php';
include_once 'php/Objet.php';

use Brocante\Modele\Brocanteur;
use Brocante\Base\Database;
use Brocante\Modele\Objet;

// Vérifie la connexion
if (!Brocanteur::estConnecte()) {
    header('Location: connexion.php');
    exit;
}

// Récupère le brocanteur
$brocanteur = Brocanteur::obtenirConnecte();

// Récupère l'ID de l'objet
$oid = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupère l'objet
$objet = Objet::obtenirParId($oid);

// Vérifie la propriété
if (!$objet || $objet->bid !== $brocanteur->bid) {
    header('Location: espaceBrocanteur.php');
    exit;
}

// Supprime l'image
if ($objet->image) {
    $uploadDir = 'uploads/objets/';
    $imagePath = $uploadDir . $objet->image;
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

// Supprime l'objet
if ($objet->supprimer()) {
    header('Location: espaceBrocanteur.php?message=suppression_reussie');
} else {
    header('Location: espaceBrocanteur.php?message=erreur_suppression');
}
exit;
?> 