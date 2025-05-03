<?php
include_once 'php/Database.php';
include_once 'php/Objet.php';
include_once 'php/Brocanteur.php';
include_once 'php/Categorie.php';

use Brocante\Base\Database;
use Brocante\Modele\Objet;
use Brocante\Modele\Brocanteur;
use Brocante\Modele\Categorie;

// Récupérer l'objet demandé
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$objet = Objet::obtenirParId($id);

// Si l'objet n'existe pas, redirection vers la page d'accueil
if (!$objet) {
    header('Location: index.php');
    exit;
}

// Récupérer le brocanteur et la catégorie
$brocanteur = $objet->obtenirBrocanteur();
$categorie = $objet->obtenirCategorie();
$zone = $brocanteur ? $brocanteur->obtenirZone() : null;
$emplacement = $brocanteur ? $brocanteur->obtenirEmplacement() : null;
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante - <?php echo htmlspecialchars($objet->intitule); ?></title>
    <link rel="icon" href="images/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include 'inc/header.php'; ?>
<main>
    <section class="articles size-half presentation">
        <article>
            <?php
            if ($objet->image === null) {
                $image = "images/placeholder.png";
            } else {
                $image = "uploads/objets/" . htmlspecialchars($objet->image);
            }
            ?>
            <img class="size-full" src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($objet->intitule); ?>" />
        </article>
        <article>
            <h1><?php echo htmlspecialchars($objet->intitule); ?></h1>
            <?php 
            if ($brocanteur && $zone) {
                echo '<h3 class="mar-O pad-0">';
                echo '<a class="mar-0 pad-0" href="vendeur.php?id=' . htmlspecialchars($brocanteur->bid) . '">';
                echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom) . ' - ' . htmlspecialchars($zone->nom);
                echo '</a>';
                echo '</h3>';
                
                if ($emplacement) {
                    echo '<p class="emplacement">Emplacement: ' . htmlspecialchars($emplacement->code) . '</p>';
                }
            }
            ?>
            <h4><?php echo htmlspecialchars($objet->prix); ?>€</h4>
            <?php 
            if ($categorie) {
                echo '<ul>';
                echo '<li class="pad-lr-1 flex">';
                echo '<p class="center">';
                echo htmlspecialchars($categorie->intitule);
                echo '</p>';
                echo '</li>';
                echo '</ul>';
            }
            ?>
            <p><?php echo htmlspecialchars($objet->description); ?></p>
        </article>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html> 