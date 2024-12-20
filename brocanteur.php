<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante - Brocanteur</title>
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
            <h1>Jean Philippe</h1>
            <h3>Zone A</h3>
            <p class="mar-tb-1">Passionné de brocantes depuis 1934, j'admire les brocantes et je me suis pris d'affection pour les brocanteurs.
            J'ai réussi à faire mon rêve de toute une vie, être brocanteur.</p>
        </article>
    </section>
    <section class="presentation center">
        <h2 class="center">Articles de Jean Philippe</h2>
    </section>
    <section class="articles articles-grow">
        <a href="objet.php">
            <img src="images/placeholder.png" alt="article" />
            <h4>Article 1</h4>
            <p>Brocanteur - Zone A</p>
            <ul>
                <li class="pad-lr-1 flex">
                    <p class="center">
                        cat1
                    </p>
                </li>
                <li class="pad-lr-1 flex">
                    <p class="center">
                        cat2
                    </p>
                </li>
                <li class="pad-lr-1 flex">
                    <p class="center">
                        cat3
                    </p>
                </li>
            </ul>
            <p>12.50€</p>
        </a>
        <a href="objet.php">
            <img src="images/placeholder.png" alt="article" />
            <h4>Article 2</h4>
            <p>Brocanteur - Zone D</p>
            <ul>
                <li class="pad-lr-1 flex">
                    <p class="center">
                        cat1
                    </p>
                </li>
                <li class="pad-lr-1 flex">
                    <p class="center">
                        cat2
                    </p>
                </li>
            </ul>
            <p>4.99€</p>
        </a>
        <a href="objet.php">
            <img src="images/placeholder.png" alt="article" />
            <h4>Article 3</h4>
            <p>Brocanteur - Zone B</p>
            <ul>
                <li class="pad-lr-1 flex">
                    <p class="center">
                        cat1
                    </p>
                </li>
            </ul>
            <p>2.99€</p>
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