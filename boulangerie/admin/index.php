<?php
/*
 * admin/index.php - Tableau de bord (back office)
 * PHP : requêtes SQL (SELECT, COUNT, JOIN, WHERE)
 */
require_once __DIR__ . '/../db.php';

$adminPage = 'dashboard';
$adminTitle = 'Tableau de bord';
$adminSubtitle = 'Vue d\'ensemble de votre catalogue';

$stats = [
    'produits'   => (int)$pdo->query("SELECT COUNT(*) FROM produit")->fetchColumn(),
    'categories' => (int)$pdo->query("SELECT COUNT(*) FROM categorie")->fetchColumn(),
    'commandes'  => (int)$pdo->query("SELECT COUNT(*) FROM commande WHERE statut = 'en_attente'")->fetchColumn(),
];

$commandesEnAttente = $pdo->query("
    SELECT c.*, p.nom_produit
    FROM commande c
    JOIN produit p ON p.id = c.produit_id
    WHERE c.statut = 'en_attente'
    ORDER BY c.date_commande ASC
    LIMIT 5
")->fetchAll();

$recentProduits = $pdo->query("
    SELECT p.*, c.nom_categorie
    FROM produit p
    LEFT JOIN categorie c ON p.categorie_id = c.id
    ORDER BY p.id DESC
    LIMIT 5
")->fetchAll();

$adminTopAction = '<a href="../ajouter.php" class="btn btn-primary btn-sm">+ Nouveau produit</a>';

require __DIR__ . '/layout-start.php';
?>

<div class="admin-stats">
  <div class="admin-stat-card">
    <span class="admin-stat-label">Produits</span>
    <span class="admin-stat-value"><?= $stats['produits'] ?></span>
  </div>
  <div class="admin-stat-card">
    <span class="admin-stat-label">Catégories</span>
    <span class="admin-stat-value"><?= $stats['categories'] ?></span>
  </div>
  <div class="admin-stat-card admin-stat-warn">
    <span class="admin-stat-label">Commandes à traiter</span>
    <span class="admin-stat-value"><?= $stats['commandes'] ?></span>
  </div>
</div>

<section class="admin-panel">
    <div class="admin-panel-head">
      <h2>Commandes en attente</h2>
      <a href="commandes.php" class="admin-panel-link">Voir tout →</a>
    </div>
    <?php if (empty($commandesEnAttente)): ?>
      <p class="admin-empty admin-empty-ok">✓ Aucune commande en attente.</p>
    <?php else: ?>
      <div class="admin-mini-list">
        <?php foreach ($commandesEnAttente as $cmd): ?>
          <div class="admin-mini-item">
            <div class="admin-mini-info">
              <strong><?= htmlspecialchars($cmd['nom_client']) ?></strong>
              <span><?= htmlspecialchars($cmd['nom_produit']) ?> × <?= (int)$cmd['quantite'] ?></span>
            </div>
            <div class="admin-mini-meta">
              <span><?= htmlspecialchars($cmd['telephone']) ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
</section>

<section class="admin-panel">
    <div class="admin-panel-head">
      <h2>Derniers produits</h2>
      <a href="produits.php" class="admin-panel-link">Voir tout →</a>
    </div>
    <?php if (empty($recentProduits)): ?>
      <p class="admin-empty">Aucun produit pour le moment.</p>
    <?php else: ?>
      <div class="admin-mini-list">
        <?php foreach ($recentProduits as $p): ?>
          <a href="../modifier.php?id=<?= $p['id'] ?>" class="admin-mini-item">
            <div class="admin-mini-thumb">
              <?php $img = produitImage($p['image']); ?>
              <?php if ($img): ?>
                <img src="../<?= htmlspecialchars($img) ?>" alt="">
              <?php else: ?>
                <span>🥖</span>
              <?php endif; ?>
            </div>
            <div class="admin-mini-info">
              <strong><?= htmlspecialchars($p['nom_produit']) ?></strong>
              <span><?= htmlspecialchars($p['nom_categorie'] ?? '-') ?></span>
            </div>
            <div class="admin-mini-meta">
              <span><?= number_format($p['prix'], 2) ?> MAD</span>
              <span class="<?= $p['quantite_stock'] < 5 ? 'text-warn' : '' ?>">Qté <?= (int)$p['quantite_stock'] ?></span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
</section>

<div class="admin-quick-actions">
  <a href="../ajouter.php" class="admin-quick-card">
    <span class="admin-quick-icon">＋</span>
    <strong>Ajouter un produit</strong>
    <span>Enrichir le catalogue</span>
  </a>
  <a href="categories.php" class="admin-quick-card">
    <span class="admin-quick-icon">◧</span>
    <strong>Gérer les catégories</strong>
    <span>Organiser le catalogue</span>
  </a>
  <a href="commandes.php" class="admin-quick-card">
    <span class="admin-quick-icon">◎</span>
    <strong>Voir les commandes</strong>
    <span>Gérer le stock</span>
  </a>
  <a href="../produits.php" class="admin-quick-card">
    <span class="admin-quick-icon">↗</span>
    <strong>Voir le site public</strong>
    <span>Page produits</span>
  </a>
</div>

<?php require __DIR__ . '/layout-end.php'; ?>
