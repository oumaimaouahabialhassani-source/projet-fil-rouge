<?php
/*
 * auth.php - Connexion administrateur avec les sessions PHP
 * (PHP : sessions, conditions, fonctions, header/redirection)
 */

session_start();

// Vérifie si l'admin est connecté
function est_connecte() {
    return isset($_SESSION['admin_id']);
}

// Protège une page admin : redirige vers login si non connecté
function exiger_connexion() {
    if (!est_connecte()) {
        // Si la page est dans le dossier admin/, on remonte d'un niveau
        if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) {
            header('Location: ../login.php');
        } else {
            header('Location: login.php');
        }
        exit;
    }
}
