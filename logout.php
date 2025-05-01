<?php
include_once 'php/Brocanteur.php';

// Déconnecter l'utilisateur
Brocanteur::deconnecter();

// Rediriger vers la page d'accueil
header('Location: index.php');
exit; 