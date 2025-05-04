<?php
include_once 'php/Database.php';
include_once 'php/Brocanteur.php';
include_once 'php/Emplacement.php';

use Brocante\Modele\Brocanteur;
use Brocante\Modele\Emplacement;
use Brocante\Base\Database;

// Redirige si déjà connecté
if (Brocanteur::estConnecte()) {
    header('Location: espaceBrocanteur.php');
    exit;
}

// Vérifie si le nombre maximum de brocanteurs est atteint (15 max)
$db = Database::getInstance();
$countBrocanteurs = $db->obtenirUn("SELECT COUNT(*) as total FROM Brocanteur WHERE est_administrateur = 0");
$maxBrocanteursAtteint = $countBrocanteurs && $countBrocanteurs['total'] >= 15;

$erreurs = [];
$succes = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$maxBrocanteursAtteint) {
    $nom = filter_var($_POST["nom"], FILTER_SANITIZE_STRING);
    $prenom = filter_var($_POST["prenom"], FILTER_SANITIZE_STRING);
    $raw_email = $_POST["email"] ?? '';
    $courriel = filter_var($raw_email, FILTER_SANITIZE_EMAIL);
    
    $motDePasse = $_POST["password"];
    $passwordConfirm = $_POST["passwordConfirm"];
    $description = filter_var($_POST["description"], FILTER_SANITIZE_STRING);
    $visible = isset($_POST["visible"]) ? 1 : 0; // Variable pour stocker le choix de visibilité
    
    // Vérifie les champs
    if (empty($nom)) {
        $erreurs['nom'] = 'Le nom est obligatoire';
    }
    
    if (empty($prenom)) {
        $erreurs['prenom'] = 'Le prénom est obligatoire';
    }
    
    if (empty($raw_email)) {
        $erreurs['email'] = 'L\'email est obligatoire';
    } elseif (!filter_var($courriel, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = 'email invalide';
    } else {
        // Vérifie si l'email existe déjà
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
    
    // Vérifie la photo
    $photo_filename = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        
        $file_type = $_FILES['photo']['type'];
        $file_name = $_FILES['photo']['name'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (!in_array($file_type, $allowed_types) || !in_array($file_extension, $allowed_extensions)) {
            $erreurs['photo'] = 'Format de fichier non accepté (JPG ou PNG uniquement)';
        } elseif ($_FILES['photo']['size'] > 20000000) {
            $erreurs['photo'] = 'Fichier trop volumineux (max 20MB)';
        } else {
            $uploadDir = 'uploads/brocanteurs/';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    $erreurs['photo'] = 'Impossible de créer le dossier';
                }
            }
            
            if (!isset($erreurs['photo'])) {
                $temp_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file_name);
            }
        }
    }

    if (empty($erreurs)) {
        // Crée le brocanteur
        $brocanteur = new Brocanteur([
            'nom' => $nom,
            'prenom' => $prenom,
            'courriel' => $courriel,
            'description' => $description,
            'visible' => $visible,
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
                $description, 
                $visible, 
                0
            ]
        );
        
        $brocanteur_id = $db->dernierIdInsere();
        
        // Traite la photo si présente
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0 && !isset($erreurs['photo'])) {
            $photo_filename = strtoupper($nom . $prenom) . '.' . $file_extension;
            $destination = $uploadDir . $photo_filename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                $db->executer("UPDATE Brocanteur SET photo = ? WHERE bid = ?", [$photo_filename, $brocanteur_id]);
            } else {
                $erreurs['photo'] = 'Erreur lors de l\'upload de la photo';
            }
        }
        
        if ($brocanteur_id) {
            $montant = 20.00;
            
            $succes = '<p>Inscription réussie! Vous devez maintenant payer 20€ pour finaliser votre inscription.</p>' .
                      '<p>Montant à payer: ' . $montant . ' €</p>' .
                      '<p>IBAN: BEXXXXXXXXXXXXXX</p>' .
                      '<p>Communication: "Foire aux puces - réservation n°' . $brocanteur_id . '"</p>';
        } else {
            $erreurs['general'] = 'Erreur lors de l\'inscription';
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
            <?php 
            if (!empty($erreurs)) {
                echo "<section class=\"message-erreur\">";
                foreach ($erreurs as $champ => $message) {
                    echo "<p>" . htmlspecialchars($message) . "</p>";
                }
                echo "</section>";
            }
            
            if (!empty($succes)) {
                echo "<section class=\"message-succes\">";
                echo $succes;
                echo "</section>";
                echo "<p class=\"center\"><a href=\"connexion.php\" class=\"underline\">Se connecter</a></p>";
            } elseif ($maxBrocanteursAtteint) {
                echo "<section class=\"message-erreur\">";
                echo "<p>Désolé, le nombre maximum de brocanteurs (15) est atteint pour cette brocante.</p>";
                echo "</section>";
                echo "<p class=\"center\"><a href=\"index.php\" class=\"underline\">Retour à l'accueil</a></p>";
            } else {
                echo "<form method=\"POST\" action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "\" enctype=\"multipart/form-data\" class=\"column\">";
                echo "<label for=\"nom\">Nom*</label>";
                echo "<input class=\"size-full\" type=\"text\" id=\"nom\" name=\"nom\" value=\"" . (isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '') . "\" required>";
                
                echo "<label for=\"prenom\">Prénom*</label>";
                echo "<input class=\"size-full\" type=\"text\" id=\"prenom\" name=\"prenom\" value=\"" . (isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : '') . "\" required>";
                
                echo "<label for=\"email\">Email*</label>";
                echo "<input class=\"size-full\" type=\"email\" id=\"email\" name=\"email\" value=\"" . (isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '') . "\" required>";
                
                echo "<label for=\"password\">Mot de passe*</label>";
                echo "<input class=\"size-full\" type=\"password\" id=\"password\" name=\"password\" required>";
                
                echo "<label for=\"passwordConfirm\">Confirmer le mot de passe*</label>";
                echo "<input class=\"size-full\" type=\"password\" id=\"passwordConfirm\" name=\"passwordConfirm\" required>";
                
                echo "<label for=\"description\">Description* (présentez votre stand et les objets que vous vendez)</label>";
                echo "<textarea class=\"size-full\" id=\"description\" name=\"description\" rows=\"5\">" . (isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '') . "</textarea>";
                
                echo "<section class=\"checkbox-container\" style=\"margin: 10px 0; display: flex; align-items: center;\">";
                echo "<input type=\"checkbox\" id=\"visible\" name=\"visible\" value=\"1\" " . (isset($_POST['visible']) ? 'checked' : '') . " style=\"margin-right: 10px; width: auto;\">";
                echo "<label for=\"visible\">Je souhaite que mon profil et mes objets soient visibles sur le site</label>";
                echo "</section>";

                echo "<label for=\"photo\">Photo (optionnelle)</label>";
                echo "<input class=\"size-full\" type=\"file\" id=\"photo\" name=\"photo\" accept=\"image/jpeg, image/png\">";
                echo "<p class=\"small-text\">Formats acceptés: JPG, PNG</p>";
                
                echo "<button type=\"submit\" class=\"size-half\">Créer un compte</button>";
                echo "<p class=\"small-text\">* Champs obligatoires</p>";
                echo "</form>";
            }
            ?>
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
