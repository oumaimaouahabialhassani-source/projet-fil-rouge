<?php
// index.php - Page d'accueil de la Boulangerie Ouahabi
require_once 'db.php';
require_once 'auth.php';

$stmtCat  = $pdo->query("SELECT * FROM categorie ORDER BY nom_categorie");
$categories = $stmtCat->fetchAll();

$stmtProd = $pdo->query("
    SELECT p.*, c.nom_categorie
    FROM produit p
    LEFT JOIN categorie c ON p.categorie_id = c.id
    ORDER BY p.quantite_stock DESC
    LIMIT 6
");
$topProduits = $stmtProd->fetchAll();

// Petit icone selon le nom de la categorie
function catIcon($nom){
    $nom = strtolower($nom);

    if ($nom == 'boulangerie') return 'image/Boulangerie.jpg';
    if ($nom == 'viennoiserie') return 'image/Viennoiserie.jpg';
    if ($nom == 'patisserie marocaine') return 'image/PatisserieMarocaine.jpg';
    if ($nom == 'patisserie moderne') return 'image/PatisserieModerne.jpg';
    if ($nom == 'tartes et gateaux') return 'image/TartesEtGateaux.jpg';

    return 'img/default.jpg';
}?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Boulangerie Ouahabi - Artisan Boulanger - Tanger</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
  <div class="navbar-inner">
    <a href="index.php" class="navbar-brand">
      <span class="brand-main">Boulangerie Ouahabi</span>
      <span class="brand-sub">Artisan Boulanger - Tanger</span>
    </a>
    <ul class="navbar-links">
      <li><a href="index.php" class="active">Accueil</a></li>
      <li><a href="#about">À propos</a></li>
      <li><a href="produits.php">Produits</a></li>
      <li><a href="#categories">Catégories</a></li>
    </ul>
    <div class="navbar-actions">
      <?php if (est_connecte()): ?>
        <a href="ajouter.php" class="btn btn-primary">+ Ajouter produit</a>
        <a href="logout.php" class="btn btn-secondary">Deconnexion</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-secondary">Connexion admin</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- HERO - Split layout -->
<section class="hero">
  <!-- Texte gauche -->
  <div class="hero-left">
    <p class="hero-eyebrow">Depuis 2001 - Tanger</p>
    <h1>L'art du pain<br><em>artisanal</em><br>au quotidien</h1>
    <p class="hero-desc">
      Pains, viennoiseries et pâtisseries façonnés chaque matin
      avec passion, tradition et les meilleurs ingrédients du terroir marocain.
    </p>
    <div class="hero-ctas">
      <a href="produits.php" class="btn btn-primary">Voir nos produits</a>
      <a href="#about" class="btn btn-secondary">Notre histoire</a>
    </div>
    <div class="hero-scroll">
      <span class="hero-scroll-line"></span>
      <span>Défiler</span>
    </div>
  </div>

  <!-- Image droro-right-overlay">

     Mini prodite -->
  <div class="hero-right">
    <div style="
    width:100%;min-height:500px;background:linear-gradient(135deg,#C9A97A,#6B3F22);display:flex;align-items:center;justify-content:center;">

  <img src="image/Hero.jpg" alt="bread" style="
    width:570px;
    max-width:100%;
    height:auto;">

</section>

<!-- ABOUT -->
<section class="about-section" id="about">
  <div class="container">
    <div class="about-layout">
      <div class="about-text-col">
        <p class="section-eyebrow">Notre histoire</p>
        <h2 class="section-title">La passion du pain<br><em>depuis 25 ans</em></h2>
        <p>Fondée en 2001 par la famille Ouahabi, notre boulangerie perpétue les traditions artisanales marocaines tout en intégrant les meilleures techniques françaises de boulangerie.</p>
        <p>Chaque matin avant l'aube, nos artisans boulangers pétrissent, façonnent et cuisent à la main pour vous offrir des produits d'exception.</p>
        <ul class="about-list">
          <li>Farines locales sélectionnées</li>
          <li>Recettes marocaines traditionnelles au savoir-faire artisanal.</li>
          <li>Sans conservateurs ni additifs</li>
          <li>Ouvert 7 jours sur 7 dès 6h00</li>
        </ul>
        <a href="produits.php" class="btn btn-primary">Découvrir nos produits</a>
      </div>

      <div class="about-images">
        <div class="about-img tall" style="background:var(--beige);display:flex;align-items:center;justify-content:center;"><img src="image/img1.jpg" alt="Pain" style="width:80%;height:auto;"></div>
        <div class="about-img" style="background:var(--beige-warm);display:flex;align-items:center;justify-content:center;"><img src="image/img2.jpg" alt="Pain" style="width:80%;height:auto;"></div>
        <div class="about-img" style="background:#E8D0B0;display:flex;align-items:center;justify-content:center;"><img src="image/img3.jpg" alt="Pain" style="width:80%;height:auto;"></div>
      </div>
    </div>
  </div>
</section>

<!-- Stats strip -->
<div style="background:var(--brown-dark);padding:3rem;">
  <div class="about-stats-strip" style="max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(4,1fr);">
    <div class="stat-item">
      <div class="stat-num">40+</div>
      <div class="stat-label">Années d'expérience</div>
    </div>
    <div class="stat-item">
      <div class="stat-num"><?= count($categories) ?></div>
      <div class="stat-label">Catégories</div>
    </div>
    <div class="stat-item">
      <div class="stat-num">100%</div>
      <div class="stat-label">Fait main chaque jour</div>
    </div>
    <div class="stat-item" style="border-right:none;">
      <div class="stat-num">6h</div>
      <div class="stat-label">Ouverture quotidienne</div>
    </div>
  </div>
</div>

<!-- CATÉGORIES -->
<section class="categories-section" id="categories">
  <div class="container">
    <div class="section-header centered">
      <p class="section-eyebrow" style="justify-content:center;">Ce que nous faisons</p>
      <h2 class="section-title">Nos <em>catégories</em></h2>
    </div>
    <div class="cat-grid">
      <?php foreach ($categories as $cat): ?>
        <a href="produits.php?categorie=<?= $cat['id'] ?>" class="cat-card">
          <div class="cat-icon">
            <img src="<?= catIcon($cat['nom_categorie']) ?>" alt="">
          </div>
          <div class="cat-name"><?= htmlspecialchars($cat['nom_categorie']) ?></div>
        </a>
      <?php endforeach; ?>
      <?php if (empty($categories)): ?>
        <p style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--text-muted);">Aucune catégorie trouvée.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- TÉMOIGNAGE -->
<div class="testimonial-band">
  <div class="testimonial-img">
    <div class="testimonial-img-placeholder">🧑‍🍳</div>
  </div>
  <div class="testimonial-content">
    <div class="quote-mark">"</div>
    <p class="quote-text">
      Chaque matin, nous sélectionnons les meilleures farines et pétrissons à la main pour vous offrir un pain qui sent bon, qui croustille, et qui vous rappelle les vraies saveurs d'autrefois.
    </p>
    <p class="quote-author">- Mohamed Ouahabi, Fondateur</p>
  </div>
</div>

<!-- MEILLEURS PRODUITS -->
<?php if (!empty($topProduits)): ?>
<section class="products-section">
  <div class="container">
    <div class="section-header" style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:3rem;">
      <div>
        <p class="section-eyebrow">Sélection du jour</p>
        <h2 class="section-title">Nos <em>meilleurs</em> produits</h2>
      </div>
      <a href="produits.php" class="btn btn-secondary" style="flex-shrink:0;">Tous les produits →</a>
    </div>
    <div class="products-grid">
      <?php foreach ($topProduits as $prod): ?>
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
              <span class="product-stock">Qté: <?= (int)$prod['quantite_stock'] ?></span>
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
  </div>
</section>
<?php endif; ?>

<!-- Footer -->
<footer class="footer">
  <div class="footer-inner">
    <div>
      <div class="footer-brand">Boulangerie Ouahabi</div>
      <div class="footer-tagline">Artisan Boulanger - Tanger - Depuis 1985</div>
    </div>
    <div class="footer-copy">
      © <?= date('Y') ?> Boulangerie Ouahabi<br>
      Tous droits réservés
    </div>
  </div>
</footer>

</body>
</html>
