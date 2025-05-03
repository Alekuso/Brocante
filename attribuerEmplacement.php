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
$brocanteur = null;

if ($id > 0) {
    $brocanteur = Brocanteur::obtenirParId($id);
}

$zones = Zone::obtenirToutes();
$message = '';
$erreur = '';

// Traite le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zoneId = $_POST['zone'] ?? '';
    $brocanteurId = $_POST['brocanteur_id'] ?? $id;
    
    if (empty($brocanteurId)) {
        $erreur = 'Veuillez sélectionner un brocanteur';
    } else {
        $db = Database::getInstance();
        $emplacement = $db->obtenirUn("SELECT * FROM Emplacement WHERE bid = ?", [$brocanteurId]);
        
        // Si zoneId est vide, ça signifie qu'on veut annuler l'attribution
        if (empty($zoneId)) {
            if ($emplacement) {
                $db->executer("DELETE FROM Emplacement WHERE bid = ?", [$brocanteurId]);
                $message = 'Attribution d\'emplacement annulée';
                header('Location: espaceAdministrateur.php?message=' . urlencode("Attribution d'emplacement annulée"));
                exit;
            } else {
                $erreur = 'Aucun emplacement n\'était attribué à ce brocanteur';
            }
        } else {
            $zone = Zone::obtenirParId($zoneId);
            
            if ($zone) {
                // Génère le code
                $lettre = substr($zone->nom, -1);
                $emplacements = $db->obtenirTous("SELECT * FROM Emplacement WHERE zid = ?", [$zoneId]);
                $numero = count($emplacements) + 1;
                $code = $lettre . $numero;
                
                // Vérifie si l'emplacement n'est pas déjà attribué à un autre brocanteur
                $emplacementExistant = $db->obtenirUn(
                    "SELECT e.*, b.nom, b.prenom FROM Emplacement e 
                     JOIN Brocanteur b ON e.bid = b.bid 
                     WHERE e.code = ? AND e.zid = ? AND e.bid != ?", 
                    [$code, $zoneId, $brocanteurId]
                );
                
                if ($emplacementExistant) {
                    $erreur = "L'emplacement $code est déjà attribué à " . 
                              htmlspecialchars($emplacementExistant['prenom'] . ' ' . $emplacementExistant['nom']) . ". " .
                              "Veuillez choisir une autre zone.";
                } else {
                    if ($emplacement) {
                        $db->executer("UPDATE Emplacement SET zid = ?, code = ? WHERE bid = ?", [$zoneId, $code, $brocanteurId]);
                        $message = 'Emplacement mis à jour';
                    } else {
                        $db->executer("INSERT INTO Emplacement (code, zid, bid) VALUES (?, ?, ?)", [$code, $zoneId, $brocanteurId]);
                        $message = 'Emplacement attribué';
                    }
                    
                    header('Location: espaceAdministrateur.php?message=' . urlencode("Emplacement attribué avec succès"));
                    exit;
                }
            } else {
                $erreur = "Zone non trouvée";
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
            
            <form method="POST" action="attribuerEmplacement.php<?php echo $id ? '?id=' . $id : ''; ?>" class="column">
                <?php 
                if ($brocanteur) {
                    // Récupère l'emplacement actuel
                    $emplacementActuel = $brocanteur->obtenirEmplacement();
                    $zoneActuelle = $brocanteur->obtenirZone();
                    
                    echo '<input type="hidden" name="brocanteur_id" value="' . htmlspecialchars($brocanteur->bid) . '">';
                    echo '<label for="nom">Nom</label>';
                    echo '<input class="size-full" type="text" id="nom" name="nom" value="' . htmlspecialchars($brocanteur->nom) . '" readonly>';
                    echo '<label for="prenom">Prénom</label>';
                    echo '<input class="size-full" type="text" id="prenom" name="prenom" value="' . htmlspecialchars($brocanteur->prenom) . '" readonly>';
                    
                    if ($emplacementActuel) {
                        echo '<p class="message-succes center">Emplacement actuel: ' . htmlspecialchars($emplacementActuel->code) . ' (Zone: ' . htmlspecialchars($zoneActuelle->nom) . ')</p>';
                    } else {
                        echo '<p class="message-erreur center">Aucun emplacement attribué actuellement.</p>';
                    }
                } else {
                    echo '<label for="brocanteur">Brocanteur</label>';
                    echo '<select class="size-full" id="brocanteur" name="brocanteur_id" required>';
                    echo '<option value="">-- Sélectionnez un brocanteur --</option>';
                    
                    $db = Database::getInstance();
                    $brocanteurs = $db->obtenirTous("SELECT * FROM Brocanteur WHERE est_administrateur = 0 ORDER BY nom");
                    
                    foreach ($brocanteurs as $b) {
                        echo '<option value="' . htmlspecialchars($b['bid']) . '">' . htmlspecialchars($b['prenom'] . ' ' . $b['nom']) . '</option>';
                    }
                    
                    echo '</select>';
                }
                ?>

                <label for="zone">Zone</label>
                <select class="size-full" id="zone" name="zone">
                    <option value="">-- Aucun emplacement (annuler l'attribution) --</option>
                    <?php 
                    foreach ($zones as $zone) {
                        $selected = ($zoneActuelle && $zoneActuelle->zid == $zone->zid) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($zone->zid) . '" ' . $selected . '>' . htmlspecialchars($zone->nom) . '</option>';
                    }
                    ?>
                </select>

                <button type="submit" class="size-half">Attribuer</button>
                <p class="mar-tb-1 center">
                    <a href="espaceAdministrateur.php" class="btn-small">Retour à l'espace administrateur</a>
                </p>
            </form>
        </article>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>