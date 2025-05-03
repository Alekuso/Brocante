<?php
include_once 'php/Brocanteur.php';
include_once 'php/Database.php';

use Brocante\Modele\Brocanteur;
use Brocante\Base\Database;

// Vérifie si l'utilisateur est connecté et est admin
if (!Brocanteur::estConnecte() || !Brocanteur::estAdmin()) {
    header('Location: index.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';
$message = '';
$erreur = '';

if ($id > 0 && $action === 'valider') {
    // Redirige vers la page d'attribution d'emplacement
    header('Location: attribuerEmplacement.php?id=' . $id . '&validate=1');
    exit;
} elseif ($id > 0 && $action === 'refuser') {
    // Refuse l'inscription
    $db = Database::getInstance();
    
    // Vérifie que le brocanteur existe et n'est pas visible
    $brocanteur = $db->obtenirUn("SELECT * FROM Brocanteur WHERE bid = ? AND visible = 0", [$id]);
    
    if ($brocanteur) {
        // Supprime les références
        $db->executer("DELETE FROM Emplacement WHERE bid = ?", [$id]);
        
        // Supprime le brocanteur
        $result = $db->executer("DELETE FROM Brocanteur WHERE bid = ?", [$id]);
        
        if ($result) {
            $message = "L'inscription a été refusée";
        } else {
            $erreur = "Erreur lors de la suppression du compte";
        }
    } else {
        $erreur = "Brocanteur introuvable ou déjà validé";
    }
} elseif ($id === 0) {
    $erreur = "ID de brocanteur non spécifié";
}

// Redirige vers l'espace administrateur
if (!empty($message)) {
    header('Location: espaceAdministrateur.php?message=' . urlencode($message));
    exit;
} elseif (!empty($erreur)) {
    header('Location: espaceAdministrateur.php?erreur=' . urlencode($erreur));
    exit;
}
?> 