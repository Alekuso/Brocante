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
$erreur = '';
$succes = '';

// Récupérer l'ID de l'objet depuis l'URL
$oid = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupérer l'objet
$objet = Objet::obtenirParId($oid);

// Vérifier si l'objet existe et appartient au brocanteur connecté
if (!$objet || $objet->bid !== $brocanteur->bid) {
    header('Location: espaceBrocanteur.php');
    exit;
}

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
        // Mise à jour de l'objet
        $objet->intitule = $intitule;
        $objet->prix = $prix;
        $objet->description = $description;
        $objet->cid = $cid;
        
        // Traitement de l'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            // Vérifier le type MIME et l'extension
            $allowed_types = ['image/jpeg', 'image/png'];
            $allowed_extensions = ['jpg', 'jpeg', 'png'];
            
            $file_type = $_FILES['image']['type'];
            $file_name = $_FILES['image']['name'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if (in_array($file_type, $allowed_types) && in_array($file_extension, $allowed_extensions)) {
                // Créer le dossier si nécessaire
                $uploadDir = 'uploads/objets/';
                if (!is_dir($uploadDir)) {
                    if (!mkdir($uploadDir, 0777, true)) {
                        $erreur = "Impossible de créer le répertoire d'upload.";
                    }
                }
                
                if (empty($erreur)) {
                    // Générer un nom de fichier unique
                    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file_name);
                    $destination = $uploadDir . $filename;
                    
                    // Vérifier si le dossier est accessible en écriture
                    if (!is_writable($uploadDir)) {
                        $erreur = "Le dossier d'upload n'est pas accessible en écriture.";
                    } else {
                        // Déplacer le fichier
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                            // Supprimer l'ancienne image si elle existe
                            if ($objet->image && file_exists($uploadDir . $objet->image) && is_file($uploadDir . $objet->image)) {
                                @unlink($uploadDir . $objet->image);
                            }
                            $objet->image = $filename;
                        } else {
                            $erreur = "Erreur lors de l'upload de l'image. Code: " . $_FILES['image']['error'];
                        }
                    }
                }
            } else {
                $erreur = "Le type de fichier n'est pas autorisé. Utilisez JPG ou PNG uniquement.";
            }
        }
        
        if (empty($erreur)) {
            // Enregistrer l'objet
            if ($objet->enregistrer()) {
                $succes = "L'objet a été modifié avec succès.";
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
    <title>Supra Brocante - Modifier un objet</title>
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
        if (isset($_FILES['image']) && $_FILES['image']['error'] != 0) {
            echo "<p>Code d'erreur PHP: " . htmlspecialchars($_FILES['image']['error']);
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
            if (isset($upload_errors[$_FILES['image']['error']])) {
                echo " - " . htmlspecialchars($upload_errors[$_FILES['image']['error']]);
            }
            echo "</p>";
        }
        echo "</section>";
    }
    
    if (!empty($succes)) {
        echo "<section class=\"message-succes\">" . htmlspecialchars($succes) . "</section>";
    }
    ?>

    <section class="presentation">
        <article class="center">
            <h1>Modifier un objet</h1>
        </article>
    </section>

    <section class="articles size-half presentation">
        <article>
            <?php 
            if ($objet->image === null) {
                $image = "images/placeholder.png";
            } else {
                $image = "uploads/objets/" . htmlspecialchars($objet->image);
            }
            ?>
            <img class="size-full" src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($objet->intitule); ?>" />
        </article>
        <article>
            <form method="POST" action="modifierObjet.php?id=<?php echo $objet->oid; ?>" enctype="multipart/form-data" class="profile-form">
                <section class="form-group">
                    <label for="intitule">Titre de l'objet:</label>
                    <input type="text" id="intitule" name="intitule" value="<?php echo htmlspecialchars($objet->intitule); ?>" required class="size-full">
                </section>
                
                <section class="form-group">
                    <label for="prix">Prix (€):</label>
                    <input type="number" id="prix" name="prix" step="0.01" min="0.01" value="<?php echo htmlspecialchars($objet->prix); ?>" required class="size-full">
                </section>
                
                <section class="form-group">
                    <label for="categorie">Catégorie:</label>
                    <select id="categorie" name="categorie" required class="size-full">
                        <?php 
                        foreach ($categories as $categorie) {
                            echo "<option value=\"" . htmlspecialchars($categorie->cid) . "\"";
                            if ($categorie->cid == $objet->cid) {
                                echo " selected";
                            }
                            echo ">" . htmlspecialchars($categorie->intitule) . "</option>";
                        }
                        ?>
                    </select>
                </section>
                
                <section class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="6" required class="size-full"><?php echo htmlspecialchars($objet->description); ?></textarea>
                </section>
                
                <section class="form-group">
                    <label for="image">Changer l'image:</label>
                    <input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
                    <input type="file" id="image" name="image" accept="image/jpeg, image/png" class="size-full">
                </section>
                
                <section class="form-actions">
                    <input type="submit" value="Enregistrer" class="btn mar-2">
                    <a href="espaceBrocanteur.php" class="btn mar-2">Annuler</a>
                    <a href="supprimerObjet.php?id=<?php echo $objet->oid; ?>" class="btn btn-danger mar-2">Supprimer</a>
                </section>
            </form>
        </article>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html> 