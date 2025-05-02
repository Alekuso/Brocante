<?php
include_once 'php/Brocanteur.php';
include_once 'php/Zone.php';
include_once 'php/Database.php';
include_once 'php/Emplacement.php';

use Brocante\Modele\Brocanteur;
use Brocante\Modele\Zone;
use Brocante\Modele\Emplacement;
use Brocante\Base\Database;

// Vérifier si l'utilisateur est connecté et est admin
if (!Brocanteur::estConnecte() || !Brocanteur::estAdmin()) {
    header('Location: index.php');
    exit;
}

// Récupérer le brocanteur à modifier
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$isValidating = isset($_GET['validate']) && $_GET['validate'] == 1;
$brocanteur = null;

if ($id > 0) {
    $brocanteur = Brocanteur::obtenirParId($id);
}

// Récupérer toutes les zones
$zones = Zone::obtenirToutes();

$message = '';
$erreur = '';

// Si l'utilisateur arrive de la validation d'inscription
if ($isValidating) {
    $db = new Database();
    // Vérifier que le brocanteur n'est pas déjà visible
    $isVisible = $db->obtenirUn("SELECT visible FROM Brocanteur WHERE bid = ?", [$id])['visible'] ?? 0;
    
    if ($isVisible) {
        $message = "Ce brocanteur est déjà validé. Vous pouvez modifier son emplacement.";
    } else {
        $message = "Pour valider l'inscription de ce brocanteur, vous devez lui attribuer un emplacement.";
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zoneId = $_POST['zone'] ?? '';
    $brocanteurId = $_POST['brocanteur_id'] ?? $id;
    
    if (empty($zoneId) || empty($brocanteurId)) {
        $erreur = 'Veuillez sélectionner une zone';
    } else {
        // Attribution de la zone
        $db = new Database();
        // Vérifier si un emplacement existe déjà
        $emplacement = $db->obtenirUn("SELECT * FROM Emplacement WHERE bid = ?", [$brocanteurId]);
        
        // Obtenir la zone
        $zone = Zone::obtenirParId($zoneId);
        
        if ($zone) {
            // Trouver le prochain numéro disponible pour cette zone
            $lettre = substr($zone->nom, -1); // Prend la dernière lettre de la zone (ex: 'Zone A' => 'A')
            
            // Compter combien d'emplacements existent déjà dans cette zone
            $emplacements = $db->obtenirTous("SELECT * FROM Emplacement WHERE zid = ?", [$zoneId]);
            $numero = count($emplacements) + 1;
            
            // Générer le code (ex: A1, A2, etc.)
            $code = $lettre . $numero;
            
            if ($emplacement) {
                // Mise à jour avec le nouveau code
                $db->executer("UPDATE Emplacement SET zid = ?, code = ? WHERE bid = ?", [$zoneId, $code, $brocanteurId]);
                $message = 'Emplacement mis à jour avec succès';
            } else {
                // Insertion avec le nouveau code
                $db->executer("INSERT INTO Emplacement (code, zid, bid) VALUES (?, ?, ?)", [$code, $zoneId, $brocanteurId]);
                $message = 'Emplacement attribué avec succès';
            }
        } else {
            $message = "Erreur: Zone non trouvée";
            // Génération de code par défaut au cas où
            $code = 'E-' . $brocanteurId;
            
            if ($emplacement) {
                $db->executer("UPDATE Emplacement SET zid = ?, code = ? WHERE bid = ?", [$zoneId, $code, $brocanteurId]);
            } else {
                $db->executer("INSERT INTO Emplacement (code, zid, bid) VALUES (?, ?, ?)", [$code, $zoneId, $brocanteurId]);
            }
        }
        
        // Rendre le brocanteur visible
        $db->executer("UPDATE Brocanteur SET visible = 1 WHERE bid = ?", [$brocanteurId]);
        
        // Rediriger vers l'espace administrateur avec un message de succès
        if ($isValidating) {
            header('Location: espaceAdministrateur.php?message=' . urlencode("L'inscription du brocanteur a été validée et l'emplacement attribué avec succès."));
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
            <?php if (!empty($message)): ?>
                <p class="message-succes center"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            
            <?php if (!empty($erreur)): ?>
                <p class="message-erreur center"><?php echo htmlspecialchars($erreur); ?></p>
            <?php endif; ?>
            
            <form method="POST" action="attribuerEmplacement.php<?php echo $id ? '?id=' . $id . ($isValidating ? '&validate=1' : '') : ''; ?>" class="column">
                <?php if ($brocanteur): ?>
                    <input type="hidden" name="brocanteur_id" value="<?php echo htmlspecialchars($brocanteur->bid); ?>">
                    <label for="nom">Nom</label>
                    <input class="size-full" type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($brocanteur->nom); ?>" readonly>
                    <label for="prenom">Prénom</label>
                    <input class="size-full" type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($brocanteur->prenom); ?>" readonly>
                <?php else: ?>
                    <label for="brocanteur">Brocanteur</label>
                    <select class="size-full" id="brocanteur" name="brocanteur_id" required>
                        <option value="">-- Sélectionnez un brocanteur --</option>
                        <?php 
                        $brocanteurs = Brocanteur::obtenirTousVisibles();
                        foreach ($brocanteurs as $b): 
                        ?>
                            <option value="<?php echo htmlspecialchars($b->bid); ?>"><?php echo htmlspecialchars($b->prenom . ' ' . $b->nom); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>

                <label for="zone">Zone</label>
                <select class="size-full" id="zone" name="zone" required>
                    <option value="">-- Sélectionnez une zone --</option>
                    <?php foreach ($zones as $zone): ?>
                        <option value="<?php echo htmlspecialchars($zone->zid); ?>"><?php echo htmlspecialchars($zone->nom); ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="size-half">Attribuer</button>
                
                <?php if ($isValidating): ?>
                    <p class="mar-tb-1 center">
                        <a href="espaceAdministrateur.php" class="btn-small">Annuler et retourner à l'espace administrateur</a>
                    </p>
                <?php endif; ?>
            </form>
        </article>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>