<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante - Inscription</title>
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
            <h1>Inscrivez-vous à la foire aux puces</h1>
            <h1>Frais d'inscription : 20€</h1>
        </article>
    </section>
    <section class="contactFormContainer bg-darkgray container">
        <article class="contactForm">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" class="column">
                <label for="nom">Nom</label>
                <input class="size-full" type="text" id="nom" name="nom" required>
                <label for="prenom">Prénom</label>
                <input class="size-full" type="text" id="prenom" name="prenom" required>
                <label for="email">Email</label>
                <input class="size-full" type="email" id="email" name="email" required>
                <label for="password">Mot de passe</label>
                <input class="size-full" type="password" id="password" name="password" required>
                <label for="passwordConfirm">Confirmer le mot de passe</label>
                <input class="size-full" type="password" id="passwordConfirm" name="passwordConfirm" required>
                <button type="submit" class="size-half">Créer un compte</button>
            </form>
        </article>
    </section>
    <section>
        <article class="center">
            <p>Vous avez déjà un compte ? <a class="underline" href="connexion.php">Connectez-vous</a></p>
        </article>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>

<?php
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = filter_var($_POST["nom"], FILTER_SANITIZE_STRING);
        $prenom = filter_var($_POST["prenom"], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        $password = filter_var($_POST["password"], FILTER_SANITIZE_STRING);
        $passwordConfirm = filter_var($_POST["passwordConfirm"], FILTER_SANITIZE_STRING);

        // Vérifier si les mots de passe correspondent
        if($password === $passwordConfirm) {
            // 1. Regarde si l'adresse email existe dans la base de données
            // - Si oui, l'utilisateur est déjà inscrit + ABORT
            // Sinon, inscription

            // select * from Brocanteur where email = $email
            // IF COUNT(*) > 0
            //      L'utilisateur est déjà inscrit.
            // ELSE
            //     HASH PASSWORD
            //     INSERT INTO Brocanteur . . . TODO
        }
    }
?>