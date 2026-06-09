<?php
// supprimer.php - Confirmation et suppression d'un produit
require_once 'db.php';
require_once 'auth.php';

// Page reservee a l'admin connecte
exiger_connexion();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: produits.php'); exit; }

// Récupérer le produit
$stmt = $pdo->prepare("
    SELECT p.*, c.nom_categorie
    FROM produit p
    LEFT JOIN categorie c ON p.categorie_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$produit = $stmt->fetch();
if (!$produit) { header('Location: produits.php'); exit; }

// Confirmation POST → supprimer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmer'])) {
    // Supprimer l'image si elle existe
    if (!empty($produit['image']) && file_exists(__DIR__ . '/images/' . $produit['image'])) {
        unlink(__DIR__ . '/images/' . $produit['image']);
    }
    $del = $pdo->prepare("DELETE FROM produit WHERE id = ?");
    $del->execute([$id]);
    header('Location: produits.php?deleted=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Supprimer - <?= htmlspecialchars($produit['nom_produit']) ?></title>
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
      <a href="produits.php" class="btn btn-secondary">← Retour</a>
    </div>
  </div>
</nav>

<div class="page-header" data-title="SUPPRIMER" style="background:var(--rust);">
  <div class="page-header-inner">
    <p class="page-header-eyebrow">Action irréversible</p>
    <h1>Supprimer <em>un produit</em></h1>
    <p class="breadcrumb">
      <a href="index.php">Accueil</a><span>/</span>
      <a href="produits.php">Produits</a><span>/</span>Supprimer
    </p>
  </div>
</div>

<div class="confirm-wrap">
  <div class="confirm-card">

    <!-- Aperçu produit -->
    <div style="width:90px;height:90px;border-radius:var(--radius);overflow:hidden;background:var(--beige);display:flex;align-items:center;justify-content:center;font-size:3rem;margin:0 auto 1.25rem;">
      <?php if (!empty($produit['image']) && file_exists('images/'.$produit['image'])): ?>
        <img src="images/<?= htmlspecialchars($produit['image']) ?>" style="width:100%;height:100%;object-fit:cover;">
      <?php else: ?>
        🥖
      <?php endif; ?>
    </div>

    <div style="font-size:0.68rem;letter-spacing:0.18em;text-transform:uppercase;color:var(--tan);margin-bottom:0.4rem;">
      <?= htmlspecialchars($produit['nom_categorie'] ?? '-') ?>
    </div>

    <h2><?= htmlspecialchars($produit['nom_produit']) ?></h2>

    <div style="display:flex;gap:1rem;justify-content:center;margin:0.75rem 0 1.5rem;">
      <div style="font-family:var(--font-display);font-size:1.1rem;font-weight:700;color:var(--brown);">
        <?= number_format($produit['prix'], 2) ?> MAD
      </div>
      <div style="color:var(--text-muted);font-size:0.85rem;align-self:center;">
        Stock: <?= (int)$produit['quantite_stock'] ?>
      </div>
    </div>

    <div style="background:#FFF5F5;border:1px solid #FCA5A5;border-radius:var(--radius);padding:1rem 1.25rem;margin-bottom:1.75rem;text-align:left;">
      <div style="font-size:0.75rem;font-weight:600;color:var(--rust);letter-spacing:0.1em;text-transform:uppercase;margin-bottom:0.3rem;">⚠ Attention</div>
      <p style="font-size:0.85rem;color:#7F1D1D;line-height:1.5;margin:0;">
        Cette action est <strong>définitive et irréversible</strong>. Le produit et son image seront supprimés définitivement du catalogue.
      </p>
    </div>

    <form method="POST">
      <div class="confirm-actions">
        <a href="produits.php" class="btn btn-secondary">✕ Annuler</a>
        <a href="modifier.php?id=<?= $id ?>" class="btn btn-secondary" style="border-color:var(--tan);color:var(--brown);">✏ Modifier</a>
        <button type="submit" name="confirmer" class="btn btn-danger">🗑 Oui, supprimer</button>
      </div>
    </form>

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
