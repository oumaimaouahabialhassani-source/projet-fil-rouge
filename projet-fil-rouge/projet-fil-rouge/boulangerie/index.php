<?php
/*
 * index.php - Page d'accueil (site public)
 * PHP : include, connexion BD, requêtes SQL (SELECT, JOIN)
 * HTML/CSS : structure sémantique, mise en page Flexbox/Grid
 */
require_once 'db.php';
require_once 'auth.php';
require_once 'includes/helpers.php';

// Récupérer les catégories (SQL : SELECT)
$stmtCat = $pdo->query("SELECT * FROM categorie ORDER BY nom_categorie");
$categories = $stmtCat->fetchAll();

// Récupérer les 6 produits les plus en stock (SQL : SELECT + JOIN + ORDER BY + LIMIT)
$stmtProd = $pdo->query("
    SELECT p.*, c.nom_categorie
    FROM produit p
    LEFT JOIN categorie c ON p.categorie_id = c.id
    ORDER BY p.quantite_stock DESC
    LIMIT 6
");
$topProduits = $stmtProd->fetchAll();

$activePage = 'accueil';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Boulangerie Ouahabi - Artisan Boulanger - Tanger</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require 'includes/site-header.php'; ?>

<section class="hero">
  <img src="image/Hero.png" alt="Pains et viennoiseries artisanaux" class="hero-bg">
  <div class="hero-overlay" aria-hidden="true"></div>
  <div class="hero-content">
    <p class="hero-eyebrow">Depuis 2001 · Tanger</p>
    <h1>L'art du pain<br><em>artisanal</em><br>au quotidien</h1>
    <p class="hero-desc">
      Pains, viennoiseries et pâtisseries façonnés chaque matin
      avec passion, tradition et les meilleurs ingrédients du terroir marocain.
    </p>
    <div class="hero-ctas">
      <a href="produits.php" class="btn btn-primary">Voir nos produits</a>
      <a href="#about" class="btn btn-secondary">Notre histoire</a>
    </div>
    <a href="#about" class="hero-scroll">Défiler</a>
  </div>
</section>

<div class="section-separator" aria-hidden="true"><span></span></div>

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
          <li>Recettes marocaines au savoir-faire artisanal</li>
          <li>Sans conservateurs ni additifs</li>
          <li>Ouvert 7 jours sur 7 dès 6h00</li>
        </ul>
        <a href="produits.php" class="btn btn-primary">Découvrir nos produits</a>
      </div>
      <div class="about-images">
        <div class="about-img tall"><img src="image/img1.jpg" alt="Boulangerie artisanale"></div>
        <div class="about-img"><img src="image/img2.jpg" alt="Pains frais"></div>
        <div class="about-img"><img src="image/img3.jpg" alt="Viennoiseries"></div>
      </div>
    </div>
  </div>
</section>

<div class="section-separator" aria-hidden="true"><span></span></div>

<section class="home-showcase">
  <div class="container">
    <!-- Barre de statistiques -->
    <div class="stats-bar">
      <div class="stat-item">
        <div class="stat-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        </div>
        <div class="stat-num">25+</div>
        <div class="stat-label">Années d'expérience</div>
      </div>
      <div class="stat-item">
        <div class="stat-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        </div>
        <div class="stat-num"><?= count($categories) ?></div>
        <div class="stat-label">Catégories</div>
      </div>
      <div class="stat-item">
        <div class="stat-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 11V6a2 2 0 0 0-2-2 2 2 0 0 0-2 2"/><path d="M14 10V4a2 2 0 0 0-2-2 2 2 0 0 0-2 2v2"/><path d="M10 10.5V6a2 2 0 0 0-2-2 2 2 0 0 0-2 2v8"/><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-1.98-1.98A2 2 0 0 1 2 14.5V11a2 2 0 1 1 4 0"/></svg>
        </div>
        <div class="stat-num">100%</div>
        <div class="stat-label">Fait main chaque jour</div>
      </div>
      <div class="stat-item">
        <div class="stat-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-num">6h</div>
        <div class="stat-label">Ouverture quotidienne</div>
      </div>
    </div>

    <?php if (!empty($topProduits)): ?>
    <!-- Carrousel des meilleurs produits -->
    <div class="carousel-block">
      <div class="carousel-header">
        <h2 class="carousel-title">Nos meilleurs produits</h2>
        <div class="carousel-nav-mini">
          <button type="button" class="carousel-btn-mini" id="btnPrev" aria-label="Produit précédent">←</button>
          <button type="button" class="carousel-btn-mini" id="btnNext" aria-label="Produit suivant">→</button>
        </div>
      </div>

      <div class="carousel-zone">
        <button type="button" class="carousel-btn-large carousel-btn-left" id="btnPrevLarge" aria-label="Précédent">‹</button>

        <div class="carousel-viewport">
          <div class="carousel-track" id="carouselTrack">
            <?php foreach ($topProduits as $prod): ?>
              <a href="produits.php" class="carousel-card">
                <div class="carousel-card-img">
                  <?php $pimg = produitImage($prod['image']); ?>
                  <?php if ($pimg != ''): ?>
                    <img src="<?= htmlspecialchars($pimg) ?>" alt="<?= htmlspecialchars($prod['nom_produit']) ?>">
                  <?php else: ?>
                    <div class="carousel-card-placeholder">🥖</div>
                  <?php endif; ?>
                </div>
                <div class="carousel-card-footer">
                  <div class="carousel-card-info">
                    <h3><?= htmlspecialchars($prod['nom_produit']) ?></h3>
                    <p class="carousel-card-price"><?= number_format($prod['prix'], 2) ?> MAD</p>
                  </div>
                  <span class="carousel-card-stock">En stock</span>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>

        <button type="button" class="carousel-btn-large carousel-btn-right" id="btnNextLarge" aria-label="Suivant">›</button>
      </div>

      <div class="carousel-dots" id="carouselDots"></div>
      <div class="carousel-cta">
        <a href="produits.php" class="btn btn-secondary">Voir tous les produits →</a>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<div class="section-separator" aria-hidden="true"><span></span></div>

<section class="categories-section" id="categories">
  <div class="container">
    <p class="categories-eyebrow">Ce que nous faisons</p>
    <h2 class="categories-title">Nos catégories</h2>

    <?php if (empty($categories)): ?>
      <p class="cat-empty">Aucune catégorie trouvée.</p>
    <?php else: ?>
      <div class="categories-grid">
        <?php foreach ($categories as $cat): ?>
          <a href="produits.php?categorie=<?= $cat['id'] ?>" class="category-card">
            <div class="category-card-img">
              <img src="<?= categorieImage($cat) ?>" alt="<?= htmlspecialchars($cat['nom_categorie']) ?>">
            </div>
            <div class="category-card-footer">
              <h3><?= htmlspecialchars($cat['nom_categorie']) ?></h3>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<div class="section-separator" aria-hidden="true"><span></span></div>

<section class="testimonial-band">
  <div class="testimonial-inner">
    <div class="testimonial-img">
      <img src="image/img2.jpg" alt="Mohamed Ouahabi, fondateur">
    </div>
    <div class="testimonial-content">
      <div class="quote-mark">"</div>
      <p class="quote-text">
        Chaque matin, nous sélectionnons les meilleures farines et pétrissons à la main pour vous offrir un pain qui sent bon, qui croustille, et qui vous rappelle les vraies saveurs d'autrefois.
      </p>
      <p class="quote-author">— Mohamed Ouahabi, Fondateur</p>
    </div>
  </div>
</section>

<div class="section-separator section-separator-dark" aria-hidden="true"><span></span></div>

<section class="cta-section">
  <div class="cta-inner">
    <p class="section-eyebrow">Visitez-nous</p>
    <h2 class="section-title">Le meilleur du pain frais,<br><em>chaque matin à Tanger</em></h2>
    <p>Ouvert dès 6h — Pains, viennoiseries et pâtisseries préparés sur place.</p>
    <a href="produits.php" class="btn btn-primary btn-lg">Explorer le catalogue</a>
  </div>
</section>

<?php require 'includes/site-footer.php'; ?>

<?php if (!empty($topProduits)): ?>
<script>
/*
 * Carrousel produits - JavaScript : variables, fonctions, événements (click)
 * Fait défiler les produits avec les boutons et les points
 */
var slideActuel = 0;
var piste = document.getElementById('carouselTrack');
var cartes = piste.querySelectorAll('.carousel-card');
var zonePoints = document.getElementById('carouselDots');

function nbCartesVisibles() {
  if (window.innerWidth <= 600) return 1;
  if (window.innerWidth <= 900) return 2;
  return 4;
}

function nbPages() {
  return Math.ceil(cartes.length / nbCartesVisibles());
}

function afficherSlide(numero) {
  var visible = nbCartesVisibles();
  var maxPage = nbPages() - 1;
  if (numero < 0) numero = maxPage;
  if (numero > maxPage) numero = 0;
  slideActuel = numero;

  var largeurCarte = cartes[0].offsetWidth;
  var decalage = slideActuel * visible * largeurCarte;
  piste.style.transform = 'translateX(-' + decalage + 'px)';

  var points = zonePoints.querySelectorAll('.carousel-dot');
  for (var i = 0; i < points.length; i++) {
    points[i].className = 'carousel-dot' + (i == slideActuel ? ' active' : '');
  }
}

function creerPoints() {
  zonePoints.innerHTML = '';
  for (var i = 0; i < nbPages(); i++) {
    var point = document.createElement('button');
    point.type = 'button';
    point.className = 'carousel-dot' + (i == 0 ? ' active' : '');
    point.setAttribute('aria-label', 'Page ' + (i + 1));
    point.onclick = function() {
      afficherSlide(parseInt(this.getAttribute('data-index')));
    };
    point.setAttribute('data-index', i);
    zonePoints.appendChild(point);
  }
}

document.getElementById('btnPrev').onclick = function() { afficherSlide(slideActuel - 1); };
document.getElementById('btnNext').onclick = function() { afficherSlide(slideActuel + 1); };
document.getElementById('btnPrevLarge').onclick = function() { afficherSlide(slideActuel - 1); };
document.getElementById('btnNextLarge').onclick = function() { afficherSlide(slideActuel + 1); };

creerPoints();
window.onresize = function() { creerPoints(); afficherSlide(0); };
</script>
<?php endif; ?>

</body>
</html>
