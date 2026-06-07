<?php
// auth.php - Gestion de la connexion admin avec les sessions

// On demarre la session (a inclure en haut de chaque page)
session_start();

// Renvoie true si un admin est connecte
function est_connecte() {
    return isset($_SESSION['admin_id']);
}

// A appeler en haut des pages reservees a l'admin.
// Si l'utilisateur n'est pas connecte, on le renvoie vers le login.
function exiger_connexion() {
    if (!est_connecte()) {
        header('Location: login.php');
        exit;
    }
}
