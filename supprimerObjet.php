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
$confirme = isset($_GET['confirme']) && $_GET['confirme'] === '1';

// Récupère l'objet
$objet = Objet::obtenirParId($oid);

// Vérifie la propriété
if (!$objet || $objet->bid !== $brocanteur->bid) {
    header('Location: espaceBrocanteur.php');
    exit;
}

// Si la suppression est confirmée
if ($confirme) {
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
}

// Sinon, affiche la page de confirmation
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante - Confirmer la suppression</title>
    <link rel="icon" href="images/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include 'inc/header.php'; ?>
<main>
    <section class="presentation center">
        <article class="center">
            <h1>Confirmer la suppression</h1>
        </article>
    </section>

    <section class="message-attention center">
        <p>Êtes-vous sûr de vouloir supprimer l'objet "<?php echo htmlspecialchars($objet->intitule); ?>" ?</p>
        <p>Cette action est irréversible.</p>
    </section>

    <section class="article center">
        <?php 
            if ($objet->image === null) {
                $image = "images/placeholder.png";
            } else {
                $image = "uploads/objets/" . htmlspecialchars($objet->image);
            }
        ?>
        <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($objet->intitule); ?>" style="max-width: 200px; margin: 20px auto;">
        <p><strong>Prix:</strong> <?php echo htmlspecialchars($objet->prix); ?> €</p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($objet->description); ?></p>
    </section>

    <section class="form-actions center">
        <a href="supprimerObjet.php?id=<?php echo $oid; ?>&confirme=1" class="btn btn-danger">Confirmer la suppression</a>
        <a href="modifierObjet.php?id=<?php echo $oid; ?>" class="btn">Annuler</a>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>
</html> 