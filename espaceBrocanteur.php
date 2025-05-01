<?php
include_once 'php/Brocanteur.php';
include_once 'php/Database.php';

// Vérifier si l'utilisateur est connecté
if (!Brocanteur::estConnecte()) {
    header('Location: connexion.php');
    exit;
}

// Récupérer le brocanteur connecté
$brocanteur = Brocanteur::obtenirConnecte();

// Traiter les messages
$message = isset($_GET['message']) ? $_GET['message'] : '';
$succes = '';
$erreur = '';

if ($message === 'suppression_reussie') {
    $succes = "L'objet a été supprimé avec succès.";
} elseif ($message === 'erreur_suppression') {
    $erreur = "Une erreur est survenue lors de la suppression de l'objet.";
}

// Récupérer la zone et l'emplacement
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
    <?php if (!empty($erreur)): ?>
        <div class="erreur-message"><?php echo htmlspecialchars($erreur); ?></div>
    <?php endif; ?>
    <?php if (!empty($succes)): ?>
        <div class="succes-message"><?php echo htmlspecialchars($succes); ?></div>
    <?php endif; ?>
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
            
            <?php if ($zone): ?>
                <h3><?php echo htmlspecialchars($zone->nom); ?></h3>
            <?php endif; ?>
            
            <?php if ($emplacement): ?>
                <p class="emplacement">Emplacement: <?php echo htmlspecialchars($emplacement->code); ?></p>
            <?php else: ?>
                <p>Emplacement non attribué</p>
            <?php endif; ?>
            
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
        if (empty($objets)): 
        ?>
            <p class="center">Vous n'avez pas encore d'articles à vendre.</p>
        <?php else: ?>
            <?php foreach ($objets as $objet): ?>
                <a href="produit.php?id=<?php echo htmlspecialchars($objet->oid); ?>">
                    <?php 
                    if ($objet->image === null) {
                        $image = "images/placeholder.png";
                    } else {
                        $image = "uploads/objets/" . htmlspecialchars($objet->image);
                    }
                    ?>
                    <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($objet->intitule); ?>" />
                    <h4><?php echo htmlspecialchars($objet->intitule); ?></h4>
                    <p><?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?> <?php echo $zone ? '- ' . htmlspecialchars($zone->nom) : ''; ?></p>
                    <p><?php echo htmlspecialchars($objet->description); ?></p>
                    <?php 
                    $categorie = $objet->obtenirCategorie();
                    if ($categorie): 
                    ?>
                        <ul>
                            <li class="pad-lr-1 flex">
                                <p class="center">
                                    <?php echo htmlspecialchars($categorie->intitule); ?>
                                </p>
                            </li>
                        </ul>
                    <?php endif; ?>
                    <h3 class="prix"><?php echo htmlspecialchars($objet->prix); ?>€</h3>
                    <p><a href="modifierObjet.php?id=<?php echo htmlspecialchars($objet->oid); ?>" class="btn-small">Modifier</a></p>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>

        <a href="ajouterObjet.php" class="articles-add btn center">
            Ajouter article
        </a>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>