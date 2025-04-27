<?php
include_once 'inc/db.php';

$db = new DB();
$objets = $db->getRandomObjets();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supra Brocante</title>
    <link rel="icon" href="images/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include 'inc/header.php'; ?>
<main>
    <section id="presentation">
        <article class="indexpresent">
            <p>Votre brocante annuelle porte sur le thème du rétro !</p>
            <p>Du 10 au 12 mars</p>
            <p>Lieu : Rue Grand Pré, Flémalle 4400</p>
            <p>Frais d'inscription brocanteur : 20€</p>
            <ul class="container center column">
                <li>
                    <a class="btn" href="inscription.php">
                        S'inscrire
                    </a>
                </li>
                <li>
                    <a class="btn" href="contact.php">
                        Nous contacter
                    </a>
                </li>
            </ul>
        </article>
        <img class="zone" src="images/zone_A.png" alt="Photo représentative de l'endroit où se trouve la zone A de la brocante" />
    </section>
    <section id="brocanteurs">
        <article class="center">
            <h1>Objets aléatoires</h1>
        </article>
    </section>
    <section class="articles articles-grow">
        <?php
        $arrayString = "";
        foreach ($objets as $article) {
            $arrayString .= "<a href='objet.php?id=" . htmlspecialchars($article["oid"]) . "'>";
            if ($article["image"] == null) {
                $article["image"] = "images/placeholder.png";
            } else {
                $article["image"] = "uploads/" . htmlspecialchars($article["image"]);
            }
            $arrayString .= "<img class='center' src='" . $article["image"] . "' alt='" . htmlspecialchars($article["intitule"]) . "' />";
            $arrayString .= "<h4>" . htmlspecialchars($article["intitule"]) . "</h4>";
            $arrayString .= "<p>" . htmlspecialchars($article["description"]) . "</p>";
            $arrayString .= "<p>" . htmlspecialchars($article["prix"]) . "€</p>";
            $arrayString .= "</a>";
        }
        echo $arrayString;
        ?>
    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>