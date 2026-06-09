<?php
/*
 * site-header.php - En-tête du site public (menu de navigation)
 * PHP : include/require, conditions (page active)
 */
if (!isset($activePage)) $activePage = '';
?>
<nav class="navbar">
  <div class="navbar-inner">
    <a href="index.php" class="navbar-brand">
      <img src="image/logo.svg" alt="" class="navbar-logo" width="44" height="44">
      <span class="navbar-brand-text">
        <span class="brand-main">Boulangerie Ouahabi</span>
        <span class="brand-sub">Artisan Boulanger · Tanger</span>
      </span>
    </a>
    <ul class="navbar-links">
      <li><a href="index.php" class="<?= $activePage === 'accueil' ? 'active' : '' ?>">Accueil</a></li>
      <li><a href="index.php#about" class="<?= $activePage === 'about' ? 'active' : '' ?>">À propos</a></li>
      <li><a href="produits.php" class="<?= $activePage === 'produits' ? 'active' : '' ?>">Produits</a></li>
      <li><a href="index.php#categories" class="<?= $activePage === 'categories' ? 'active' : '' ?>">Catégories</a></li>
    </ul>
  </div>
</nav>
