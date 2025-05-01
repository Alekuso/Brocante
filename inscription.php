<?php
include_once 'php/Database.php';
include_once 'php/Brocanteur.php';

// Rediriger si déjà connecté
if (Brocanteur::estConnecte()) {
    header('Location: espaceBrocanteur.php');
    exit;
}

$erreur = '';
$succes = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = filter_var($_POST["nom"], FILTER_SANITIZE_STRING);
    $prenom = filter_var($_POST["prenom"], FILTER_SANITIZE_STRING);
    $courriel = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $motDePasse = $_POST["password"];
    $passwordConfirm = $_POST["passwordConfirm"];
    
    if (empty($nom) || empty($prenom) || empty($courriel) || empty($motDePasse) || empty($passwordConfirm)) {
        $erreur = 'Tous les champs sont obligatoires';
    } elseif ($motDePasse !== $passwordConfirm) {
        $erreur = 'Les mots de passe ne correspondent pas';
    } else {
        // Vérifier si l'email existe déjà
        $db = new Database();
        $existe = $db->obtenirUn("SELECT * FROM Brocanteur WHERE courriel = ?", [$courriel]);
        
        if ($existe) {
            $erreur = 'Cette adresse email est déjà utilisée';
        } else {
            // Créer le nouveau brocanteur
            $brocanteur = new Brocanteur([
                'nom' => $nom,
                'prenom' => $prenom,
                'courriel' => $courriel,
                'description' => 'Nouveau brocanteur',
                'visible' => 0,  // Non visible par défaut jusqu'à validation
                'est_administrateur' => 0
            ]);
            
            // Insertion avec mot de passe
            $db->executer(
                "INSERT INTO Brocanteur (nom, prenom, courriel, mot_passe, description, visible, est_administrateur) 
                VALUES (?, ?, ?, ?, ?, ?, ?)",
                [
                    $nom, 
                    $prenom, 
                    $courriel, 
                    password_hash($motDePasse, PASSWORD_DEFAULT),
                    'Nouveau brocanteur', 
                    0, 
                    0
                ]
            );
            
            $succes = 'Inscription réussie! Vous pouvez maintenant vous connecter.';
            
            // Récupérer l'ID du brocanteur créé
            $brocanteur->bid = $db->dernierIdInsere();
            
            if ($brocanteur->bid) {
                // Rediriger vers la page de connexion
                header('Location: connexion.php?inscrit=1');
                exit;
            } else {
                $erreur = 'Erreur lors de l\'inscription. Veuillez réessayer.';
            }
        }
    }
}
?>
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
            <?php if (!empty($erreur)): ?>
                <p class="erreur"><?php echo htmlspecialchars($erreur); ?></p>
            <?php endif; ?>
            <?php if (!empty($succes)): ?>
                <p class="succes"><?php echo htmlspecialchars($succes); ?></p>
                <p class="center"><a href="connexion.php" class="underline">Se connecter</a></p>
            <?php else: ?>
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
            <?php endif; ?>
        </article>
    </section>
    <section>
        <article class="center">
            <p>Vous avez déjà un compte ? <a class="underline" href="connexion.php">Connectez-vous</a></p>
        </article>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>
