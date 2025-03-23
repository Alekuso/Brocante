<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante - Contact</title>
    <link rel="icon" href="images/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include 'inc/header.php'; ?>
<main>
    <section class="center contactFirstImg">
        <img class="contactFirstImg" src="./images/contact.png" alt="Brocante rétro">
    </section>
    <section class="presentation center">
        <article class="center">
            <h3>Vous avez une question ?</h3>
            <h3>Remplissez ce formulaire et on vous répondra dans le plus bref délais !</h3>
        </article>
    </section>
    <section class="contactFormContainer bg-darkgray container">
        <article class="contactForm">
            <form method="POST" action="todo" class="column">
                <label for="nom">Nom</label>
                <input class="size-full" type="text" id="nom" name="nom" required>
                <label for="prenom">Prénom</label>
                <input class="size-full" type="text" id="prenom" name="prenom" required>
                <label for="email">Email</label>
                <input class="size-full" type="email" id="email" name="email" required>
                <label for="message">Message</label>
                <textarea id="message" name="message" required></textarea>
                <button type="submit" class="size-half">Envoyer</button>
            </form>
        </article>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>

<!-- Valide le CI malgré le fait qu'on n'utilise pas encore du code PHP -->
<?php

?>