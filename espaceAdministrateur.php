<?php
include_once 'php/Brocanteur.php';
include_once 'php/Database.php';
include_once 'php/Emplacement.php';
include_once 'php/Zone.php';

use Brocante\Modele\Brocanteur;
use Brocante\Base\Database;
use Brocante\Modele\Emplacement;
use Brocante\Modele\Zone;

// Vérifier si l'utilisateur est connecté et est admin
if (!Brocanteur::estConnecte() || !Brocanteur::estAdmin()) {
    header('Location: index.php');
    exit;
}

// Récupérer l'admin connecté
$admin = Brocanteur::obtenirConnecte();

// Messages
$message = isset($_GET['message']) ? $_GET['message'] : '';
$erreur = isset($_GET['erreur']) ? $_GET['erreur'] : '';

// Récupérer les brocanteurs à valider (non visibles)
$db = new Database();
$brocanteurs_attente = $db->obtenirTous("SELECT * FROM Brocanteur WHERE visible = 0 AND est_administrateur = 0 ORDER BY nom ASC");

// Récupérer les brocanteurs validés
$brocanteurs_valides = $db->obtenirTous("
    SELECT b.*, e.code as emplacement_code, z.nom as zone_nom
    FROM Brocanteur b 
    LEFT JOIN Emplacement e ON b.bid = e.bid
    LEFT JOIN Zone z ON e.zid = z.zid
    WHERE b.visible = 1 AND b.est_administrateur = 0
    ORDER BY b.nom ASC
");

// Compter les zones et emplacements
$stats = [
    'total_brocanteurs' => count($brocanteurs_attente) + count($brocanteurs_valides),
    'attente' => count($brocanteurs_attente),
    'valides' => count($brocanteurs_valides),
    'emplacements_attribues' => 0
];

// Requête directe pour compter les emplacements attribués
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
    <?php if (!empty($message)): ?>
        <div class="message-succes"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if (!empty($erreur)): ?>
        <div class="message-erreur"><?php echo htmlspecialchars($erreur); ?></div>
    <?php endif; ?>
    
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
        <div class="stats-grid">
            <div class="stat-box">
                <h3>Total des brocanteurs</h3>
                <p class="stat-number"><?php echo $stats['total_brocanteurs']; ?></p>
            </div>
            <div class="stat-box">
                <h3>En attente</h3>
                <p class="stat-number"><?php echo $stats['attente']; ?></p>
            </div>
            <div class="stat-box">
                <h3>Validés</h3>
                <p class="stat-number"><?php echo $stats['valides']; ?></p>
            </div>
            <div class="stat-box">
                <h3>Emplacements attribués</h3>
                <p class="stat-number"><?php echo $stats['emplacements_attribues']; ?></p>
            </div>
        </div>
    </section>
    
    <!-- Inscriptions en attente -->
    <section class="presentation">
        <h2 class="center">Inscriptions en attente</h2>
        
        <?php if (empty($brocanteurs_attente)): ?>
            <p class="center">Aucune inscription en attente.</p>
        <?php else: ?>
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($brocanteurs_attente as $brocanteur): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($brocanteur['bid']); ?></td>
                                <td><?php echo htmlspecialchars($brocanteur['nom']); ?></td>
                                <td><?php echo htmlspecialchars($brocanteur['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($brocanteur['courriel']); ?></td>
                                <td class="actions">
                                    <a href="vendeur.php?id=<?php echo $brocanteur['bid']; ?>" class="btn-small">Voir</a>
                                    <a href="validerInscription.php?id=<?php echo $brocanteur['bid']; ?>&action=valider" class="btn-small">Valider & Attribuer</a>
                                    <a href="validerInscription.php?id=<?php echo $brocanteur['bid']; ?>&action=refuser" class="btn-small" style="background-color: #cc3333;">Refuser</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
    
    <!-- Brocanteurs validés -->
    <section class="presentation">
        <h2 class="center">Brocanteurs validés</h2>
        
        <?php if (empty($brocanteurs_valides)): ?>
            <p class="center">Aucun brocanteur validé.</p>
        <?php else: ?>
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Zone</th>
                            <th>Emplacement</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($brocanteurs_valides as $brocanteur): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($brocanteur['bid']); ?></td>
                                <td><?php echo htmlspecialchars($brocanteur['nom']); ?></td>
                                <td><?php echo htmlspecialchars($brocanteur['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($brocanteur['zone_nom'] ?? 'Non assigné'); ?></td>
                                <td><?php echo htmlspecialchars($brocanteur['emplacement_code'] ?? 'Non assigné'); ?></td>
                                <td class="actions">
                                    <a href="vendeur.php?id=<?php echo $brocanteur['bid']; ?>" class="btn-small">Voir</a>
                                    <a href="attribuerEmplacement.php?id=<?php echo $brocanteur['bid']; ?>" class="btn-small">Emplacement</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>