<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante - Connexion</title>
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
    <section class="presentation center">
        <article class="center">
            <h1>Connexion</h1>
        </article>
    </section>
    <section class="contactFormContainer bg-darkgray container">
        <article class="contactForm">
            <form method="POST" action="todo" class="column">
                <label for="email">Email</label>
                <input class="size-full" type="email" id="email" name="email" required>
                <label for="password">Mot de passe</label>
                <input class="size-full" type="password" id="password" name="password" required>
                <button type="submit" class="size-half">Créer un compte</button>
            </form>
        </article>
    </section>
    <section>
        <article class="center">
            <p>Vous voulez vendre ? <a class="underline" href="inscription.php">Inscrivez-vous</a></p>
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