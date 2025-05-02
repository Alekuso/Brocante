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
            <?php if (Brocanteur::estConnecte()): ?>
                <li class="btn">
                    <a href="<?php echo Brocanteur::estAdmin() ? 'espaceAdministrateur.php' : 'espaceBrocanteur.php'; ?>">
                        <?php echo Brocanteur::estAdmin() ? 'Espace Administrateur' : 'Espace Brocanteur'; ?>
                    </a>
                </li>
                <li class="btn">
                    <a href="logout.php">
                        Déconnexion
                    </a>
                </li>
            <?php else: ?>
                <li class="btn">
                    <a href="connexion.php">
                        Connexion
                    </a>
                </li>
                <li class="btn">
                    <a href="inscription.php">
                        S'inscrire
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<?php
// These includes aren't needed in the header since they should be included in the pages that use them
/* 
include_once 'php/Database.php';
include_once 'php/Objet.php';
include_once 'php/Categorie.php';
*/
?>