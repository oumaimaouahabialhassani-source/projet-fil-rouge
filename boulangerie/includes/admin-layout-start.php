<?php
/*
 * admin-layout-start.php - Début du layout admin (pages à la racine : ajouter, modifier...)
 * PHP : include/require pour réutiliser le menu et l'en-tête
 */
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/helpers.php';

exiger_connexion();

if (!isset($adminPage)) $adminPage = '';
if (!isset($adminTitle)) $adminTitle = 'Back office';
if (!isset($adminSubtitle)) $adminSubtitle = '';

$message = lireMessage();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($adminTitle) ?> - Administration</title>
  <link rel="stylesheet" href="<?= baseUrl() ?>css/style.css">
</head>
<body class="admin-body">

<div class="admin-shell">
  <aside class="admin-sidebar">
    <a href="admin/index.php" class="admin-sidebar-brand">
      <img src="<?= baseUrl() ?>image/logo.svg" alt="" width="40" height="40">
      <div>
        <span class="admin-brand-name">Ouahabi</span>
        <span class="admin-brand-role">Back office</span>
      </div>
    </a>

    <nav class="admin-nav">
      <p class="admin-nav-label">Menu</p>
      <a href="admin/index.php" class="admin-nav-link <?= $adminPage === 'dashboard' ? 'active' : '' ?>">
        <span class="admin-nav-icon">◈</span> Tableau de bord
      </a>
      <a href="admin/produits.php" class="admin-nav-link <?= $adminPage === 'produits' ? 'active' : '' ?>">
        <span class="admin-nav-icon">◫</span> Produits
      </a>
      <a href="admin/categories.php" class="admin-nav-link <?= $adminPage === 'categories' ? 'active' : '' ?>">
        <span class="admin-nav-icon">◧</span> Catégories
      </a>
      <a href="admin/commandes.php" class="admin-nav-link <?= $adminPage === 'commandes' ? 'active' : '' ?>">
        <span class="admin-nav-icon">◎</span> Commandes
      </a>
      <a href="ajouter.php" class="admin-nav-link <?= $adminPage === 'ajouter' ? 'active' : '' ?>">
        <span class="admin-nav-icon">＋</span> Nouveau produit
      </a>
    </nav>

    <div class="admin-sidebar-footer">
      <a href="index.php" class="admin-nav-link">
        <span class="admin-nav-icon">↗</span> Voir le site
      </a>
      <a href="logout.php" class="admin-nav-link admin-nav-logout">
        <span class="admin-nav-icon">⏻</span> Déconnexion
      </a>
      <div class="admin-user">
        <span class="admin-user-dot"></span>
        <?= htmlspecialchars($_SESSION['admin_login'] ?? 'admin') ?>
      </div>
    </div>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <div>
        <p class="admin-topbar-eyebrow">Administration</p>
        <h1 class="admin-topbar-title"><?= htmlspecialchars($adminTitle) ?></h1>
        <?php if ($adminSubtitle): ?>
          <p class="admin-topbar-sub"><?= htmlspecialchars($adminSubtitle) ?></p>
        <?php endif; ?>
      </div>
      <div class="admin-topbar-actions">
        <?php if (!empty($adminTopAction)): ?>
          <?= $adminTopAction ?>
        <?php endif; ?>
      </div>
    </header>

    <main class="admin-content">
      <?php if ($message): ?>
        <div class="alert alert-<?= $message['type'] == 'success' ? 'success' : 'danger' ?> admin-flash">
          <?= $message['type'] == 'success' ? '✓' : '⚠' ?> <?= htmlspecialchars($message['texte']) ?>
        </div>
      <?php endif; ?>
