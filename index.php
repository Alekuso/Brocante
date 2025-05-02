<?php
include_once 'php/Database.php';
include_once 'php/Objet.php';
include_once 'php/Brocanteur.php';
include_once 'php/Categorie.php';

use Brocante\Base\Database;
use Brocante\Modele\Objet;
use Brocante\Modele\Brocanteur;
use Brocante\Modele\Categorie;

$objets = Objet::obtenirAleatoires();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante</title>
    <link rel="icon" href="images/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include 'inc/header.php'; ?>
<main>
    <section id="presentation">
        <article class="indexpresent">
            <p>Votre brocante annuelle porte sur le thème du rétro !</p>
            <p>Du 10 au 12 mars</p>
            <p>Lieu : Rue Grand Pré, Flémalle 4400</p>
            <p>Frais d'inscription brocanteur : 20€</p>
            <ul class="container center column">
                <li>
                    <a class="btn" href="inscription.php">
                        S'inscrire
                    </a>
                </li>
                <li>
                    <a class="btn" href="contact.php">
                        Nous contacter
                    </a>
                </li>
            </ul>
        </article>
        <img class="zone" src="images/zone_A.png" alt="Photo représentative de l'endroit où se trouve la zone A de la brocante" />
    </section>
    <section id="brocanteurs">
        <article class="center">
            <h1>Objets aléatoires</h1>
        </article>
    </section>
    <section class="articles articles-grow">
        <?php foreach ($objets as $article):
            $brocanteur = $article->obtenirBrocanteur();
            $categorie = $article->obtenirCategorie();
            
            if ($article->image === null) {
                $image = "images/placeholder.png";
            } else {
                $image = "uploads/objets/" . htmlspecialchars($article->image);
            }
        ?>
            <a href="produit.php?id=<?php echo htmlspecialchars($article->oid); ?>">
                <img class="center" src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($article->intitule); ?>" />
                <h4><?php echo htmlspecialchars($article->intitule); ?></h4>
                
                <?php if ($brocanteur): 
                    $zone = $brocanteur->obtenirZone();
                    $zoneInfo = $zone ? " - " . htmlspecialchars($zone->nom) : "";
                ?>
                    <p><?php echo htmlspecialchars($brocanteur->prenom . " " . $brocanteur->nom) . $zoneInfo; ?></p>
                <?php endif; ?>
                
                <p><?php echo htmlspecialchars($article->description); ?></p>
                
                <?php if ($categorie): ?>
                    <ul class="pad-lr-1 flex">
                        <li class="pad-lr-1 flex"><p class="center"><?php echo htmlspecialchars($categorie->intitule); ?></p></li>
                    </ul>
                <?php endif; ?>
                
                <h3 class="prix"><?php echo htmlspecialchars($article->prix); ?>€</h3>
            </a>
        <?php endforeach; ?>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html> 