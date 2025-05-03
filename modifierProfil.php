<?php
include_once 'php/Brocanteur.php';
include_once 'php/Database.php';

use Brocante\Modele\Brocanteur;
use Brocante\Base\Database;

// Vérifie la connexion
if (!Brocanteur::estConnecte()) {
    header('Location: connexion.php');
    exit;
}

// Récupère l'utilisateur
$utilisateur = Brocanteur::obtenirConnecte();
$erreur = '';
$succes = '';

// Traite les modifications de données
if (isset($_POST['modifier_donnees'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $description = trim($_POST['description']);
    
    if (!empty($nom) && !empty($prenom) && !empty($description)) {
        $db = Database::getInstance();
        $db->executer("UPDATE Brocanteur SET nom = ?, prenom = ?, description = ? WHERE bid = ?", 
            [$nom, $prenom, $description, $utilisateur->bid]);
        
        // Met à jour en mémoire
        $utilisateur->nom = $nom;
        $utilisateur->prenom = $prenom;
        $utilisateur->description = $description;
        $succes = "Informations mises à jour";
        
        // Redirige
        header('Location: ' . ($utilisateur->est_administrateur ? 'espaceAdministrateur.php' : 'espaceBrocanteur.php'));
        exit;
    } else {
        $erreur = "Tous les champs sont obligatoires";
    }
}

// Traite la photo
if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $allowed_types = ['image/jpeg', 'image/png'];
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    
    $file_type = $_FILES['photo']['type'];
    $file_name = $_FILES['photo']['name'];
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    if (in_array($file_type, $allowed_types) && in_array($file_extension, $allowed_extensions)) {
        $uploadDir = 'uploads/brocanteurs/';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                $erreur = "Impossible de créer le dossier";
            }
        }
        
        if (empty($erreur)) {
            // Génère le nom de fichier
            $filename = strtoupper($utilisateur->nom . $utilisateur->prenom) . '.' . $file_extension;
            $destination = $uploadDir . $filename;
            
            if (!is_writable($uploadDir)) {
                $erreur = "Le dossier n'est pas accessible en écriture";
            } else {
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                    // Supprime l'ancienne photo
                    if ($utilisateur->photo && file_exists($uploadDir . $utilisateur->photo) && is_file($uploadDir . $utilisateur->photo)) {
                        @unlink($uploadDir . $utilisateur->photo);
                    }
                    
                    // Met à jour la BD
                    $db = Database::getInstance();
                    $db->executer("UPDATE Brocanteur SET photo = ? WHERE bid = ?", 
                        [$filename, $utilisateur->bid]);
                    
                    // Met à jour en mémoire
                    $utilisateur->photo = $filename;
                    $succes = "Photo de profil mise à jour";
                    
                    // Redirige
                    header('Location: ' . ($utilisateur->est_administrateur ? 'espaceAdministrateur.php' : 'espaceBrocanteur.php'));
                    exit;
                } else {
                    $erreur = "Erreur lors de l'upload de la photo";
                }
            }
        }
    } else {
        $erreur = "Format de fichier non accepté (JPG ou PNG uniquement)";
    }
}

// Récupère données
$zone = $utilisateur->obtenirZone();
$emplacement = $utilisateur->obtenirEmplacement();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante - Modifier Profil</title>
    <link rel="icon" href="images/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include 'inc/header.php'; ?>
<main>
    <?php 
    if (!empty($erreur)) {
        echo "<section class=\"message-erreur\">";
        echo htmlspecialchars($erreur);
        echo "</section>";
    }
    
    if (!empty($succes)) {
        echo "<section class=\"message-succes\">" . htmlspecialchars($succes) . "</section>";
    }
    ?>

    <section class="presentation">
        <article class="center">
            <h1>Modifier votre profil</h1>
        </article>
    </section>

    <section class="articles size-half presentation">
        <article>
            <?php 
            if ($utilisateur->photo === null) {
                $image = "images/placeholder.png";
            } else {
                $image = "uploads/brocanteurs/" . htmlspecialchars($utilisateur->photo);
            }
            ?>
            <img class="size-full" src="<?php echo $image; ?>" alt="Photo de profil" />
            
            <!-- Formulaire photo -->
            <form method="POST" action="modifierProfil.php" enctype="multipart/form-data" class="photo-form">
                <input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
                <section class="file-input-wrapper">
                    <input type="file" name="photo" id="photo" accept="image/jpeg, image/png" class="file-input">
                    <button type="submit" class="btn mar-2">Changer photo de profil</button>
                </section>
            </form>
        </article>
        <article>
            <h1><?php echo htmlspecialchars($utilisateur->prenom . ' ' . $utilisateur->nom); ?></h1>
            
            <?php 
            if ($utilisateur->est_administrateur) {
                echo "<h3>Administrateur</h3>";
            }
            
            if ($zone) {
                echo "<h3>" . htmlspecialchars($zone->nom) . "</h3>";
            }
            
            if ($emplacement) {
                echo "<p class=\"emplacement\">Emplacement: " . htmlspecialchars($emplacement->code) . "</p>";
            }
            ?>
            
            <!-- Formulaire infos -->
            <form method="POST" action="modifierProfil.php" class="profile-form">
                <section class="form-group">
                    <label for="nom">Nom:</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($utilisateur->nom); ?>" class="size-full" required>
                </section>
                
                <section class="form-group">
                    <label for="prenom">Prénom:</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($utilisateur->prenom); ?>" class="size-full" required>
                </section>
                
                <section class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4" class="size-full" required><?php echo htmlspecialchars($utilisateur->description); ?></textarea>
                </section>
                
                <section class="form-actions">
                    <input type="submit" name="modifier_donnees" value="Enregistrer" class="btn mar-2">
                    <a href="<?php echo $utilisateur->est_administrateur ? 'espaceAdministrateur.php' : 'espaceBrocanteur.php'; ?>" class="btn mar-2">Annuler</a>
                </section>
            </form>
        </article>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html> 