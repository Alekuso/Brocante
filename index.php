<?php
/* Article {
 *  intitule: String,
 *  prix: Number,
 *  image: String,
 *  categories: [
 *      intitule: String
 * ],
 *  Brocanteur {
 *      nom: String,
 *      prenom: String,
 *      zone: String
 *  }
 * }
 */
$articles = [
    [
        "intitule" => "Article 1",
        "prix" => 12.50,
        "image" => "images/placeholder.png",
        "categories" => [
            [
                "intitule" => "cat1"
            ],
            [
                "intitule" => "cat2"
            ],
            [
                "intitule" => "cat3"
            ]
        ],
        "brocanteur" => [
            "nom" => "Bro",
            "prenom" => "canteur",
            "zone" => "A"
        ]
    ],

    [
        "intitule" => "Article 2",
        "prix" => 4.99,
        "image" => "images/placeholder.png",
        "categories" => [
            [
                "intitule" => "cat1"
            ],
            [
                "intitule" => "cat2"
            ]
        ],
        "brocanteur" => [
            "nom" => "Canteur",
            "prenom" => "bro",
            "zone" => "D"
        ]
    ],

    [
        "intitule" => "Article 3",
        "prix" => 2.99,
        "image" => "images/placeholder.png",
        "categories" => [
            [
                "intitule" => "cat1"
            ]
        ],
        "brocanteur" => [
            "nom" => "William",
            "prenom" => "Joshua",
            "zone" => "B"
        ]
    ]
];
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
<header>
    <a href="index.php">
        <img id="icon" src="images/icon.png" alt="Logo Brocante">
    </a>
    <nav>
        <ul>
            <li class="btn">
                <a href="brocanteurs.php">
                    Brocanteurs
                </a>
            </li>
            <li class="btn">
                <a href="objets.php">
                    Objets
                </a>
            </li>
            <li class="btn">
                <a href="contact.php">
                    Contacter
                </a>
            </li>
            <li class="btn">

<!--                "Vous" sera affiché si l'utilisateur est connecté.-->
                <a href="connexion.php">
                    Connexion
                </a>
            </li>
        </ul>
    </nav>
</header>
<main>
    <section id="presentation">
        <article class="indexpresent">
            <p>Votre brocante annuelle porte sur le thème du rétro !</p>
            <p>Du 10 au 12 mars</p>
            <p>Lieu : Rue Grand Pré, Flémalle 4400</p>
            <p>Frais d'inscription brocanteur : 20€</p>
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
            foreach ($articles as $article) {
                $arrayString .= "<a href='objet.php'>";
                $arrayString .= "<img class='center' src='" . $article["image"] . "' alt='". $article["intitule"] ."' />";
                $arrayString .= "<h4>" . $article["intitule"] . "</h4>";
                $arrayString .= "<p>" . $article["brocanteur"]["prenom"] . " " .  $article["brocanteur"]["nom"] . " - Zone " . $article["brocanteur"]["zone"] . "</p>";
                $arrayString .= "<ul>";
                foreach ($article["categories"] as $categorie) {
                    $arrayString .= "<li class='pad-lr-1 flex'>";
                    $arrayString .= "<p class='center'>";
                    $arrayString .= $categorie["intitule"];
                    $arrayString .= "</p>";
                    $arrayString .= "</li>";
                }
                $arrayString .= "</ul>";
                $arrayString .= "<p>" . $article["prix"] . "€</p>";
                $arrayString .= "</a>";
            }

            echo "$arrayString"
        ?>
    </section>
</main>
<footer>
    <p>Brocante - 2024 ~ 2025</p>
</footer>
</body>

</html>