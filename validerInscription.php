<?php
include_once 'php/Brocanteur.php';
include_once 'php/Database.php';

use Brocante\Modele\Brocanteur;
use Brocante\Base\Database;

// Vérifier si l'utilisateur est connecté et est admin
if (!Brocanteur::estConnecte() || !Brocanteur::estAdmin()) {
    header('Location: index.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';
$message = '';
$erreur = '';

if ($id > 0 && $action === 'valider') {
    // Rediriger vers la page d'attribution d'emplacement pour ce brocanteur
    header('Location: attribuerEmplacement.php?id=' . $id . '&validate=1');
    exit;
} elseif ($id > 0 && $action === 'refuser') {
    // Refuser l'inscription (supprimer le compte)
    $db = new Database();
    
    // On vérifie d'abord que le brocanteur existe et qu'il n'est pas visible
    $brocanteur = $db->obtenirUn("SELECT * FROM Brocanteur WHERE bid = ? AND visible = 0", [$id]);
    
    if ($brocanteur) {
        // D'abord supprimer les références potentielles
        $db->executer("DELETE FROM Emplacement WHERE bid = ?", [$id]);
        
        // Ensuite, supprimer le brocanteur
        $result = $db->executer("DELETE FROM Brocanteur WHERE bid = ?", [$id]);
        
        if ($result) {
            $message = "L'inscription a été refusée et le compte a été supprimé.";
        } else {
            $erreur = "Une erreur est survenue lors de la suppression du compte.";
        }
    } else {
        $erreur = "Brocanteur introuvable ou déjà validé.";
    }
} elseif ($id === 0) {
    $erreur = "ID de brocanteur non spécifié.";
}

// Rediriger vers l'espace administrateur avec un message
if (!empty($message)) {
    header('Location: espaceAdministrateur.php?message=' . urlencode($message));
    exit;
} elseif (!empty($erreur)) {
    header('Location: espaceAdministrateur.php?erreur=' . urlencode($erreur));
    exit;
}
?> 