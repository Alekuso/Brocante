<?php
include_once 'php/Brocanteur.php';
include_once 'php/Database.php';

use Brocante\Base\Database;
use Brocante\Modele\Brocanteur;

// Vérifie la connexion
if (!Brocanteur::estConnecte()) {
    header('Location: connexion.php');
    exit;
}

// Récupère le brocanteur
$brocanteur = Brocanteur::obtenirConnecte();

// Traite les messages
$message = isset($_GET['message']) ? $_GET['message'] : '';
$succes = '';
$erreur = '';

if ($message === 'suppression_reussie') {
    $succes = "L'objet a été supprimé avec succès";
} elseif ($message === 'erreur_suppression') {
    $erreur = "Une erreur est survenue lors de la suppression";
}

// Récupère la zone et l'emplacement
$zone = $brocanteur->obtenirZone();
$emplacement = $brocanteur->obtenirEmplacement();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante - Espace Brocanteur</title>
    <link rel="icon" href="images/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include 'inc/header.php'; ?>
<main>
    <?php 
    if (!empty($erreur)) {
        echo "<section class=\"message-erreur\">" . htmlspecialchars($erreur) . "</section>";
    }
    
    if (!empty($succes)) {
        echo "<section class=\"message-succes\">" . htmlspecialchars($succes) . "</section>";
    }
    ?>
    
    <section class="presentation">
        <article class="center">
            <h1>Bonjour, <?php echo htmlspecialchars($brocanteur->prenom); ?> !</h1>
        </article>
    </section>
    
    <section class="articles size-half presentation">
        <article>
            <?php 
            if ($brocanteur->photo === null) {
                $image = "images/placeholder.png";
            } else {
                $image = "uploads/brocanteurs/" . htmlspecialchars($brocanteur->photo);
            }
            ?>
            <img class="size-full" src="<?php echo $image; ?>" alt="Photo de profil" />
        </article>
        <article>
            <h1><?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?></h1>
            
            <?php 
            if ($zone) {
                echo "<h3>" . htmlspecialchars($zone->nom) . "</h3>";
            }
            
            if ($emplacement) {
                echo "<p class=\"emplacement\">Emplacement: " . htmlspecialchars($emplacement->code) . "</p>";
            } else {
                echo "<p>Emplacement non attribué</p>";
            }
            ?>
            
            <p class="mar-tb-1"><?php echo htmlspecialchars($brocanteur->description); ?></p>
            
            <a href="modifierProfil.php" class="btn mar-2">Modifier profil</a>
        </article>
    </section>
    
    <section class="presentation center">
        <h2 class="center">Vos articles</h2>
    </section>
    
    <section class="articles articles-grow">
        <?php 
        $objets = $brocanteur->obtenirObjets();
        if (empty($objets)) {
            echo "<p class=\"center\">Vous n'avez pas encore d'articles à vendre</p>";
        } else {
            foreach ($objets as $objet) {
                echo "<article class=\"objet-card\">";
                
                if ($objet->image === null) {
                    $image = "images/placeholder.png";
                } else {
                    $image = "uploads/objets/" . htmlspecialchars($objet->image);
                }
                
                echo "<a href=\"produit.php?id=" . htmlspecialchars($objet->oid) . "\" class=\"card-content\">";
                echo "<img src=\"" . $image . "\" alt=\"" . htmlspecialchars($objet->intitule) . "\" />";
                echo "<h4>" . htmlspecialchars($objet->intitule) . "</h4>";
                echo "<p>" . htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom) . " " . ($zone ? '- ' . htmlspecialchars($zone->nom) : '') . "</p>";
                echo "<p>" . htmlspecialchars($objet->description) . "</p>";
                
                $categorie = $objet->obtenirCategorie();
                if ($categorie) {
                    echo "<ul>";
                    echo "<li class=\"pad-lr-1 flex\">";
                    echo "<p class=\"center\">";
                    echo htmlspecialchars($categorie->intitule);
                    echo "</p>";
                    echo "</li>";
                    echo "</ul>";
                }
                
                echo "<h3 class=\"prix\">" . htmlspecialchars($objet->prix) . "€</h3>";
                echo "</a>";
                echo "<footer class=\"card-actions\">";
                echo "<a href=\"modifierObjet.php?id=" . htmlspecialchars($objet->oid) . "\" class=\"btn-small\">Modifier</a>";
                echo "</footer>";
                echo "</article>";
            }
        }
        ?>

        <a href="ajouterObjet.php" class="articles-add btn center">
            Ajouter article
        </a>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>