<?php
include_once 'php/Database.php';
include_once 'php/Brocanteur.php';
include_once 'php/Emplacement.php';

use Brocante\Modele\Brocanteur;
use Brocante\Modele\Emplacement;
use Brocante\Base\Database;

// Rediriger si déjà connecté
if (Brocanteur::estConnecte()) {
    header('Location: espaceBrocanteur.php');
    exit;
}

$erreurs = [];
$succes = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = filter_var($_POST["nom"], FILTER_SANITIZE_STRING);
    $prenom = filter_var($_POST["prenom"], FILTER_SANITIZE_STRING);
    $courriel = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $motDePasse = $_POST["password"];
    $passwordConfirm = $_POST["passwordConfirm"];
    $description = filter_var($_POST["description"], FILTER_SANITIZE_STRING);
    
    // Vérifications des champs
    if (empty($nom)) {
        $erreurs['nom'] = 'Le nom est obligatoire';
    }
    
    if (empty($prenom)) {
        $erreurs['prenom'] = 'Le prénom est obligatoire';
    }
    
    if (empty($courriel)) {
        $erreurs['email'] = 'L\'email est obligatoire';
    } elseif (!filter_var($courriel, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = 'Format d\'email invalide';
    } else {
        // Vérifier si l'email existe déjà
        $db = Database::getInstance();
        $existe = $db->obtenirUn("SELECT * FROM Brocanteur WHERE courriel = ?", [$courriel]);
        
        if ($existe) {
            $erreurs['email'] = 'Cette adresse email est déjà utilisée';
        }
    }
    
    if (empty($motDePasse)) {
        $erreurs['password'] = 'Le mot de passe est obligatoire';
    } elseif (strlen($motDePasse) < 6) {
        $erreurs['password'] = 'Le mot de passe doit contenir au moins 6 caractères';
    }
    
    if ($motDePasse !== $passwordConfirm) {
        $erreurs['passwordConfirm'] = 'Les mots de passe ne correspondent pas';
    }
    
    if (empty($description)) {
        $erreurs['description'] = 'La description est obligatoire';
    }
    
    // Vérification de la photo
    $photo_filename = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        // Vérifier le type MIME et l'extension
        $allowed_types = ['image/jpeg', 'image/png'];
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        
        $file_type = $_FILES['photo']['type'];
        $file_name = $_FILES['photo']['name'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (!in_array($file_type, $allowed_types) || !in_array($file_extension, $allowed_extensions)) {
            $erreurs['photo'] = 'Le format de fichier n\'est pas autorisé. Utilisez JPG ou PNG uniquement.';
        } elseif ($_FILES['photo']['size'] > 20000000) { // 20MB
            $erreurs['photo'] = 'Le fichier est trop volumineux (max 20MB)';
        } else {
            // Créer le dossier uploads/brocanteurs s'il n'existe pas
            $uploadDir = 'uploads/brocanteurs/';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    $erreurs['photo'] = 'Impossible de créer le répertoire d\'upload';
                }
            }
            
            if (!isset($erreurs['photo'])) {
                // Générer un nom de fichier unique temporaire
                $temp_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file_name);
                // Le nom de fichier final sera défini après l'enregistrement du brocanteur (format NOMPRENOM.extension)
            }
        }
    }

    if (empty($erreurs)) {
        // Créer le nouveau brocanteur
        $brocanteur = new Brocanteur([
            'nom' => $nom,
            'prenom' => $prenom,
            'courriel' => $courriel,
            'description' => $description,
            'visible' => 0,  // Non visible par défaut jusqu'à validation
            'est_administrateur' => 0
        ]);
        
        // Insertion avec mot de passe
        $db = Database::getInstance();
        $db->executer(
            "INSERT INTO Brocanteur (nom, prenom, courriel, mot_passe, description, visible, est_administrateur) 
            VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $nom, 
                $prenom, 
                $courriel, 
                password_hash($motDePasse, PASSWORD_DEFAULT),
                $description, 
                0, 
                0
            ]
        );
        
        // Récupérer l'ID du brocanteur créé
        $brocanteur_id = $db->dernierIdInsere();
        
        // Traiter la photo si présente et valide
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0 && !isset($erreurs['photo'])) {
            // Définir le nom final au format NOMPRENOM.extension
            $photo_filename = strtoupper($nom . $prenom) . '.' . $file_extension;
            $destination = $uploadDir . $photo_filename;
            
            // Déplacer le fichier
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                // Mettre à jour la BD avec le nom de la photo
                $db->executer("UPDATE Brocanteur SET photo = ? WHERE bid = ?", [$photo_filename, $brocanteur_id]);
            } else {
                // La photo n'a pas pu être déplacée, mais l'inscription est quand même valide
                $erreurs['photo'] = 'Erreur lors de l\'upload de la photo, mais votre inscription a été enregistrée';
            }
        }
        
        if ($brocanteur_id) {
            $montant = 20.00; // Frais d'inscription
            
            $succes = 'Inscription réussie! Vous devez maintenant payer 20€ pour finaliser votre inscription.<br>' .
                      'Montant à payer: ' . $montant . ' €<br>' .
                      'IBAN: BE68 5390 0754 7034<br>' .
                      'Communication: "Brocante - ' . strtoupper($nom) . ' ' . strtoupper($prenom) . '"<br>' .
                      'Un administrateur validera votre compte après réception du paiement.';
            
            // Ne plus rediriger vers la page de confirmation
            // On affiche directement le message de succès sur cette page
        } else {
            $erreurs['general'] = 'Erreur lors de l\'inscription. Veuillez réessayer.';
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
            <?php if (!empty($erreurs)): ?>
                <div class="message-erreur">
                    <?php foreach ($erreurs as $champ => $message): ?>
                        <p><?php echo htmlspecialchars($message); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($succes)): ?>
                <div class="message-succes">
                    <p><?php echo $succes; ?></p>
                </div>
                <p class="center"><a href="connexion.php" class="underline">Se connecter</a></p>
            <?php else: ?>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" enctype="multipart/form-data" class="column">
                    <label for="nom">Nom*</label>
                    <input class="size-full" type="text" id="nom" name="nom" value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>" required>
                    
                    <label for="prenom">Prénom*</label>
                    <input class="size-full" type="text" id="prenom" name="prenom" value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : ''; ?>" required>
                    
                    <label for="email">Email*</label>
                    <input class="size-full" type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    
                    <label for="password">Mot de passe*</label>
                    <input class="size-full" type="password" id="password" name="password" required>
                    
                    <label for="passwordConfirm">Confirmer le mot de passe*</label>
                    <input class="size-full" type="password" id="passwordConfirm" name="passwordConfirm" required>
                    
                    <label for="description">Description* (présentez votre stand et les objets que vous vendez)</label>
                    <textarea class="size-full" id="description" name="description" rows="5"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    
                    <label for="photo">Photo (optionnelle)</label>
                    <input class="size-full" type="file" id="photo" name="photo" accept="image/jpeg, image/png">
                    <p class="small-text">Formats acceptés: JPG, PNG - Max 20 MB</p>
                    
                    <button type="submit" class="size-half">Créer un compte</button>
                    <p class="small-text">* Champs obligatoires</p>
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
