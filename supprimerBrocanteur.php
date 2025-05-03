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

$message = '';
$erreur = '';

// Vérifier si un ID est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: espaceAdministrateur.php?erreur=Aucun+brocanteur+spécifié');
    exit;
}

$brocanteurId = (int)$_GET['id'];
$brocanteur = Brocanteur::obtenirParId($brocanteurId);

// Vérifier si le brocanteur existe
if (!$brocanteur) {
    header('Location: espaceAdministrateur.php?erreur=Brocanteur+introuvable');
    exit;
}

// Vérifier si le brocanteur a un emplacement attribué
if ($brocanteur->aEmplacementAttribue()) {
    header('Location: espaceAdministrateur.php?erreur=Impossible+de+supprimer+un+brocanteur+avec+un+emplacement+attribué');
    exit;
}

// Si une action de confirmation est demandée
if (isset($_GET['action']) && $_GET['action'] === 'confirmer') {
    if ($brocanteur->supprimer()) {
        header('Location: espaceAdministrateur.php?message=Brocanteur+supprimé+avec+succès');
        exit;
    } else {
        $erreur = "Une erreur est survenue lors de la suppression du brocanteur.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer un brocanteur - Supra Brocante</title>
    <link rel="icon" href="images/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include 'inc/header.php'; ?>
<main>
    <section class="form-container">
        <h1>Supprimer un brocanteur</h1>
        
        <?php if (!empty($message)): ?>
            <section class="message-succes"><?php echo htmlspecialchars($message); ?></section>
        <?php endif; ?>
        
        <?php if (!empty($erreur)): ?>
            <section class="message-erreur"><?php echo htmlspecialchars($erreur); ?></section>
        <?php endif; ?>
        
        <section class="confirmation-box">
            <h2>Êtes-vous sûr de vouloir supprimer ce brocanteur ?</h2>
            <article class="brocanteur-info">
                <p><strong>Nom :</strong> <?php echo htmlspecialchars($brocanteur->nom); ?></p>
                <p><strong>Prénom :</strong> <?php echo htmlspecialchars($brocanteur->prenom); ?></p>
                <p><strong>Email :</strong> <?php echo htmlspecialchars($brocanteur->courriel); ?></p>
            </article>
            <p class="warning">
                Attention : Cette action supprimera définitivement le brocanteur ainsi que tous ses objets.
                Cette action est irréversible !
            </p>
            <footer class="confirmation-actions">
                <a href="supprimerBrocanteur.php?id=<?php echo $brocanteurId; ?>&action=confirmer" class="btn danger pad-tb-1 pad-lr-2">Confirmer la suppression</a>
                <a href="espaceAdministrateur.php" class="btn pad-tb-1 pad-lr-2">Annuler</a>
            </footer>
        </section>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>
</html> 