<?php
include_once 'php/Brocanteur.php';
include_once 'php/Database.php';
include_once 'php/Objet.php';

use Brocante\Modele\Brocanteur;
use Brocante\Base\Database;
use Brocante\Modele\Objet;

// Vérifier si l'utilisateur est connecté
if (!Brocanteur::estConnecte()) {
    header('Location: connexion.php');
    exit;
}

// Récupérer le brocanteur connecté
$brocanteur = Brocanteur::obtenirConnecte();

// Récupérer l'ID de l'objet depuis l'URL
$oid = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupérer l'objet
$objet = Objet::obtenirParId($oid);

// Vérifier si l'objet existe et appartient au brocanteur connecté
if (!$objet || $objet->bid !== $brocanteur->bid) {
    header('Location: espaceBrocanteur.php');
    exit;
}

// Supprimer l'image si elle existe
if ($objet->image) {
    $uploadDir = 'uploads/objets/';
    $imagePath = $uploadDir . $objet->image;
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

// Supprimer l'objet
if ($objet->supprimer()) {
    // Redirection avec un message de succès
    header('Location: espaceBrocanteur.php?message=suppression_reussie');
} else {
    // Redirection avec un message d'erreur
    header('Location: espaceBrocanteur.php?message=erreur_suppression');
}
exit;
?> 