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

// Récupère le brocanteur
$brocanteur = Brocanteur::obtenirConnecte();
$erreur = '';
$succes = '';

// Récupère l'objet
$oid = isset($_GET['id']) ? intval($_GET['id']) : 0;
$objet = Objet::obtenirParId($oid);

// Vérifie la propriété
if (!$objet || $objet->bid !== $brocanteur->bid) {
    header('Location: espaceBrocanteur.php');
    exit;
}

// Récupère les catégories
$categories = Categorie::obtenirToutes();

// Traite le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $intitule = trim($_POST['intitule'] ?? '');
    $prix = floatval(str_replace(',', '.', $_POST['prix'] ?? 0));
    $description = trim($_POST['description'] ?? '');
    $cid = intval($_POST['categorie'] ?? 0);
    
    // Vérifie les données
    if (empty($intitule)) {
        $erreur = "Le titre de l'objet est requis";
    } elseif ($prix <= 0) {
        $erreur = "Le prix doit être supérieur à 0";
    } elseif (empty($description)) {
        $erreur = "La description est requise";
    } elseif ($cid <= 0) {
        $erreur = "Veuillez sélectionner une catégorie";
    } else {
        // Met à jour l'objet
        $objet->intitule = $intitule;
        $objet->prix = $prix;
        $objet->description = $description;
        $objet->cid = $cid;
        
        // Traite l'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png'];
            $allowed_extensions = ['jpg', 'jpeg', 'png'];
            
            $file_type = $_FILES['image']['type'];
            $file_name = $_FILES['image']['name'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if (in_array($file_type, $allowed_types) && in_array($file_extension, $allowed_extensions)) {
                $uploadDir = 'uploads/objets/';
                if (!is_dir($uploadDir)) {
                    if (!mkdir($uploadDir, 0777, true)) {
                        $erreur = "Impossible de créer le dossier";
                    }
                }
                
                if (empty($erreur)) {
                    // Génère un nom unique
                    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file_name);
                    $destination = $uploadDir . $filename;
                    
                    if (!is_writable($uploadDir)) {
                        $erreur = "Le dossier n'est pas accessible en écriture";
                    } else {
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                            // Supprime l'ancienne image
                            if ($objet->image && file_exists($uploadDir . $objet->image) && is_file($uploadDir . $objet->image)) {
                                @unlink($uploadDir . $objet->image);
                            }
                            $objet->image = $filename;
                        } else {
                            $erreur = "Erreur lors de l'upload de l'image";
                        }
                    }
                }
            } else {
                $erreur = "Format de fichier non accepté (JPG ou PNG uniquement)";
            }
        }
        
        if (empty($erreur)) {
            if ($objet->enregistrer()) {
                $succes = "L'objet a été modifié";
                header('Location: espaceBrocanteur.php');
                exit;
            } else {
                $erreur = "Erreur lors de l'enregistrement";
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
                    <a href="supprimerObjet.php?id=<?php echo $objet->oid; ?>&confirme=0" class="btn btn-danger mar-2">Supprimer</a>
                </section>
            </form>
        </article>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html> 