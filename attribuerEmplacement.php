<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante - Attribution d'Emplacement</title>
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
                <a href="espaceAdministrateur.php">
                    Espace
                </a>
            </li>
        </ul>
    </nav>
</header>
<main>
    <section id="presentation center">
        <article class="center">
            <h1>Attribuer une zone à un brocanteur</h1>
        </article>
    </section>
    <section class="contactFormContainer bg-darkgray container">
        <article class="contactForm">
            <form method="POST" action="todo" class="column">
                <label for="nom">Nom</label>
                <input class="size-full" type="text" id="nom" name="nom" required>
                <label for="prenom">Prénom</label>
                <input class="size-full" type="text" id="prenom" name="prenom" required>

                <label for="zone">Zone</label>
                <select class="size-full" id="zone" name="zone" required>
                    <option value="">-- Sélectionnez une zone --</option>
                    <option value="zone1">Zone A</option>
                    <option value="zone2">Zone B</option>
                    <option value="zone3">Zone C</option>
                    <option value="zone3">Zone D</option>
                </select>

                <button type="submit" class="size-half">Envoyer</button>
            </form>
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