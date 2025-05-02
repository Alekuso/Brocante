<?php
include_once 'php/Brocanteur.php';
include_once 'php/Database.php';
include_once 'php/Objet.php';
include_once 'php/Categorie.php';

use Brocante\Modele\Brocanteur;
use Brocante\Modele\Objet;
use Brocante\Modele\Categorie;
use Brocante\Base\Database;

// Vérifier si l'utilisateur est connecté
if (!Brocanteur::estConnecte()) {
    header('Location: connexion.php');
    exit;
}

// Récupérer le brocanteur connecté
$brocanteur = Brocanteur::obtenirConnecte();
$erreurs = [];
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
        $erreurs['intitule'] = "Le titre de l'objet est requis.";
    }
    
    if ($prix <= 0) {
        $erreurs['prix'] = "Le prix doit être supérieur à 0.";
    }
    
    if (empty($description)) {
        $erreurs['description'] = "La description est requise.";
    }
    
    if ($cid <= 0) {
        $erreurs['categorie'] = "Veuillez sélectionner une catégorie.";
    }
    
    // Si aucune erreur, on peut créer l'objet
    if (empty($erreurs)) {
        // Créer un objet
        $objet = new Objet();
        $objet->intitule = $intitule;
        $objet->prix = $prix;
        $objet->description = $description;
        $objet->cid = $cid;
        $objet->bid = $brocanteur->bid;
        
        // Traitement de l'image si présente
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            // Vérifier le type MIME et l'extension
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            $file_type = $_FILES['image']['type'];
            $file_name = $_FILES['image']['name'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if (in_array($file_type, $allowed_types) && in_array($file_extension, $allowed_extensions)) {
                // Créer le dossier si nécessaire
                $uploadDir = 'uploads/objets/';
                if (!is_dir($uploadDir)) {
                    if (!mkdir($uploadDir, 0777, true)) {
                        $erreurs['image'] = "Impossible de créer le répertoire d'upload.";
                    }
                }
                
                if (empty($erreurs['image'])) {
                    // Générer un nom de fichier unique
                    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file_name);
                    $destination = $uploadDir . $filename;
                    
                    // Vérifier si le dossier est accessible en écriture
                    if (!is_writable($uploadDir)) {
                        $erreurs['image'] = "Le dossier d'upload n'est pas accessible en écriture.";
                    } else {
                        // Déplacer le fichier
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                            $objet->image = $filename;
                        } else {
                            $erreurs['image'] = "Erreur lors de l'upload de l'image. Code: " . $_FILES['image']['error'];
                        }
                    }
                }
            } else {
                $erreurs['image'] = "Le type de fichier n'est pas autorisé. Utilisez JPG, PNG ou GIF uniquement.";
            }
        }
        
        // Si toujours pas d'erreur, on enregistre l'objet
        if (empty($erreurs)) {
            if ($objet->enregistrer()) {
                $succes = "L'objet a été ajouté avec succès.";
                // Redirection vers l'espace brocanteur
                header('Location: espaceBrocanteur.php');
                exit;
            } else {
                $erreurs['general'] = "Une erreur est survenue lors de l'enregistrement de l'objet.";
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
    <?php if (!empty($erreurs)): ?>
        <div class="message-erreur">
            <?php 
            foreach ($erreurs as $champ => $message) {
                echo htmlspecialchars($message) . "<br>";
            }
            
            // Informations de debug pour les erreurs d'upload
            if (isset($erreurs['image']) && isset($_FILES['image']) && $_FILES['image']['error'] != 0): 
                $upload_errors = [
                    0 => "Aucune erreur, le téléchargement est réussi.",
                    1 => "Le fichier dépasse la taille maximale définie dans php.ini (upload_max_filesize).",
                    2 => "Le fichier dépasse la taille maximale spécifiée dans le formulaire HTML (MAX_FILE_SIZE).",
                    3 => "Le fichier n'a été que partiellement téléchargé.",
                    4 => "Aucun fichier n'a été téléchargé.",
                    6 => "Dossier temporaire manquant.",
                    7 => "Échec d'écriture du fichier sur le disque.",
                    8 => "Une extension PHP a arrêté le téléchargement du fichier."
                ];
                echo "Code d'erreur PHP: " . htmlspecialchars($_FILES['image']['error']);
                if (isset($upload_errors[$_FILES['image']['error']])) {
                    echo " - " . htmlspecialchars($upload_errors[$_FILES['image']['error']]);
                }
            endif;
            ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($succes)): ?>
        <div class="message-succes"><?php echo htmlspecialchars($succes); ?></div>
    <?php endif; ?>

    <section class="presentation">
        <article class="center">
            <h1>Ajouter un objet</h1>
        </article>
    </section>

    <section id="contactFormContainer" class="container">
        <article class="contactForm">
            <form method="POST" action="ajouterObjet.php" enctype="multipart/form-data" class="bg-darkgray desk-pad-2 rounded-sm">
                <!-- Champ caché pour limiter la taille des fichiers -->
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />

                <div class="form-group">
                    <label for="intitule">Titre de l'objet:</label>
                    <input type="text" id="intitule" name="intitule" value="<?php echo isset($_POST['intitule']) ? htmlspecialchars($_POST['intitule']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="prix">Prix (€):</label>
                    <input type="number" id="prix" name="prix" step="0.01" min="0.01" value="<?php echo isset($_POST['prix']) ? htmlspecialchars($_POST['prix']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="categorie">Catégorie:</label>
                    <select id="categorie" name="categorie" required>
                        <option value="">Sélectionnez une catégorie</option>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?php echo htmlspecialchars($categorie->cid); ?>" <?php echo (isset($_POST['categorie']) && $_POST['categorie'] == $categorie->cid) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($categorie->intitule); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="6" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="image">Image (facultatif):</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
                
                <button type="submit">Ajouter l'objet</button>
            </form>
        </article>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html> 