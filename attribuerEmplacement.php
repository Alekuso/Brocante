<?php
include_once 'php/Brocanteur.php';
include_once 'php/Zone.php';
include_once 'php/Database.php';
include_once 'php/Emplacement.php';

use Brocante\Modele\Brocanteur;
use Brocante\Modele\Zone;
use Brocante\Modele\Emplacement;
use Brocante\Base\Database;

// Vérifie les droits
if (!Brocanteur::estConnecte() || !Brocanteur::estAdmin()) {
    header('Location: index.php');
    exit;
}

// Récupère le brocanteur
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$isValidating = isset($_GET['validate']) && $_GET['validate'] == 1;
$brocanteur = null;

if ($id > 0) {
    $brocanteur = Brocanteur::obtenirParId($id);
}

$zones = Zone::obtenirToutes();
$message = '';
$erreur = '';

// Cas validation d'inscription
if ($isValidating) {
    $db = Database::getInstance();
    $isVisible = $db->obtenirUn("SELECT visible FROM Brocanteur WHERE bid = ?", [$id])['visible'] ?? 0;
    
    if ($isVisible) {
        $message = "Ce brocanteur est déjà validé. Vous pouvez modifier son emplacement";
    } else {
        $message = "Pour valider l'inscription, attribuez un emplacement";
    }
}

// Traite le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zoneId = $_POST['zone'] ?? '';
    $brocanteurId = $_POST['brocanteur_id'] ?? $id;
    
    if (empty($zoneId) || empty($brocanteurId)) {
        $erreur = 'Veuillez sélectionner une zone';
    } else {
        $db = Database::getInstance();
        $emplacement = $db->obtenirUn("SELECT * FROM Emplacement WHERE bid = ?", [$brocanteurId]);
        $zone = Zone::obtenirParId($zoneId);
        
        if ($zone) {
            // Génère le code
            $lettre = substr($zone->nom, -1);
            $emplacements = $db->obtenirTous("SELECT * FROM Emplacement WHERE zid = ?", [$zoneId]);
            $numero = count($emplacements) + 1;
            $code = $lettre . $numero;
            
            if ($emplacement) {
                $db->executer("UPDATE Emplacement SET zid = ?, code = ? WHERE bid = ?", [$zoneId, $code, $brocanteurId]);
                $message = 'Emplacement mis à jour';
            } else {
                $db->executer("INSERT INTO Emplacement (code, zid, bid) VALUES (?, ?, ?)", [$code, $zoneId, $brocanteurId]);
                $message = 'Emplacement attribué';
            }
        } else {
            $message = "Zone non trouvée";
            $code = 'E-' . $brocanteurId;
            
            if ($emplacement) {
                $db->executer("UPDATE Emplacement SET zid = ?, code = ? WHERE bid = ?", [$zoneId, $code, $brocanteurId]);
            } else {
                $db->executer("INSERT INTO Emplacement (code, zid, bid) VALUES (?, ?, ?)", [$code, $zoneId, $brocanteurId]);
            }
        }
        
        // Valide le brocanteur
        $db->executer("UPDATE Brocanteur SET visible = 1 WHERE bid = ?", [$brocanteurId]);
        
        if ($isValidating) {
            header('Location: espaceAdministrateur.php?message=' . urlencode("Brocanteur validé et emplacement attribué"));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante - Attribution d'Emplacement</title>
    <link rel="icon" href="images/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include 'inc/header.php'; ?>
<main>
    <section id="presentation center">
        <article class="center">
            <h1>Attribuer une zone à un brocanteur</h1>
        </article>
    </section>
    <section class="contactFormContainer bg-darkgray container">
        <article class="contactForm">
            <?php 
            if (!empty($message)) {
                echo '<p class="message-succes center">' . htmlspecialchars($message) . '</p>';
            }
            
            if (!empty($erreur)) {
                echo '<p class="message-erreur center">' . htmlspecialchars($erreur) . '</p>';
            }
            ?>
            
            <form method="POST" action="attribuerEmplacement.php<?php echo $id ? '?id=' . $id . ($isValidating ? '&validate=1' : '') : ''; ?>" class="column">
                <?php 
                if ($brocanteur) {
                    echo '<input type="hidden" name="brocanteur_id" value="' . htmlspecialchars($brocanteur->bid) . '">';
                    echo '<label for="nom">Nom</label>';
                    echo '<input class="size-full" type="text" id="nom" name="nom" value="' . htmlspecialchars($brocanteur->nom) . '" readonly>';
                    echo '<label for="prenom">Prénom</label>';
                    echo '<input class="size-full" type="text" id="prenom" name="prenom" value="' . htmlspecialchars($brocanteur->prenom) . '" readonly>';
                } else {
                    echo '<label for="brocanteur">Brocanteur</label>';
                    echo '<select class="size-full" id="brocanteur" name="brocanteur_id" required>';
                    echo '<option value="">-- Sélectionnez un brocanteur --</option>';
                    
                    $brocanteurs = Brocanteur::obtenirTousVisibles();
                    foreach ($brocanteurs as $b) {
                        echo '<option value="' . htmlspecialchars($b->bid) . '">' . htmlspecialchars($b->prenom . ' ' . $b->nom) . '</option>';
                    }
                    
                    echo '</select>';
                }
                ?>

                <label for="zone">Zone</label>
                <select class="size-full" id="zone" name="zone" required>
                    <option value="">-- Sélectionnez une zone --</option>
                    <?php 
                    foreach ($zones as $zone) {
                        echo '<option value="' . htmlspecialchars($zone->zid) . '">' . htmlspecialchars($zone->nom) . '</option>';
                    }
                    ?>
                </select>

                <button type="submit" class="size-half">Attribuer</button>
                
                <?php 
                if ($isValidating) {
                    echo '<p class="mar-tb-1 center">';
                    echo '<a href="espaceAdministrateur.php" class="btn-small">Annuler et retourner à l\'espace administrateur</a>';
                    echo '</p>';
                }
                ?>
            </form>
        </article>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>