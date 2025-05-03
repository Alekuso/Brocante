<footer>
    <p class="footer-nom">Supra Brocante - 2024 ~ 2025</p>
    <ul>
        <li><a href="index.php">Accueil</a></li>
        <li><a href="brocanteurs.php">Brocanteurs</a></li>
        <li><a href="objets.php">Objets</a></li>
        <li><a href="contact.php">Contact</a></li>
    </ul>
    <p class="footer-addresse">Du 10 au 12 mars - Rue Grand Pré, Flémalle 4400</p>
    <?php 
    if (class_exists('Brocanteur') && Brocanteur::estConnecte()) {
        echo "<p class=\"footer-user\">Connecté: " . htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) . "</p>";
    }
    ?>
</footer>