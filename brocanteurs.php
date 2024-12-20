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

                <!--                "Vous" sera affiché si l'utilisateur est connecté.-->
                <a href="connexion.php">
                    Connexion
                </a>
            </li>
        </ul>
    </nav>
</header>
<main>
    <section id="presentation center">
        <article class="center">
            <h1>Brocanteurs</h1>
        </article>
    </section>
    <section id="contactFormContainer bg-fire container">
        <article class="contactForm">

            <form action="todo" method="get" class="bg-yum pad-2 rounded-sm column">
                <label for="nom">Nom</label>
                <input class="size-half" type="text" id="nom" name="nom" placeholder="Nom" required>
                <label for="prenom">Prénom</label>
                <input class="size-half" type="text" id="prenom" name="prenom" placeholder="Prénom" required>
                <button type="submit" class="size-half">Rechercher</button>
            </form>
        </article>
    </section>

    <section class="articles articles-grow">
        <a href="brocanteur.php">
            <img src="images/placeholder.png" alt="article" />
            <h4>Jean Philippe</h4>
            <p>Zone A</p>
            <p>Passionné de brocantes depuis 1934, j'admire les brocantes et je me suis pris d'affection pour les brocanteurs.
                J'ai réussi à faire mon rêve de toute une vie, être brocanteur.</p>
        </a>
        <a href="brocanteur.php">
            <img src="images/placeholder.png" alt="article" />
            <h4>Jean Philippe</h4>
            <p>Zone A</p>
            <p>Passionné de brocantes depuis 1934, j'admire les brocantes et je me suis pris d'affection pour les brocanteurs.
                J'ai réussi à faire mon rêve de toute une vie, être brocanteur.</p>
        </a>
        <a href="brocanteur.php">
            <img src="images/placeholder.png" alt="article" />
            <h4>Jean Philippe</h4>
            <p>Zone A</p>
            <p>Passionné de brocantes depuis 1934, j'admire les brocantes et je me suis pris d'affection pour les brocanteurs.
                J'ai réussi à faire mon rêve de toute une vie, être brocanteur.</p>
        </a>

    </section>
</main>
<footer>
    <p>Brocante - 2024 ~ 2025</p>
</footer>
</body>

</html>

<!-- Valide le CI malgré le fait qu'on n'utilise pas encore du code PHP -->
<?php

?>