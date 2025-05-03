<?php
include_once 'php/Database.php';
include_once 'php/Brocanteur.php';
include_once 'php/Zone.php';

use Brocante\Base\Database;
use Brocante\Modele\Brocanteur;
use Brocante\Modele\Zone;

// Initialisation des paramètres de recherche
$nom = isset($_GET['nom']) ? trim($_GET['nom']) : '';
$prenom = isset($_GET['prenom']) ? trim($_GET['prenom']) : '';

// Optimisation: récupérer tous les brocanteurs avec leurs zones et emplacements en une seule requête
$db = Database::getInstance();

// Si la recherche est active, récupérer les brocanteurs correspondants
if (!empty($nom) || !empty($prenom)) {
    $params = [];
    $sql = "SELECT b.*, e.code as emplacement_code, z.nom as zone_nom, z.zid as zone_id 
            FROM Brocanteur b
            LEFT JOIN Emplacement e ON b.bid = e.bid
            LEFT JOIN Zone z ON e.zid = z.zid
            WHERE b.visible = 1";
    
    if (!empty($nom)) {
        $sql .= " AND b.nom LIKE ?";
        $params[] = "%$nom%";
    }
    
    if (!empty($prenom)) {
        $sql .= " AND b.prenom LIKE ?";
        $params[] = "%$prenom%";
    }
    
    $sql .= " ORDER BY b.nom ASC, b.prenom ASC";
    $resultats = $db->obtenirTous($sql, $params);
    
    // Organiser les résultats
    $brocanteurs_data = [];
    foreach ($resultats as $row) {
        $brocanteurs_data[] = [
            'brocanteur' => new Brocanteur($row),
            'zone_nom' => $row['zone_nom'],
            'emplacement_code' => $row['emplacement_code']
        ];
    }
    $zones = []; // Pas besoin de zones car on affiche juste les résultats de recherche
} else {
    // Récupérer toutes les zones
    $zones = Zone::obtenirToutes();
    
    // Pour chaque zone, récupérer les brocanteurs en une seule requête
    $brocanteurs_par_zone = [];
    
    foreach ($zones as $zone) {
        $sql = "SELECT b.*, e.code as emplacement_code
                FROM Brocanteur b
                JOIN Emplacement e ON b.bid = e.bid
                WHERE e.zid = ? AND b.visible = 1
                ORDER BY b.nom ASC, b.prenom ASC";
        $resultats = $db->obtenirTous($sql, [$zone->zid]);
        
        $brocanteurs_zone = [];
        foreach ($resultats as $row) {
            $brocanteurs_zone[] = [
                'brocanteur' => new Brocanteur($row),
                'emplacement_code' => $row['emplacement_code']
            ];
        }
        
        $brocanteurs_par_zone[$zone->zid] = $brocanteurs_zone;
    }
    
    $brocanteurs_data = null;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante - Brocanteurs</title>
    <link rel="icon" href="images/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include 'inc/header.php'; ?>
<main>
    <section class="presentation center">
        <article class="center">
            <h1>Brocanteurs</h1>
        </article>
    </section>
    <section id="contactFormContainer" class="container">
        <article class="contactForm">

            <form action="brocanteurs.php" method="get" class="bg-darkgray desk-pad-2 rounded-sm column">
                <label for="nom">Nom</label>
                <input class="size-half" type="text" id="nom" name="nom" placeholder="Nom" value="<?php echo htmlspecialchars($nom); ?>">
                <label for="prenom">Prénom</label>
                <input class="size-half" type="text" id="prenom" name="prenom" placeholder="Prénom" value="<?php echo htmlspecialchars($prenom); ?>">
                <button type="submit" class="size-half">Rechercher</button>
            </form>
        </article>
    </section>
    <section>
        <?php if ($brocanteurs_data !== null): ?>
            <!-- Affichage des résultats de recherche -->
            <h3 class="flex center">Résultats de recherche</h3>
            <article class="articles articles-grow brocanteurs-grid">
                <?php if (empty($brocanteurs_data)): ?>
                    <p class="center">Aucun brocanteur ne correspond à votre recherche.</p>
                <?php else: ?>
                    <?php foreach ($brocanteurs_data as $data): 
                        $brocanteur = $data['brocanteur'];
                    ?>
                        <a href="vendeur.php?id=<?php echo htmlspecialchars($brocanteur->bid); ?>" class="center brocanteur-card">
                            <?php
                            if ($brocanteur->photo === null) {
                                $image = "images/placeholder.png";
                            } else {
                                $image = "uploads/brocanteurs/" . htmlspecialchars($brocanteur->photo);
                            }
                            ?>
                            <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?>" />
                            <h4><?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?></h4>
                            <?php if (!empty($data['zone_nom'])): ?>
                                <p><?php echo htmlspecialchars($data['zone_nom']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($data['emplacement_code'])): ?>
                                <p class="emplacement">Emplacement: <?php echo htmlspecialchars($data['emplacement_code']); ?></p>
                            <?php endif; ?>
                            <p><?php echo htmlspecialchars($brocanteur->description); ?></p>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </article>
        <?php else: ?>
            <!-- Affichage par zones -->
            <?php foreach ($zones as $zone): 
                $brocanteurs_zone = $brocanteurs_par_zone[$zone->zid] ?? [];
                if (empty($brocanteurs_zone)) continue;
            ?>
                <h3 class="flex center"><?php echo htmlspecialchars($zone->nom); ?></h3>
                <article class="articles articles-grow brocanteurs-grid">
                    <?php foreach ($brocanteurs_zone as $data): 
                        $brocanteur = $data['brocanteur'];
                    ?>
                        <a href="vendeur.php?id=<?php echo htmlspecialchars($brocanteur->bid); ?>" class="center brocanteur-card">
                            <?php
                            if ($brocanteur->photo === null) {
                                $image = "images/placeholder.png";
                            } else {
                                $image = "uploads/brocanteurs/" . htmlspecialchars($brocanteur->photo);
                            }
                            ?>
                            <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?>" />
                            <h4><?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?></h4>
                            <p><?php echo htmlspecialchars($zone->nom); ?></p>
                            <?php if (!empty($data['emplacement_code'])): ?>
                                <p class="emplacement">Emplacement: <?php echo htmlspecialchars($data['emplacement_code']); ?></p>
                            <?php endif; ?>
                            <p><?php echo htmlspecialchars($brocanteur->description); ?></p>
                        </a>
                    <?php endforeach; ?>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>