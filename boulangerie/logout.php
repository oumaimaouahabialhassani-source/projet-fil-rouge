<?php
// logout.php - Deconnexion de l'admin
require_once 'auth.php';

// On vide et on detruit la session
$_SESSION = [];
session_destroy();

header('Location: index.php');
exit;
