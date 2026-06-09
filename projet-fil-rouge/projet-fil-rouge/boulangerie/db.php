<?php
/*
 * db.php - Connexion à la base de données MySQL avec PDO
 * (PHP : connexion BD / SQL : base de données boulangerie_ouahabi)
 * Configurer selon XAMPP : user root, mot de passe vide
 */

$host   = "localhost";
$dbname = "boulangerie_ouahabi";
$user   = "root";
$pass   = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
