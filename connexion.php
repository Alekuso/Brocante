<?php
include_once 'php/Database.php';
include_once 'php/Brocanteur.php';

// Rediriger si déjà connecté
if (Brocanteur::estConnecte()) {
    header('Location: espaceBrocanteur.php');
    exit;
}

$erreur = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courriel = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $motDePasse = $_POST["password"];
    
    if (empty($courriel) || empty($motDePasse)) {
        $erreur = 'Tous les champs sont obligatoires';
    } else {
        $brocanteur = Brocanteur::connecter($courriel, $motDePasse);
        
        if ($brocanteur) {
            Brocanteur::connecterUtilisateur($brocanteur);
            
            // Rediriger selon le rôle
            if ($brocanteur->est_administrateur) {
                header('Location: espaceAdministrateur.php');
            } else {
                header('Location: espaceBrocanteur.php');
            }
            exit;
        } else {
            $erreur = 'Email ou mot de passe incorrect';
        }
    }
}
?>
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
            <?php if (!empty($erreur)): ?>
                <p class="erreur"><?php echo htmlspecialchars($erreur); ?></p>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" class="column">
                <label for="email">Email</label>
                <input class="size-full" type="email" id="email" name="email" required>
                <label for="password">Mot de passe</label>
                <input class="size-full" type="password" id="password" name="password" required>
                <button type="submit" class="size-half">Se connecter</button>
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