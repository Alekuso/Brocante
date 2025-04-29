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

            <form action="todo" method="get" class="bg-darkgray desk-pad-2 rounded-sm">
                <label for="nom">Nom</label>
                <input class="size-half" type="text" id="nom" name="nom" placeholder="nom de l'objet" required>

                <label for="categorie">Catégorie</label>
                <select id="categorie" name="category">
                    <option value="*">Toutes les catégories</option>
                    <option value="jeu">Jeu</option>
                    <option value="ancien">Ancien</option>
                    <option value="collection">Collection</option>
                    <option value="cassette">Cassette</option>
                    <option value="cd">CD</option>
                    <option value="autre">Autre</option>
                </select>

                <label for="prix-filtre">Filtre</label>
                <select id="prix-filtre" name="price">
                    <option value="asc">Prix ascendant</option>
                    <option value="desc">Prix descendant</option>
                </select>
                <button type="submit">Rechercher</button>
            </form>
        </article>
    </section>

    <section class="articles articles-grow">
        <a href="produit.php">
            <img src="images/placeholder.png" alt="article" />
            <h4>Article 1</h4>
            <p>Jean Philippe - Zone A</p>
            <p>Description de l'article 1 avec quelques détails intéressants</p>
            <ul>
                <li class="pad-lr-1 flex">
                    <p class="center">
                        cat1
                    </p>
                </li>
                <li class="pad-lr-1 flex">
                    <p class="center">
                        cat2
                    </p>
                </li>
                <li class="pad-lr-1 flex">
                    <p class="center">
                        cat3
                    </p>
                </li>
            </ul>
            <h3 class="prix">12.50€</h3>
        </a>
        <a href="produit.php">
            <img src="images/placeholder.png" alt="article" />
            <h4>Article 2</h4>
            <p>Michel Dupont - Zone D</p>
            <p>Un bel article de collection à ne pas manquer</p>
            <ul>
                <li class="pad-lr-1 flex">
                    <p class="center">
                        cat1
                    </p>
                </li>
                <li class="pad-lr-1 flex">
                    <p class="center">
                        cat2
                    </p>
                </li>
            </ul>
            <h3 class="prix">4.99€</h3>
        </a>
        <a href="produit.php">
            <img src="images/placeholder.png" alt="article" />
            <h4>Article 3</h4>
            <p>Sophie Martin - Zone B</p>
            <p>Pièce rare à saisir rapidement</p>
            <ul>
                <li class="pad-lr-1 flex">
                    <p class="center">
                        cat1
                    </p>
                </li>
            </ul>
            <h3 class="prix">2.99€</h3>
        </a>

    </section>
</main>
<?php include 'inc/footer.php'; ?>
</body>

</html>