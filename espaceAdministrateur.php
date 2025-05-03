<?php
include_once 'php/Brocanteur.php';
include_once 'php/Database.php';
include_once 'php/Emplacement.php';
include_once 'php/Zone.php';

use Brocante\Modele\Brocanteur;
use Brocante\Base\Database;
use Brocante\Modele\Emplacement;
use Brocante\Modele\Zone;

// Vérifie si l'utilisateur est connecté et est admin
// KNOWN ISSUE: L'utilisateur doit se déconnecter et se reconnecter pour voir ses modifications !!!!!! TO FIX
if (!Brocanteur::estConnecte() || !Brocanteur::estAdmin()) {
    header('Location: index.php');
    exit;
}

// Récupère l'admin connecté
$admin = Brocanteur::obtenirConnecte();

// Messages
$message = isset($_GET['message']) ? $_GET['message'] : '';
$erreur = isset($_GET['erreur']) ? $_GET['erreur'] : '';

// Récupère tous les brocanteurs (non administrateurs)
$db = Database::getInstance();
$brocanteurs = $db->obtenirTous("
    SELECT b.*, e.code as emplacement_code, z.nom as zone_nom
    FROM Brocanteur b 
    LEFT JOIN Emplacement e ON b.bid = e.bid
    LEFT JOIN Zone z ON e.zid = z.zid
    WHERE b.est_administrateur = 0
    ORDER BY b.nom ASC
");

// Compte les emplacements
$stats = [
    'total_brocanteurs' => count($brocanteurs),
    'emplacements_attribues' => 0
];

$query_emplacements = "SELECT COUNT(*) as count FROM Emplacement WHERE bid IS NOT NULL";
$result = $db->obtenirUn($query_emplacements);
$stats['emplacements_attribues'] = $result['count'];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante - Espace Administrateur</title>
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
    if (!empty($message)) {
        echo "<section class=\"message-succes\">" . htmlspecialchars($message) . "</section>";
    }
    
    if (!empty($erreur)) {
        echo "<section class=\"message-erreur\">" . htmlspecialchars($erreur) . "</section>";
    }
    ?>
    
    <section class="articles size-half presentation">
        <article>
            <?php 
            if ($admin->photo === null) {
                $image = "images/placeholder.png";
            } else {
                $image = "uploads/brocanteurs/" . htmlspecialchars($admin->photo);
            }
            ?>
            <img class="size-full" src="<?php echo $image; ?>" alt="Photo de profil" />
        </article>
        <article>
            <h1>Espace Administrateur</h1>
            <h2>Bonjour, <?php echo htmlspecialchars($admin->prenom); ?> !</h2>
            <p class="mar-tb-1"><?php echo htmlspecialchars($admin->description); ?></p>
            <a href="modifierProfil.php" class="btn mar-2">Modifier profil</a>
        </article>
    </section>
    
    <!-- Statistiques -->
    <section class="stats-container bg-darkgray">
        <section class="stats-grid">
            <article class="stat-box">
                <h3>Total des brocanteurs</h3>
                <p class="stat-number"><?php echo $stats['total_brocanteurs']; ?> / 15</p>
                <p class="stat-note">Maximum: 15 brocanteurs</p>
            </article>
            <article class="stat-box">
                <h3>Emplacements attribués</h3>
                <p class="stat-number"><?php echo $stats['emplacements_attribues']; ?></p>
            </article>
            <article class="stat-box">
                <h3>Emplacements restants</h3>
                <p class="stat-number"><?php echo $stats['total_brocanteurs'] - $stats['emplacements_attribues']; ?></p>
            </article>
            <article class="stat-box">
                <h3>Places disponibles</h3>
                <p class="stat-number"><?php echo max(0, 15 - $stats['total_brocanteurs']); ?></p>
            </article>
        </section>
    </section>
    
    <!-- Liste des brocanteurs -->
    <section class="presentation">
        <h2 class="center">Brocanteurs</h2>
        
        <?php 
        if (empty($brocanteurs)) {
            echo "<p class=\"center\">Aucun brocanteur inscrit.</p>";
        } else {
            echo "<section class=\"admin-cards-container\">";
            foreach ($brocanteurs as $brocanteur) {
                $hasEmplacement = !empty($brocanteur['emplacement_code']);
                $cardClass = $hasEmplacement ? "validated" : "waiting";
                
                echo "<article class=\"admin-card $cardClass\">";
                echo "<header class=\"admin-card-header\">";
                echo "<h3>" . htmlspecialchars($brocanteur['prenom'] . ' ' . $brocanteur['nom']) . "</h3>";
                echo "<span class=\"admin-card-id\">ID: " . htmlspecialchars($brocanteur['bid']) . "</span>";
                echo "</header>";
                echo "<section class=\"admin-card-body\">";
                echo "<p><strong>Email:</strong> " . htmlspecialchars($brocanteur['courriel']) . "</p>";
                echo "<p><strong>Zone:</strong> " . htmlspecialchars($brocanteur['zone_nom'] ?? 'Non assigné') . "</p>";
                echo "<p><strong>Emplacement:</strong> " . htmlspecialchars($brocanteur['emplacement_code'] ?? 'Non assigné') . "</p>";
                echo "</section>";
                echo "<footer class=\"admin-card-actions\">";
                echo "<a href=\"vendeur.php?id=" . $brocanteur['bid'] . "\" class=\"btn-small\">Voir</a>";
                echo "<a href=\"attribuerEmplacement.php?id=" . $brocanteur['bid'] . "\" class=\"btn-small\">Emplacement</a>";
                
                // Afficher l'option de suppression si pas d'emplacement attribué
                if (!$hasEmplacement) {
                    echo "<a href=\"supprimerBrocanteur.php?id=" . $brocanteur['bid'] . "\" class=\"btn-small\" style=\"background-color: #cc3333;\">Supprimer</a>";
                }
                
                echo "</footer>";
                echo "</article>";
            }
            echo "</section>";
        }
        ?>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>