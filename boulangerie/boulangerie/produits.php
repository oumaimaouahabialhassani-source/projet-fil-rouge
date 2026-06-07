<?php
// produits.php - Catalogue complet des produits (avec filtre par categorie)
require_once 'db.php';
require_once 'auth.php';

$categorieId = isset($_GET['categorie']) ? (int)$_GET['categorie'] : 0;

$stmtCat    = $pdo->query("SELECT * FROM categorie ORDER BY nom_categorie");
$categories = $stmtCat->fetchAll();

if ($categorieId > 0) {
    $stmt = $pdo->prepare("
        SELECT p.*, c.nom_categorie
        FROM produit p LEFT JOIN categorie c ON p.categorie_id = c.id
        WHERE p.categorie_id = ?
        ORDER BY p.nom_produit
    ");
    $stmt->execute([$categorieId]);
} else {
    $stmt = $pdo->query("
        SELECT p.*, c.nom_categorie
        FROM produit p LEFT JOIN categorie c ON p.categorie_id = c.id
        ORDER BY p.nom_produit
    ");
}
$produits = $stmt->fetchAll();

$activeCatNom = 'Tous les produits';
foreach ($categories as $c) {
    if ((int)$c['id'] === $categorieId) { $activeCatNom = $c['nom_categorie']; break; }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Produits - Boulangerie Ouahabi</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
  <div class="navbar-inner">
    <a href="index.php" class="navbar-brand">
      <span class="brand-main">Boulangerie Ouahabi</span>
      <span class="brand-sub">Artisan Boulanger - Casablanca</span>
    </a>
    <ul class="navbar-links">
      <li><a href="index.php">Accueil</a></li>
      <li><a href="index.php#about">À propos</a></li>
      <li><a href="produits.php" class="active">Produits</a></li>
      <li><a href="index.php#categories">Catégories</a></li>
    </ul>
    <div class="navbar-actions">
      <?php if (est_connecte()): ?>
        <a href="ajouter.php" class="btn btn-primary">+ Ajouter</a>
        <a href="logout.php" class="btn btn-secondary">Deconnexion</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-secondary">Connexion admin</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="page-header" data-title="PRODUITS">
  <div class="page-header-inner">
    <p class="page-header-eyebrow">Notre catalogue</p>
    <h1><em><?= htmlspecialchars($activeCatNom) ?></em></h1>
    <p><?= count($produits) ?> produit<?= count($produits) > 1 ? 's' : '' ?> disponible<?= count($produits) > 1 ? 's' : '' ?></p>
    <p class="breadcrumb">
      <a href="index.php">Accueil</a><span>/</span>
      <a href="produits.php">Produits</a>
      <?php if ($categorieId): ?>
        <span>/</span><?= htmlspecialchars($activeCatNom) ?>
      <?php endif; ?>
    </p>
  </div>
</div>

<div class="filters-bar">
  <div class="filters-inner">
    <div class="filter-tabs">
      <a href="produits.php" class="filter-tab <?= $categorieId === 0 ? 'active' : '' ?>">Tous</a>
      <?php foreach ($categories as $cat): ?>
        <a href="produits.php?categorie=<?= $cat['id'] ?>"
           class="filter-tab <?= $categorieId === (int)$cat['id'] ? 'active' : '' ?>">
          <?= htmlspecialchars($cat['nom_categorie']) ?>
        </a>
      <?php endforeach; ?>
    </div>
    <?php if (est_connecte()): ?>
      <a href="ajouter.php" class="btn btn-primary btn-sm">+ Nouveau produit</a>
    <?php endif; ?>
  </div>
</div>

<div style="padding:3rem;background:var(--off-white);">
  <div style="max-width:1280px;margin:0 auto;">
    <?php if (empty($produits)): ?>
      <div style="text-align:center;padding:6rem 2rem;">
        <div style="font-size:5rem;margin-bottom:1.5rem;">🍞</div>
        <h3 style="font-family:var(--font-display);font-size:2rem;color:var(--brown-dark);margin-bottom:0.5rem;">Aucun produit trouvé</h3>
        <p style="color:var(--text-muted);margin-bottom:2rem;">Commencez par ajouter vos premiers produits au catalogue.</p>
        <a href="ajouter.php" class="btn btn-primary">+ Ajouter un produit</a>
      </div>
    <?php else: ?>
      <div class="products-grid">
        <?php foreach ($produits as $prod): ?>
          <div class="product-card">
            <div class="product-img-wrap">
              <?php if (!empty($prod['image']) && file_exists('images/'.$prod['image'])): ?>
                <img src="images/<?= htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['nom_produit']) ?>">
              <?php else: ?>
                <div class="product-img-placeholder">🥖</div>
              <?php endif; ?>
              <span class="product-badge"><?= htmlspecialchars($prod['nom_categorie'] ?? '-') ?></span>
            </div>
            <div class="product-body">
              <p class="product-category"><?= htmlspecialchars($prod['nom_categorie'] ?? '') ?></p>
              <h3 class="product-name"><?= htmlspecialchars($prod['nom_produit']) ?></h3>
              <div class="product-divider"></div>
              <div class="product-meta">
                <span class="product-price"><?= number_format($prod['prix'],2) ?> MAD</span>
                <span class="product-stock" style="<?= $prod['quantite_stock'] < 5 ? 'color:var(--rust);font-weight:500;' : '' ?>">
                  Qté: <?= (int)$prod['quantite_stock'] ?>
                </span>
              </div>
              <?php if (est_connecte()): ?>
              <div class="product-actions">
                <a href="modifier.php?id=<?= $prod['id'] ?>" class="btn btn-secondary btn-sm">✏ Modifier</a>
                <a href="supprimer.php?id=<?= $prod['id'] ?>" class="btn btn-danger btn-sm">✕ Supprimer</a>
              </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<footer class="footer">
  <div class="footer-inner">
    <div>
      <div class="footer-brand">Boulangerie Ouahabi</div>
      <div class="footer-tagline">Artisan Boulanger - Casablanca - Depuis 1985</div>
    </div>
    <div class="footer-copy">© <?= date('Y') ?> Boulangerie Ouahabi</div>
  </div>
</footer>

</body>
</html>
