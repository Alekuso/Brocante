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
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" class="column">
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

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $alexEmail = "a.olemans@student.helmo.be";
    $nom = htmlspecialchars($_POST["nom"]);
    $prenom = htmlspecialchars($_POST["prenom"]);
    $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
    $message = htmlspecialchars($_POST["message"]);

    if (!$email) {
        die("Adresse email invalide.");
    }

    $sujet = "[Supra Brocante] Message de $prenom $nom";
    $contenu = "Message reçu de : $prenom $nom <$email>\n\n$message";

    // header
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8";

    $envoi = mail($alexEmail, $sujet, $contenu, $headers);

    if ($envoi) {
        echo "<pre>Ce message n'est pas censé s'afficher car ça va fail dans tous les cas.</pre>";
    } else {
        echo "<pre>godverdomme, php mail werkt niet</pre>";
    }
}
?>