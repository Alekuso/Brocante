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
<header>
    <a href="index.php">
        <img id="icon" src="images/icon.png" alt="Logo Brocante">
    </a>
    <nav>
        <ul>
            <li class="btn">
                <a href="brocanteurs.php">
                    Brocanteurs
                </a>
            </li>
            <li class="btn">
                <a href="objets.php">
                    Objets
                </a>
            </li>
            <li class="btn">
                <a href="contact.php">
                    Contacter
                </a>
            </li>
            <li class="btn">

                <!-- On assume que l'utilisateur est connecté lors de la visite de cette page. -->
                <a href="espaceBrocanteur.php">
                    Profil
                </a>
            </li>
        </ul>
    </nav>
</header>
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
<footer>
    <p>Brocante - 2024 ~ 2025</p>
</footer>
</body>

</html>

<!-- Valide le CI malgré le fait qu'on n'utilise pas encore du code PHP -->
<?php

?>