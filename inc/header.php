<?php
include_once 'php/Brocanteur.php';
use Brocante\Modele\Brocanteur;
?>
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
            <?php 
            if (Brocanteur::estConnecte()) {
                echo "<li class=\"btn\">";
                echo "<a href=\"" . (Brocanteur::estAdmin() ? 'espaceAdministrateur.php' : 'espaceBrocanteur.php') . "\">";
                echo Brocanteur::estAdmin() ? 'Espace Administrateur' : 'Espace Brocanteur';
                echo "</a>";
                echo "</li>";
                echo "<li class=\"btn\">";
                echo "<a href=\"logout.php\">";
                echo "Déconnexion";
                echo "</a>";
                echo "</li>";
            } else {
                echo "<li class=\"btn\">";
                echo "<a href=\"connexion.php\">";
                echo "Connexion";
                echo "</a>";
                echo "</li>";
                echo "<li class=\"btn\">";
                echo "<a href=\"inscription.php\">";
                echo "S'inscrire";
                echo "</a>";
                echo "</li>";
            }
            ?>
        </ul>
    </nav>
</header>