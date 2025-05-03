<?php
include_once 'php/Brocanteur.php';
use Brocante\Modele\Brocanteur;

$erreur = '';
$message = '';

// Vérifie si l'utilisateur est connecté
$utilisateurConnecte = Brocanteur::estConnecte() ? Brocanteur::obtenirConnecte() : null;
$emailPreRempli = '';

if ($utilisateurConnecte) {
    $emailPreRempli = $utilisateurConnecte->courriel;
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courriel = isset($_POST['courriel']) ? trim($_POST['courriel']) : '';
    
    // Validation de l'email
    if (empty($courriel)) {
        $erreur = "Veuillez entrer votre adresse e-mail.";
    } elseif (!filter_var($courriel, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse e-mail n'est pas valide.";
    } else {
        $nouveauMotDePasse = Brocanteur::reinitialiserMotDePasse($courriel);
        
        if ($nouveauMotDePasse) {
            
            // Préparation de l'email
            $destinataire = $courriel;
            $sujet = "Réinitialisation de votre mot de passe - Supra Brocante";
            $contenu = "Bonjour,\n\n";
            $contenu .= "Vous avez demandé la réinitialisation de votre mot de passe pour le site Supra Brocante.\n\n";
            $contenu .= "Votre nouveau mot de passe est : $nouveauMotDePasse\n\n";
            $headers = "From: noreply@suprabrocante.com\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8";
            

            mail($destinataire, $sujet, $contenu, $headers);
            

            $message = "Un nouveau mot de passe a été généré et envoyé à votre adresse e-mail.";

        } else {
            $erreur = "Aucun compte n'est associé à cette adresse e-mail.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe - Supra Brocante</title>
    <link rel="icon" href="images/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include 'inc/header.php'; ?>
<main>
    <section class="form-container">
        <h1>Réinitialiser votre mot de passe</h1>
        
        <?php if (!empty($message)): ?>
            <section class="message-succes"><?php echo $message; ?></section>
        <?php endif; ?>
        
        <?php if (!empty($erreur)): ?>
            <section class="message-erreur"><?php echo htmlspecialchars($erreur); ?></section>
        <?php endif; ?>
        
        <?php if (empty($message)): // N'affiche le formulaire que si pas de message de succès ?>
            <form method="post" action="reinitialiserMotDePasse.php" class="form">
                <section class="form-group">
                    <label for="courriel">Adresse e-mail</label>
                    <input type="email" id="courriel" name="courriel" required 
                           placeholder="Entrez votre adresse e-mail" 
                           value="<?php echo !empty($emailPreRempli) ? htmlspecialchars($emailPreRempli) : (isset($_POST['courriel']) ? htmlspecialchars($_POST['courriel']) : ''); ?>">
                </section>
                
                <section class="form-actions">
                    <button type="submit" class="btn">Réinitialiser mon mot de passe</button>
                    <?php if ($utilisateurConnecte): ?>
                        <a href="modifierProfil.php" class="btn-link">Retour au profil</a>
                    <?php else: ?>
                        <a href="connexion.php" class="btn-link">Retour à la connexion</a>
                    <?php endif; ?>
                </section>
            </form>
        <?php else: ?>
            <section class="form-actions center">
                <?php if ($utilisateurConnecte): ?>
                    <a href="modifierProfil.php" class="btn">Retour au profil</a>
                <?php else: ?>
                    <a href="connexion.php" class="btn">Retour à la connexion</a>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>
</html> 