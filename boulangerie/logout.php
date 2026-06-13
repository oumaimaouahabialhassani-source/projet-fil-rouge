<?php
/*
 * logout.php - Déconnexion de l'administrateur
 * PHP : sessions (destruction de session)
 */
require_once 'auth.php';

$_SESSION = array();
session_destroy();

header('Location: login.php');
exit;
