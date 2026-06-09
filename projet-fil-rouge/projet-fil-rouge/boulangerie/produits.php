<?php
/*
 * produits.php - Catalogue des produits (site public)
 * PHP : $_GET pour filtrer par catégorie, requêtes SQL (SELECT, JOIN, WHERE)
 */
require_once 'db.php';
require_once 'auth.php';
require_once 'includes/helpers.php';

// Filtre par catégorie depuis l'URL (PHP : $_GET)
$categorieId = isset($_GET['categorie']) ? (int)$_GET['categorie'] : 0;

$stmtCat = $pdo->query("SELECT * FROM categorie ORDER BY nom_categorie");
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

$activePage = 'produits';
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

<?php require 'includes/site-header.php'; ?>

<section class="catalog">
  <div class="container-wide">

    <header class="catalog-top">
      <h1><?= htmlspecialchars($activeCatNom) ?></h1>
      <p><?= count($produits) ?> produit<?= count($produits) > 1 ? 's' : '' ?></p>
      <nav class="catalog-filters">
        <a href="produits.php" class="<?= $categorieId === 0 ? 'active' : '' ?>">Tous</a>
        <?php foreach ($categories as $cat): ?>
          <a href="produits.php?categorie=<?= $cat['id'] ?>"
             class="<?= $categorieId === (int)$cat['id'] ? 'active' : '' ?>">
            <?= htmlspecialchars($cat['nom_categorie']) ?>
          </a>
        <?php endforeach; ?>
      </nav>
    </header>

    <?php if (empty($produits)): ?>
      <div class="catalog-empty">
        <p>🍞</p>
        <h3>Aucun produit trouvé</h3>
        <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
      </div>
    <?php else: ?>
      <div class="products-grid">
        <?php foreach ($produits as $prod): ?>
          <?php $pimg = produitImage($prod['image']); ?>
          <article class="product-card">
            <?php if ($pimg): ?>
              <img src="<?= htmlspecialchars($pimg) ?>" alt="<?= htmlspecialchars($prod['nom_produit']) ?>" class="product-img">
            <?php else: ?>
              <div class="product-img product-img-empty">🥖</div>
            <?php endif; ?>
            <div class="product-info">
              <p class="product-cat"><?= htmlspecialchars($prod['nom_categorie'] ?? '') ?></p>
              <h3><?= htmlspecialchars($prod['nom_produit']) ?></h3>
              <p class="product-price"><?= number_format($prod['prix'], 2) ?> MAD</p>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php require 'includes/site-footer.php'; ?>

</body>
</html>
