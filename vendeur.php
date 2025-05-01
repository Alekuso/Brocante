<?php
include_once 'php/Database.php';
include_once 'php/Objet.php';
include_once 'php/Brocanteur.php';
include_once 'php/Categorie.php';
include_once 'php/Zone.php';

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
            <?php if ($zone): ?>
                <h3><?php echo htmlspecialchars($zone->nom); ?></h3>
            <?php endif; ?>
            
            <?php if ($emplacement): ?>
                <p class="emplacement">Emplacement: <?php echo htmlspecialchars($emplacement->code); ?></p>
            <?php else: ?>
                <p class="emplacement">Emplacement non attribué</p>
            <?php endif; ?>
            
            <p class="mar-tb-1"><?php echo htmlspecialchars($brocanteur->description); ?></p>
        </article>
    </section>
    <section class="presentation center">
        <h2 class="center">Articles de <?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?></h2>
    </section>
    <section class="articles articles-grow">
        <?php if (empty($objets)): ?>
            <p class="center">Ce brocanteur n'a pas encore d'objets à vendre.</p>
        <?php else: ?>
            <?php foreach ($objets as $article): 
                $categorie = $article->obtenirCategorie();
            ?>
                <a href="produit.php?id=<?php echo htmlspecialchars($article->oid); ?>">
                    <?php
                    if ($article->image === null) {
                        $image = "images/placeholder.png";
                    } else {
                        $image = "uploads/objets/" . htmlspecialchars($article->image);
                    }
                    ?>
                    <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($article->intitule); ?>" />
                    <h4><?php echo htmlspecialchars($article->intitule); ?></h4>
                    <p><?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?><?php echo $zone ? ' - ' . htmlspecialchars($zone->nom) : ''; ?></p>
                    <p><?php echo htmlspecialchars($article->description); ?></p>
                    <?php if ($categorie): ?>
                        <ul>
                            <li class="pad-lr-1 flex">
                                <p class="center">
                                    <?php echo htmlspecialchars($categorie->intitule); ?>
                                </p>
                            </li>
                        </ul>
                    <?php endif; ?>
                    <h3 class="prix"><?php echo htmlspecialchars($article->prix); ?>€</h3>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html> 