<?php
include_once 'php/Database.php';
include_once 'php/Objet.php';
include_once 'php/Brocanteur.php';
include_once 'php/Categorie.php';
include_once 'php/Zone.php';

use Brocante\Base\Database;
use Brocante\Modele\Objet;
use Brocante\Modele\Brocanteur;
use Brocante\Modele\Categorie;
use Brocante\Modele\Zone;

// Récupérer le brocanteur demandé
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$brocanteur = Brocanteur::obtenirParId($id);

// Si le brocanteur n'existe pas, redirection vers la page d'accueil
if (!$brocanteur) {
    header('Location: index.php');
    exit;
}

// Récupérer la zone du brocanteur
$zone = $brocanteur->obtenirZone();

// Récupérer l'emplacement du brocanteur
$emplacement = $brocanteur->obtenirEmplacement();

// Récupérer les objets du brocanteur
$objets = $brocanteur->obtenirObjets();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante - <?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?></title>
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
            if ($brocanteur->photo == null) {
                $image = "images/placeholder.png";
            } else {
                $image = "uploads/brocanteurs/" . htmlspecialchars($brocanteur->photo);
            }
            ?>
            <img class="size-full" src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?>" />
        </article>
        <article>
            <h1><?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?></h1>
            <?php 
            if ($zone) {
                echo '<h3>' . htmlspecialchars($zone->nom) . '</h3>';
            }
            
            if ($emplacement) {
                echo '<p class="emplacement">Emplacement: ' . htmlspecialchars($emplacement->code) . '</p>';
            } else {
                echo '<p class="emplacement">Emplacement non attribué</p>';
            }
            ?>
            
            <p class="mar-tb-1"><?php echo htmlspecialchars($brocanteur->description); ?></p>
        </article>
    </section>
    <section class="presentation center">
        <h2 class="center">Articles de <?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?></h2>
    </section>
    <section class="articles articles-grow">
        <?php 
        if (empty($objets)) {
            echo '<p class="center">Ce brocanteur n\'a pas encore d\'objets à vendre.</p>';
        } else {
            foreach ($objets as $article) {
                $categorie = $article->obtenirCategorie();
                
                echo '<a href="produit.php?id=' . htmlspecialchars($article->oid) . '">';
                
                if ($article->image === null) {
                    $image = "images/placeholder.png";
                } else {
                    $image = "uploads/objets/" . htmlspecialchars($article->image);
                }
                
                echo '<img src="' . $image . '" alt="' . htmlspecialchars($article->intitule) . '" />';
                echo '<h4>' . htmlspecialchars($article->intitule) . '</h4>';
                echo '<p>' . htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom);
                echo $zone ? ' - ' . htmlspecialchars($zone->nom) : '';
                echo '</p>';
                echo '<p>' . htmlspecialchars($article->description) . '</p>';
                
                if ($categorie) {
                    echo '<ul>';
                    echo '<li class="pad-lr-1 flex">';
                    echo '<p class="center">';
                    echo htmlspecialchars($categorie->intitule);
                    echo '</p>';
                    echo '</li>';
                    echo '</ul>';
                }
                
                echo '<h3 class="prix">' . htmlspecialchars($article->prix) . '€</h3>';
                echo '</a>';
            }
        }
        ?>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html> 