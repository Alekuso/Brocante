<?php
include_once 'php/Brocanteur.php';
include_once 'php/Database.php';
include_once 'php/Objet.php';
include_once 'php/Categorie.php';

use Brocante\Modele\Brocanteur;
use Brocante\Modele\Objet;
use Brocante\Modele\Categorie;
use Brocante\Base\Database;

// Vérifie la connexion
if (!Brocanteur::estConnecte()) {
    header('Location: connexion.php');
    exit;
}

// Récupère le brocanteur connecté
$brocanteur = Brocanteur::obtenirConnecte();
$erreurs = [];
$succes = '';

// Récupère les catégories pour le formulaire
$categories = Categorie::obtenirToutes();

// Traite le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $intitule = trim($_POST['intitule'] ?? '');
    $prix = floatval(str_replace(',', '.', $_POST['prix'] ?? 0));
    $description = trim($_POST['description'] ?? '');
    $cid = intval($_POST['categorie'] ?? 0);
    
    // Vérifie les données
    if (empty($intitule)) {
        $erreurs['intitule'] = "Le titre de l'objet est requis";
    }
    
    if ($prix <= 0) {
        $erreurs['prix'] = "Le prix doit être supérieur à 0";
    }
    
    if (empty($description)) {
        $erreurs['description'] = "La description est requise";
    }
    
    if ($cid <= 0) {
        $erreurs['categorie'] = "Veuillez sélectionner une catégorie";
    }
    
    // Crée l'objet si aucune erreur
    if (empty($erreurs)) {
        // Crée un nouvel objet
        $objet = new Objet();
        $objet->intitule = $intitule;
        $objet->prix = $prix;
        $objet->description = $description;
        $objet->cid = $cid;
        $objet->bid = $brocanteur->bid;
        
        // Traite l'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            // Vérifie le type et l'extension
            $allowed_types = ['image/jpeg', 'image/png'];
            $allowed_extensions = ['jpg', 'jpeg', 'png'];
            
            $file_type = $_FILES['image']['type'];
            $file_name = $_FILES['image']['name'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if (in_array($file_type, $allowed_types) && in_array($file_extension, $allowed_extensions)) {
                // Crée le dossier si nécessaire
                $uploadDir = 'uploads/objets/';
                if (!is_dir($uploadDir)) {
                    if (!mkdir($uploadDir, 0777, true)) {
                        $erreurs['image'] = "Impossible de créer le dossier";
                    }
                }
                
                if (empty($erreurs['image'])) {
                    // Génère un nom unique
                    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file_name);
                    $destination = $uploadDir . $filename;
                    
                    // Vérifie les permissions
                    if (!is_writable($uploadDir)) {
                        $erreurs['image'] = "Le dossier n'est pas accessible en écriture";
                    } else {
                        // Déplace le fichier
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                            $objet->image = $filename;
                        } else {
                            $erreurs['image'] = "Erreur lors de l'upload de l'image";
                        }
                    }
                }
            } else {
                $erreurs['image'] = "Format de fichier non accepté (JPG ou PNG uniquement)";
            }
        }
        
        // Enregistre l'objet s'il n'y a pas d'erreur
        if (empty($erreurs)) {
            if ($objet->enregistrer()) {
                $succes = "L'objet a été ajouté avec succès";
                // Redirige vers l'espace brocanteur
                header('Location: espaceBrocanteur.php');
                exit;
            } else {
                $erreurs['general'] = "Erreur lors de l'enregistrement de l'objet";
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
    <?php 
    if (!empty($erreurs)) {
        echo "<section class=\"message-erreur\">";
        foreach ($erreurs as $champ => $message) {
            echo "<p>" . htmlspecialchars($message) . "</p>";
        }
        echo "</section>";
    }
    
    if (!empty($succes)) {
        echo "<section class=\"message-succes\">" . htmlspecialchars($succes) . "</section>";
    }
    ?>

    <section class="presentation">
        <article class="center">
            <h1>Ajouter un objet</h1>
        </article>
    </section>

    <section id="contactFormContainer" class="container">
        <article class="contactForm">
            <form method="POST" action="ajouterObjet.php" enctype="multipart/form-data" class="bg-darkgray desk-pad-2 rounded-sm">
                <!-- Limite la taille des fichiers -->
                <input type="hidden" name="MAX_FILE_SIZE" value="20000000" />

                <section class="form-group">
                    <label for="intitule">Titre de l'objet:</label>
                    <input type="text" id="intitule" name="intitule" value="<?php echo isset($_POST['intitule']) ? htmlspecialchars($_POST['intitule']) : ''; ?>" required>
                </section>
                
                <section class="form-group">
                    <label for="prix">Prix (€):</label>
                    <input type="number" id="prix" name="prix" step="0.01" min="0.01" value="<?php echo isset($_POST['prix']) ? htmlspecialchars($_POST['prix']) : ''; ?>" required>
                </section>
                
                <section class="form-group">
                    <label for="categorie">Catégorie:</label>
                    <select id="categorie" name="categorie" required>
                        <option value="">Sélectionnez une catégorie</option>
                        <?php 
                        foreach ($categories as $categorie) {
                            echo "<option value=\"" . htmlspecialchars($categorie->cid) . "\"";
                            if (isset($_POST['categorie']) && $_POST['categorie'] == $categorie->cid) {
                                echo " selected";
                            }
                            echo ">" . htmlspecialchars($categorie->intitule) . "</option>";
                        }
                        ?>
                    </select>
                </section>
                
                <section class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="6" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </section>
                
                <section class="form-group">
                    <label for="image">Image (facultatif):</label>
                    <input type="file" id="image" name="image" accept="image/jpeg, image/png">
                </section>
                
                <button type="submit">Ajouter l'objet</button>
            </form>
        </article>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html> 