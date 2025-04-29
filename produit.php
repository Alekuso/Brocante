<?php
include_once 'php/Database.php';
include_once 'php/Objet.php';
include_once 'php/Brocanteur.php';
include_once 'php/Categorie.php';

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
            if ($objet->image == null) {
                $image = "images/placeholder.png";
            } else {
                $image = "uploads/" . htmlspecialchars($objet->image);
            }
            ?>
            <img class="size-full" src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($objet->intitule); ?>" />
        </article>
        <article>
            <h1><?php echo htmlspecialchars($objet->intitule); ?></h1>
            <?php if ($brocanteur && $zone): ?>
                <h3 class="mar-O pad-0">
                    <a class="mar-0 pad-0" href="vendeur.php?id=<?php echo htmlspecialchars($brocanteur->bid); ?>">
                        <?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?> 
                        - <?php echo htmlspecialchars($zone->nom); ?>
                    </a>
                </h3>
            <?php endif; ?>
            <h4><?php echo htmlspecialchars($objet->prix); ?>€</h4>
            <?php if ($categorie): ?>
            <ul>
                <li class="pad-lr-1 flex">
                    <p class="center">
                        <?php echo htmlspecialchars($categorie->intitule); ?>
                    </p>
                </li>
            </ul>
            <?php endif; ?>
            <p><?php echo htmlspecialchars($objet->description); ?></p>
        </article>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html> 