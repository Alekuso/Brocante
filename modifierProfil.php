<?php
include_once 'php/Brocanteur.php';
include_once 'php/Database.php';

// Vérifier si l'utilisateur est connecté
if (!Brocanteur::estConnecte()) {
    header('Location: connexion.php');
    exit;
}

// Récupérer l'utilisateur connecté
$utilisateur = Brocanteur::obtenirConnecte();
$erreur = '';
$succes = '';

// Traiter la modification des données personnelles
if (isset($_POST['modifier_donnees'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $description = trim($_POST['description']);
    
    if (!empty($nom) && !empty($prenom) && !empty($description)) {
        $db = new Database();
        $db->executer("UPDATE Brocanteur SET nom = ?, prenom = ?, description = ? WHERE bid = ?", 
            [$nom, $prenom, $description, $utilisateur->bid]);
        
        // Mettre à jour l'objet utilisateur
        $utilisateur->nom = $nom;
        $utilisateur->prenom = $prenom;
        $utilisateur->description = $description;
        $succes = "Informations mises à jour avec succès.";
        
        // Rediriger vers la page appropriée
        header('Location: ' . ($utilisateur->est_administrateur ? 'espaceAdministrateur.php' : 'espaceBrocanteur.php'));
        exit;
    } else {
        $erreur = "Tous les champs sont obligatoires.";
    }
}

// Traiter l'upload de photo
if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    // Vérifier le type de fichier
    $allowed = ['image/jpeg', 'image/png', 'image/gif'];
    $type = $_FILES['photo']['type'];
    
    if (in_array($type, $allowed)) {
        // Créer le dossier si nécessaire
        $uploadDir = 'uploads/brocanteurs/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Générer un nom de fichier unique
        $filename = $utilisateur->bid . '_' . time() . '_' . $_FILES['photo']['name'];
        $destination = $uploadDir . $filename;
        
        // Déplacer le fichier
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
            // Mettre à jour la base de données
            $db = new Database();
            $db->executer("UPDATE Brocanteur SET photo = ? WHERE bid = ?", 
                [$filename, $utilisateur->bid]);
            
            // Mettre à jour l'objet utilisateur
            $utilisateur->photo = $filename;
            $succes = "Photo de profil mise à jour avec succès.";
            
            // Rediriger vers la page appropriée
            header('Location: ' . ($utilisateur->est_administrateur ? 'espaceAdministrateur.php' : 'espaceBrocanteur.php'));
            exit;
        } else {
            $erreur = "Erreur lors de l'upload de la photo.";
        }
    } else {
        $erreur = "Le type de fichier n'est pas autorisé. Utilisez JPG, PNG ou GIF.";
    }
}

// Récupérer la zone et l'emplacement si c'est un brocanteur
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
    <?php if (!empty($erreur)): ?>
        <div class="erreur-message"><?php echo htmlspecialchars($erreur); ?></div>
    <?php endif; ?>
    <?php if (!empty($succes)): ?>
        <div class="succes-message"><?php echo htmlspecialchars($succes); ?></div>
    <?php endif; ?>

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
            
            <!-- Formulaire pour changer la photo de profil -->
            <form method="POST" action="modifierProfil.php" enctype="multipart/form-data" class="photo-form">
                <div class="file-input-wrapper">
                    <input type="file" name="photo" id="photo" accept="image/*" class="file-input">
                    <button type="submit" class="btn mar-2">Changer photo de profil</button>
                </div>
            </form>
        </article>
        <article>
            <h1><?php echo htmlspecialchars($utilisateur->prenom . ' ' . $utilisateur->nom); ?></h1>
            
            <?php if ($utilisateur->est_administrateur): ?>
                <h3>Administrateur</h3>
            <?php endif; ?>
            
            <?php if ($zone): ?>
                <h3><?php echo htmlspecialchars($zone->nom); ?></h3>
            <?php endif; ?>
            
            <?php if ($emplacement): ?>
                <p class="emplacement">Emplacement: <?php echo htmlspecialchars($emplacement->code); ?></p>
            <?php endif; ?>
            
            <!-- Formulaire pour modifier les informations -->
            <form method="POST" action="modifierProfil.php" class="profile-form">
                <div class="form-group">
                    <label for="nom">Nom:</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($utilisateur->nom); ?>" class="size-full">
                </div>
                
                <div class="form-group">
                    <label for="prenom">Prénom:</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($utilisateur->prenom); ?>" class="size-full">
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4" class="size-full"><?php echo htmlspecialchars($utilisateur->description); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <input type="submit" name="modifier_donnees" value="Enregistrer" class="btn mar-2">
                    <a href="<?php echo $utilisateur->est_administrateur ? 'espaceAdministrateur.php' : 'espaceBrocanteur.php'; ?>" class="btn mar-2">Annuler</a>
                </div>
            </form>
        </article>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html> 