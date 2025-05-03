<?php
include_once 'php/Brocanteur.php';

use Brocante\Modele\Brocanteur;

// Déconnecte l'utilisateur
Brocanteur::deconnecter();

// Redirige vers la page d'accueil
header('Location: index.php');
exit; 