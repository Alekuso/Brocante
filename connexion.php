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
<?php include 'inc/header.php'; ?>
<main>
    <section class="presentation center">
        <article class="center">
            <h1>Connexion</h1>
        </article>
    </section>
    <section class="contactFormContainer bg-darkgray container">
        <article class="contactForm">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" class="column">
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
<?php include 'inc/footer.php'; ?>
</body>

</html>

<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = filter_var($_POST["password"], FILTER_SANITIZE_STRING);

    // select * from Brocanteur where email = $email and (verify: password = $password HASH)
}
?>