<?php
include_once 'php/Database.php';
include_once 'php/Objet.php';
include_once 'php/Brocanteur.php';
include_once 'php/Categorie.php';

use Brocante\Base\Database;
use Brocante\Modele\Objet;
use Brocante\Modele\Brocanteur;
use Brocante\Modele\Categorie;

// Initialise les paramètres de recherche
$nom = isset($_GET['nom']) ? trim($_GET['nom']) : '';
$categorieId = isset($_GET['category']) && $_GET['category'] !== '*' ? $_GET['category'] : null;
$prixFiltre = isset($_GET['price']) ? $_GET['price'] : 'asc';

// Récupère les catégories pour le formulaire
$categories = Categorie::obtenirToutes();

// Récupère tous les objets
$db = Database::getInstance();
$params = [];

$sql = "SELECT o.*, c.intitule as categorie_nom, b.nom as brocanteur_nom, b.prenom as brocanteur_prenom, 
               b.description as brocanteur_description, b.photo as brocanteur_photo, b.bid as brocanteur_id, 
               z.nom as zone_nom, e.code as emplacement_code
        FROM Objet o
        LEFT JOIN Categorie c ON o.cid = c.cid
        LEFT JOIN Brocanteur b ON o.bid = b.bid
        LEFT JOIN Emplacement e ON b.bid = e.bid
        LEFT JOIN Zone z ON e.zid = z.zid
        WHERE b.visible = 1";

if (!empty($nom)) {
    $sql .= " AND (o.intitule LIKE ? OR o.description LIKE ?)";
    $params[] = "%$nom%";
    $params[] = "%$nom%";
}

if (!empty($categorieId)) {
    $sql .= " AND o.cid = ?";
    $params[] = $categorieId;
}

$sql .= " ORDER BY o.prix " . ($prixFiltre === 'desc' ? 'DESC' : 'ASC');

$resultats = $db->obtenirTous($sql, $params);

// Transforme les résultats
$objets_data = [];
foreach ($resultats as $row) {
    $objet = new Objet($row);
    
    $objets_data[] = [
        'objet' => $objet,
        'categorie_nom' => $row['categorie_nom'],
        'brocanteur_nom' => $row['brocanteur_nom'],
        'brocanteur_prenom' => $row['brocanteur_prenom'],
        'brocanteur_id' => $row['brocanteur_id'],
        'zone_nom' => $row['zone_nom'],
        'emplacement_code' => $row['emplacement_code']
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante - Objets</title>
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
            <h1>Objets</h1>
        </article>
    </section>
    <section id="contactFormContainer" class="container">
        <article class="contactForm">

            <form action="objets.php" method="get" class="bg-darkgray desk-pad-2 rounded-sm">
                <label for="nom">Nom</label>
                <input class="size-half" type="text" id="nom" name="nom" placeholder="nom de l'objet" value="<?php echo htmlspecialchars($nom); ?>">

                <label for="categorie">Catégorie</label>
                <select id="categorie" name="category">
                    <option value="*">Toutes les catégories</option>
                    <?php 
                    foreach ($categories as $categorie) {
                        echo '<option value="' . htmlspecialchars($categorie->cid) . '" ' . (($categorieId == $categorie->cid) ? 'selected' : '') . '>';
                        echo htmlspecialchars($categorie->intitule);
                        echo '</option>';
                    }
                    ?>
                </select>

                <label for="prix-filtre">Filtre</label>
                <select id="prix-filtre" name="price">
                    <option value="asc" <?php echo ($prixFiltre === 'asc') ? 'selected' : ''; ?>>Prix ascendant</option>
                    <option value="desc" <?php echo ($prixFiltre === 'desc') ? 'selected' : ''; ?>>Prix descendant</option>
                </select>
                <button type="submit">Rechercher</button>
            </form>
        </article>
    </section>

    <section class="articles articles-grow objets-grid">
        <?php 
        if (empty($objets_data)) {
            echo '<p class="center">Aucun objet ne correspond à votre recherche.</p>';
        } else {
            foreach ($objets_data as $data) {
                $article = $data['objet'];
                echo '<a href="produit.php?id=' . htmlspecialchars($article->oid) . '" class="objet-card">';
                
                if ($article->image === null) {
                    $image = "images/placeholder.png";
                } else {
                    $image = "uploads/objets/" . htmlspecialchars($article->image);
                }
                
                echo '<img src="' . $image . '" alt="' . htmlspecialchars($article->intitule) . '" />';
                echo '<h4>' . htmlspecialchars($article->intitule) . '</h4>';
                
                if (!empty($data['brocanteur_nom'])) {
                    echo '<p>';
                    echo htmlspecialchars($data['brocanteur_prenom'] . ' ' . $data['brocanteur_nom']);
                    echo !empty($data['zone_nom']) ? ' - ' . htmlspecialchars($data['zone_nom']) : '';
                    echo '</p>';
                }
                
                echo '<p>' . htmlspecialchars($article->description) . '</p>';
                
                if (!empty($data['categorie_nom'])) {
                    echo '<ul>';
                    echo '<li class="pad-lr-1 flex">';
                    echo '<p class="center">';
                    echo htmlspecialchars($data['categorie_nom']);
                    echo '</p>';
                    echo '</li>';
                    echo '</ul>';
                }
                
                echo '<h3 class="prix">' . htmlspecialchars($article->prix) . '€</h3>';
                echo '</a>';
            }
        }
        ?>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>