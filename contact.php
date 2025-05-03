<?php
include_once 'php/Brocanteur.php';
use Brocante\Modele\Brocanteur;

$erreurs = [];
$prenom = $email = $nom = $message = '';
$succes = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST["nom"] ?? '';
    $prenom = $_POST["prenom"] ?? '';
    $email = $_POST["email"] ?? '';
    $message = $_POST["message"] ?? '';
    
    // Vérifie le formulaire
    if (empty($nom)) {
        $erreurs['nom'] = "Le nom est obligatoire";
    }
    
    if (empty($prenom)) {
        $erreurs['prenom'] = "Le prénom est obligatoire";
    }
    
    if (empty($email)) {
        $erreurs['email'] = "L'email est obligatoire";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = "Format d'email invalide";
    }
    
    if (empty($message)) {
        $erreurs['message'] = "Le message est obligatoire";
    }
    
    // Traite le formulaire si pas d'erreurs
    if (empty($erreurs)) {
        $administrateurs = Brocanteur::obtenirTousAdministrateurs();
        
        $nomFiltre = htmlspecialchars($nom);
        $prenomFiltre = htmlspecialchars($prenom);
        $emailFiltre = filter_var($email, FILTER_SANITIZE_EMAIL);
        $messageFiltre = htmlspecialchars($message);

        $sujet = "[Supra Brocante] Message de $prenomFiltre $nomFiltre";
        $contenu = "Message reçu de : $prenomFiltre $nomFiltre <$emailFiltre>\n\n$messageFiltre";

        $headers = "From: $emailFiltre\r\n";
        $headers .= "Reply-To: $emailFiltre\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8";

        // Envoie à tous les administrateurs
        foreach ($administrateurs as $admin) {
            mail($admin->courriel, $sujet, $contenu, $headers);
        }
        
        // Envoie une copie à l'expéditeur (dans le cadre du cours)
        $sujet = "[Supra Brocante] Copie de votre message";
        $contenu = "Voici une copie de votre message : $messageFiltre";
        mail($emailFiltre, $sujet, $contenu, $headers);
        
        $succes = true;
        
        // Vide les champs après envoi
        $prenom = $email = $nom = $message = '';
    }
}
?>
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
    <section class="contact-image-container center">
        <img src="./images/contact.png" alt="Brocante rétro" class="contact-image">
    </section>
    <section class="presentation center">
        <article class="center">
            <h1>Contact</h1>
            <h3>Vous avez une question ?</h3>
            <h3>Remplissez ce formulaire et on vous répondra dans le plus bref délais !</h3>
            
            <?php
            if ($succes) {
                echo "<section class=\"message-succes\">";
                echo "<p>Votre message a été envoyé avec succès !</p>";
                echo "</section>";
            }
            ?>
        </article>
    </section>
    <section class="contactFormContainer bg-darkgray container">
        <article class="contactForm">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" class="column">
                <label for="nom">Nom</label>
                <input class="size-full" type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
                <?php
                if (isset($erreurs['nom'])) {
                    echo "<p class=\"erreur\">" . $erreurs['nom'] . "</p>";
                }
                ?>
                
                <label for="prenom">Prénom</label>
                <input class="size-full" type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($prenom); ?>" required>
                <?php
                if (isset($erreurs['prenom'])) {
                    echo "<p class=\"erreur\">" . $erreurs['prenom'] . "</p>";
                }
                ?>
                
                <label for="email">Email</label>
                <input class="size-full" type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                <?php
                if (isset($erreurs['email'])) {
                    echo "<p class=\"erreur\">" . $erreurs['email'] . "</p>";
                }
                ?>
                
                <label for="message">Message</label>
                <textarea id="message" name="message" required><?php echo htmlspecialchars($message); ?></textarea>
                <?php
                if (isset($erreurs['message'])) {
                    echo "<p class=\"erreur\">" . $erreurs['message'] . "</p>";
                }
                ?>
                
                <button type="submit" class="size-half">Envoyer</button>
            </form>
        </article>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>