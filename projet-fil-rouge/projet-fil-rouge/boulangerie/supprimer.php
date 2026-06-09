<?php
/*
 * supprimer.php - Supprimer un produit
 * PHP : $_GET, formulaire POST de confirmation
 * SQL : DELETE
 */
require_once 'db.php';
require_once 'auth.php';
require_once 'includes/helpers.php';

exiger_connexion();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: admin/produits.php'); exit; }

// Récupérer le produit (SQL : SELECT avec JOIN)
$stmt = $pdo->prepare("
    SELECT p.*, c.nom_categorie
    FROM produit p LEFT JOIN categorie c ON p.categorie_id = c.id
    WHERE p.id = ?
");
$stmt->execute(array($id));
$produit = $stmt->fetch();
if (!$produit) { header('Location: admin/produits.php'); exit; }

// Si l'admin confirme la suppression (PHP : $_POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirmer'])) {
    if (!empty($produit['image']) && file_exists(__DIR__ . '/images/' . $produit['image'])) {
        unlink(__DIR__ . '/images/' . $produit['image']);
    }
    $pdo->prepare("DELETE FROM produit WHERE id = ?")->execute(array($id));
    enregistrerMessage('success', 'Produit supprimé définitivement.');
    header('Location: admin/produits.php');
    exit;
}

$adminPage = 'produits';
$adminTitle = 'Supprimer le produit';
$adminSubtitle = 'Cette action est irréversible';
$adminTopAction = '<a href="admin/produits.php" class="btn btn-secondary btn-sm">← Annuler</a>';

require 'includes/admin-layout-start.php';
?>

<div class="admin-confirm-panel">
  <div class="admin-confirm-visual">
    <div class="admin-confirm-img">
      <?php $img = produitImage($produit['image']); ?>
      <?php if ($img != ''): ?>
        <img src="<?= htmlspecialchars($img) ?>" alt="">
      <?php else: ?>
        <span>🥖</span>
      <?php endif; ?>
    </div>
    <span class="admin-badge"><?= htmlspecialchars($produit['nom_categorie'] ?? '-') ?></span>
    <h2><?= htmlspecialchars($produit['nom_produit']) ?></h2>
    <p class="admin-confirm-meta">
      <?= number_format($produit['prix'], 2) ?> MAD · Stock: <?= (int)$produit['quantite_stock'] ?>
    </p>
  </div>

  <div class="alert alert-danger">
    <strong>Attention</strong> — Cette action est définitive.
  </div>

  <form method="POST" class="admin-confirm-actions">
    <a href="modifier.php?id=<?= $id ?>" class="btn btn-secondary">Modifier à la place</a>
    <a href="admin/produits.php" class="btn btn-secondary">Annuler</a>
    <button type="submit" name="confirmer" class="btn btn-danger">Oui, supprimer</button>
  </form>
</div>

<?php require 'includes/admin-layout-end.php'; ?>
