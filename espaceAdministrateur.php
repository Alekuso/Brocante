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
            <h1>Bonjour, Philippe !</h1>
        </article>
    </section>
    <section class="articles size-half presentation">
        <article>
            <img class="size-full" src="images/placeholder.png" alt="article" />
            <a class="btn mar-2">Changer photo de profil</a>
        </article>
        <article>
            <h1>Nom Prénom</h1>
            <h3>Administrateur</h3>
            <p class="mar-tb-1">Description</p>
            <a class="btn mar-2">Modifier</a>
        </article>
    </section>
    <section class="presentation center">
        <h2 class="center">Inscriptions à valider</h2>
    </section>
    <section class="articles articles">
        <a href="brocanteur.php">
            <h4>Jean Philippe</h4>
            <p>Valider</p>
            <p>Définir Emplacement</p>
            <p>Refuser</p>
        </a>

        <a href="brocanteur.php">
            <h4>Benjamin Bonjour</h4>
            <p>Valider</p>
            <p>Définir Emplacement</p>
            <p>Refuser</p>
        </a>

        <a href="brocanteur.php">
            <h4>Andrea Rossi</h4>
            <p>Valider</p>
            <p>Définir Emplacement</p>
            <p>Refuser</p>
        </a>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>

<!-- Valide le CI malgré le fait qu'on n'utilise pas encore du code PHP -->
<?php

?>