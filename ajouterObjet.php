<?php
include_once 'php/Brocanteur.php';
include_once 'php/Database.php';
include_once 'php/Objet.php';
include_once 'php/Categorie.php';

// Vérifier si l'utilisateur est connecté
if (!Brocanteur::estConnecte()) {
    header('Location: connexion.php');
    exit;
}

// Récupérer le brocanteur connecté
$brocanteur = Brocanteur::obtenirConnecte();
$erreur = '';
$succes = '';

// Récupérer toutes les catégories pour le formulaire
$categories = Categorie::obtenirToutes();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $intitule = trim($_POST['intitule'] ?? '');
    $prix = floatval(str_replace(',', '.', $_POST['prix'] ?? 0));
    $description = trim($_POST['description'] ?? '');
    $cid = intval($_POST['categorie'] ?? 0);
    
    // Validation des données
    if (empty($intitule)) {
        $erreur = "Le titre de l'objet est requis.";
    } elseif ($prix <= 0) {
        $erreur = "Le prix doit être supérieur à 0.";
    } elseif (empty($description)) {
        $erreur = "La description est requise.";
    } elseif ($cid <= 0) {
        $erreur = "Veuillez sélectionner une catégorie.";
    } else {
        // Création de l'objet
        $objet = new Objet([
            'intitule' => $intitule,
            'prix' => $prix,
            'description' => $description,
            'bid' => $brocanteur->bid,
            'cid' => $cid
        ]);
        
        // Traitement de l'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['image/jpeg', 'image/png', 'image/gif'];
            $type = $_FILES['image']['type'];
            
            if (in_array($type, $allowed)) {
                // Créer le dossier si nécessaire
                $uploadDir = 'uploads/objets/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Générer un nom de fichier unique
                $filename = time() . '_' . $_FILES['image']['name'];
                $destination = $uploadDir . $filename;
                
                // Déplacer le fichier
                if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                    $objet->image = $filename;
                } else {
                    $erreur = "Erreur lors de l'upload de l'image.";
                }
            } else {
                $erreur = "Le type de fichier n'est pas autorisé. Utilisez JPG, PNG ou GIF.";
            }
        }
        
        if (empty($erreur)) {
            // Enregistrer l'objet
            if ($objet->enregistrer()) {
                $succes = "L'objet a été ajouté avec succès.";
                // Redirection vers l'espace brocanteur
                header('Location: espaceBrocanteur.php');
                exit;
            } else {
                $erreur = "Une erreur est survenue lors de l'enregistrement de l'objet.";
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
    <title>Supra Brocante - Ajouter un objet</title>
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
            <h1>Ajouter un nouvel objet</h1>
        </article>
    </section>

    <section class="articles size-half presentation">
        <article class="size-full">
            <form method="POST" action="ajouterObjet.php" enctype="multipart/form-data" class="profile-form">
                <div class="form-group">
                    <label for="intitule">Titre de l'objet:</label>
                    <input type="text" id="intitule" name="intitule" required class="size-full">
                </div>
                
                <div class="form-group">
                    <label for="prix">Prix (€):</label>
                    <input type="number" id="prix" name="prix" step="0.01" min="0.01" required class="size-full">
                </div>
                
                <div class="form-group">
                    <label for="categorie">Catégorie:</label>
                    <select id="categorie" name="categorie" required class="size-full">
                        <option value="">Sélectionnez une catégorie</option>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?php echo htmlspecialchars($categorie->cid); ?>">
                                <?php echo htmlspecialchars($categorie->intitule); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="6" required class="size-full"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="image">Image:</label>
                    <input type="file" id="image" name="image" accept="image/*" class="size-full">
                </div>
                
                <div class="form-actions">
                    <input type="submit" value="Ajouter" class="btn mar-2">
                    <a href="espaceBrocanteur.php" class="btn mar-2">Annuler</a>
                </div>
            </form>
        </article>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html> 