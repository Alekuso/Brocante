<?php
include_once 'php/Database.php';
include_once 'php/Brocanteur.php';
include_once 'php/Zone.php';

// Initialisation des paramètres de recherche
$nom = isset($_GET['nom']) ? trim($_GET['nom']) : '';
$prenom = isset($_GET['prenom']) ? trim($_GET['prenom']) : '';

// Si la recherche est active, récupérer les brocanteurs correspondants
if (!empty($nom) || !empty($prenom)) {
    $brocanteurs = Brocanteur::rechercher($nom, $prenom);
    $zones = []; // Pas besoin de zones car on affiche juste les résultats de recherche
} else {
    // Sinon, récupérer toutes les zones avec leurs brocanteurs
    $zones = Zone::obtenirToutes();
    $brocanteurs = null;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante - Brocanteurs</title>
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
            <h1>Brocanteurs</h1>
        </article>
    </section>
    <section id="contactFormContainer" class="container">
        <article class="contactForm">

            <form action="brocanteurs.php" method="get" class="bg-darkgray desk-pad-2 rounded-sm column">
                <label for="nom">Nom</label>
                <input class="size-half" type="text" id="nom" name="nom" placeholder="Nom" value="<?php echo htmlspecialchars($nom); ?>">
                <label for="prenom">Prénom</label>
                <input class="size-half" type="text" id="prenom" name="prenom" placeholder="Prénom" value="<?php echo htmlspecialchars($prenom); ?>">
                <button type="submit" class="size-half">Rechercher</button>
            </form>
        </article>
    </section>
    <section>
        <?php if ($brocanteurs !== null): ?>
            <!-- Affichage des résultats de recherche -->
            <h3 class="flex center">Résultats de recherche</h3>
            <article class="articles articles-grow brocanteurs-grid">
                <?php if (empty($brocanteurs)): ?>
                    <p class="center">Aucun brocanteur ne correspond à votre recherche.</p>
                <?php else: ?>
                    <?php foreach ($brocanteurs as $brocanteur): 
                        $zone = $brocanteur->obtenirZone();
                        $emplacement = $brocanteur->obtenirEmplacement();
                    ?>
                        <a href="vendeur.php?id=<?php echo htmlspecialchars($brocanteur->bid); ?>" class="center brocanteur-card">
                            <?php
                            if ($brocanteur->photo === null) {
                                $image = "images/placeholder.png";
                            } else {
                                $image = "uploads/brocanteurs/" . htmlspecialchars($brocanteur->photo);
                            }
                            ?>
                            <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?>" />
                            <h4><?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?></h4>
                            <?php if ($zone): ?>
                                <p><?php echo htmlspecialchars($zone->nom); ?></p>
                            <?php endif; ?>
                            <?php if ($emplacement): ?>
                                <p class="emplacement">Emplacement: <?php echo htmlspecialchars($emplacement->code); ?></p>
                            <?php endif; ?>
                            <p><?php echo htmlspecialchars($brocanteur->description); ?></p>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </article>
        <?php else: ?>
            <!-- Affichage par zones -->
            <?php foreach ($zones as $zone): 
                $brocanteurs = $zone->obtenirBrocanteurs();
                if (empty($brocanteurs)) continue;
            ?>
                <h3 class="flex center"><?php echo htmlspecialchars($zone->nom); ?></h3>
                <article class="articles articles-grow brocanteurs-grid">
                    <?php foreach ($brocanteurs as $brocanteur): 
                        $emplacement = $brocanteur->obtenirEmplacement();
                    ?>
                        <a href="vendeur.php?id=<?php echo htmlspecialchars($brocanteur->bid); ?>" class="center brocanteur-card">
                            <?php
                            if ($brocanteur->photo === null) {
                                $image = "images/placeholder.png";
                            } else {
                                $image = "uploads/brocanteurs/" . htmlspecialchars($brocanteur->photo);
                            }
                            ?>
                            <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?>" />
                            <h4><?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?></h4>
                            <p><?php echo htmlspecialchars($zone->nom); ?></p>
                            <?php if ($emplacement): ?>
                                <p class="emplacement">Emplacement: <?php echo htmlspecialchars($emplacement->code); ?></p>
                            <?php endif; ?>
                            <p><?php echo htmlspecialchars($brocanteur->description); ?></p>
                        </a>
                    <?php endforeach; ?>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>