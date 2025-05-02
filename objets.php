<?php
include_once 'php/Database.php';
include_once 'php/Objet.php';
include_once 'php/Brocanteur.php';
include_once 'php/Categorie.php';

use Brocante\Base\Database;
use Brocante\Modele\Objet;
use Brocante\Modele\Brocanteur;
use Brocante\Modele\Categorie;

// Initialisation des paramètres de recherche
$nom = isset($_GET['nom']) ? trim($_GET['nom']) : '';
$categorieId = isset($_GET['category']) && $_GET['category'] !== '*' ? $_GET['category'] : null;
$prixFiltre = isset($_GET['price']) ? $_GET['price'] : 'asc';

// Récupération des catégories pour le formulaire
$categories = Categorie::obtenirToutes();

// Récupération des objets avec les filtres
$objets = Objet::rechercher($nom, $categorieId, $prixFiltre);
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
                    <?php foreach ($categories as $categorie): ?>
                        <option value="<?php echo htmlspecialchars($categorie->cid); ?>" <?php echo ($categorieId == $categorie->cid) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($categorie->intitule); ?>
                        </option>
                    <?php endforeach; ?>
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
        <?php if (empty($objets)): ?>
            <p class="center">Aucun objet ne correspond à votre recherche.</p>
        <?php else: ?>
            <?php foreach ($objets as $article):
                $brocanteur = $article->obtenirBrocanteur();
                $categorie = $article->obtenirCategorie();
            ?>
                <a href="produit.php?id=<?php echo htmlspecialchars($article->oid); ?>" class="objet-card">
                    <?php
                    if ($article->image === null) {
                        $image = "images/placeholder.png";
                    } else {
                        $image = "uploads/objets/" . htmlspecialchars($article->image);
                    }
                    ?>
                    <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($article->intitule); ?>" />
                    <h4><?php echo htmlspecialchars($article->intitule); ?></h4>
                    
                    <?php if ($brocanteur): ?>
                        <?php $zone = $brocanteur->obtenirZone(); ?>
                        <p>
                            <?php echo htmlspecialchars($brocanteur->prenom . ' ' . $brocanteur->nom); ?>
                            <?php echo $zone ? ' - ' . htmlspecialchars($zone->nom) : ''; ?>
                        </p>
                    <?php endif; ?>
                    
                    <p><?php echo htmlspecialchars($article->description); ?></p>
                    
                    <?php if ($categorie): ?>
                        <ul>
                            <li class="pad-lr-1 flex">
                                <p class="center">
                                    <?php echo htmlspecialchars($categorie->intitule); ?>
                                </p>
                            </li>
                        </ul>
                    <?php endif; ?>
                    
                    <h3 class="prix"><?php echo htmlspecialchars($article->prix); ?>€</h3>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>