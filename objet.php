<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante - Objet</title>
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
    <section class="articles size-half presentation">
        <article>
            <img class="size-full" src="images/placeholder.png" alt="article" />
        </article>
        <article>
            <h1>Article Super !</h1>
            <h3 class="mar-O pad-0"><a class="mar-0 pad-0" href="brocanteur.php">Jean Philippe - Zone A</a></h3>
            <h4>12.50€</h4>
            <ul>
                <li class="pad-lr-1 flex">
                    <p class="center">
                        jeu
                    </p>
                </li>
                <li class="pad-lr-1 flex">
                    <p class="center">
                        ancien
                    </p>
                </li>
                <li class="pad-lr-1 flex">
                    <p class="center">
                        oeuf
                    </p>
                </li>
            </ul>
        </article>
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