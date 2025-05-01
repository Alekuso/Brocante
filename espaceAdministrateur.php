<?php
include_once 'php/Brocanteur.php';
include_once 'php/Database.php';

// Vérifier si l'utilisateur est connecté et est admin
if (!Brocanteur::estConnecte() || !Brocanteur::estAdmin()) {
    header('Location: index.php');
    exit;
}

// Récupérer l'admin connecté
$admin = Brocanteur::obtenirConnecte();

// Récupérer les brocanteurs à valider (non visibles)
$db = new Database();
$resultats = $db->obtenirTous("SELECT * FROM Brocanteur WHERE visible = 0 AND est_administrateur = 0");

$brocanteurs = [];
foreach ($resultats as $donnees) {
    $brocanteurs[] = new Brocanteur($donnees);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante - Espace Administrateur</title>
    <link rel="icon" href="images/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include 'inc/header.php'; ?>
<main>
    <section class="presentation">
        <article class="center">
            <h1>Bonjour, <?php echo htmlspecialchars($admin->prenom); ?> !</h1>
        </article>
    </section>
    <section class="articles size-half presentation">
        <article>
            <?php 
            if ($admin->photo === null) {
                $image = "images/placeholder.png";
            } else {
                $image = "uploads/brocanteurs/" . htmlspecialchars($admin->photo);
            }
            ?>
            <img class="size-full" src="<?php echo $image; ?>" alt="Photo de profil" />
        </article>
        <article>
            <h1><?php echo htmlspecialchars($admin->prenom . ' ' . $admin->nom); ?></h1>
            <h3>Administrateur</h3>
            <p class="mar-tb-1"><?php echo htmlspecialchars($admin->description); ?></p>
            
            <a href="modifierProfil.php" class="btn mar-2">Modifier profil</a>
        </article>
    </section>
    <section class="presentation center">
        <h2 class="center">Inscriptions à valider</h2>
    </section>
    <section class="articles articles">
        <?php if (empty($brocanteurs)): ?>
            <p class="center">Aucune inscription en attente.</p>
        <?php else: ?>
            <?php foreach ($brocanteurs as $brocanteur): ?>
                <a href="vendeur.php?id=<?php echo htmlspecialchars($brocanteur->bid); ?>">
                    <h4><?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?></h4>
                    <p>Valider</p>
                    <p><a href="attribuerEmplacement.php?id=<?php echo htmlspecialchars($brocanteur->bid); ?>">Définir Emplacement</a></p>
                    <p>Refuser</p>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>