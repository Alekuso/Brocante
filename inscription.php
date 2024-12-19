<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante</title>
    <link rel="icon" href="./res/img/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<style>
    /*    * {
            box-sizing: border-box;
            outline: 1px solid limegreen !important;
        }*/
</style>
<body>
<header>
    <a href="index.php">
        <img src="./res/img/icon.png" alt="Logo Brocante">
    </a>
    <nav>
        <ul>
            <li class="btn">
                <a href="searchBroc.php">
                    Chercher Brocanteur
                </a>
            </li>
            <li class="btn">
                <a href="searchObj.php">
                    Chercher Objet
                </a>
            </li>
            <li class="btn">
                <a href="contact.php">
                    Nous Contacter
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
            <h1>Inscription</h1>
        </article>
    </section>
    <section class="contactFormContainer bg-yum container">
        <article class="contactForm">
            <form method="POST" action="LIEN A VOIR DANS LE FUTURE WAHOO">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" required>
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" required>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
                <label for="passwordConfirm">Confirmer le mot de passe</label>
                <input type="password" id="passwordConfirm" name="passwordConfirm" required>
                <button type="submit">Créer un compte</button>
            </form>
        </article>
    </section>
    <section>
        <article class="center">
            <p>Vous avez déjà un compte ? <a class="underline" href="connexion.php">Connectez-vous</a></p>
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