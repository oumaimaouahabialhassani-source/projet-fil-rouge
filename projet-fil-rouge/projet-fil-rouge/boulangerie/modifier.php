<?php
/*
 * modifier.php - Modifier un produit existant
 * PHP : $_GET (id), formulaire POST, validation, upload d'image
 * SQL : SELECT, UPDATE
 */
require_once 'db.php';
require_once 'auth.php';
require_once 'includes/helpers.php';

exiger_connexion();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: admin/produits.php'); exit; }

// Récupérer le produit à modifier (SQL : SELECT WHERE)
$stmt = $pdo->prepare("SELECT * FROM produit WHERE id = ?");
$stmt->execute(array($id));
$produit = $stmt->fetch();
if (!$produit) { header('Location: admin/produits.php'); exit; }

$erreurs = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom      = trim($_POST['nom_produit'] ?? '');
    $prix     = $_POST['prix'] ?? '';
    $quantite = $_POST['quantite_stock'] ?? '';
    $catId    = (int)($_POST['categorie_id'] ?? 0);

    if ($nom == '') $erreurs[] = "Le nom du produit est obligatoire.";
    if (!is_numeric($prix) || $prix < 0) $erreurs[] = "Le prix doit être un nombre positif.";
    if (!is_numeric($quantite) || $quantite < 0) $erreurs[] = "La quantité doit être un nombre positif.";
    if ($catId <= 0) $erreurs[] = "Veuillez choisir une catégorie.";

    $nomImage = $produit['image'];

    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $formatsOk = array('jpg', 'jpeg', 'png', 'webp', 'gif');
        if (!in_array($ext, $formatsOk)) {
            $erreurs[] = "Format d'image non supporté.";
        } elseif ($_FILES['image']['size'] > 2097152) {
            $erreurs[] = "Image trop grande (max 2 Mo).";
        } else {
            if (!empty($produit['image']) && file_exists(__DIR__ . '/images/' . $produit['image'])) {
                unlink(__DIR__ . '/images/' . $produit['image']);
            }
            $nomImage = uniqid('prod_') . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/images/' . $nomImage);
        }
    }

    if (empty($erreurs)) {
        // Mise à jour en base (SQL : UPDATE)
        $stmt = $pdo->prepare("
            UPDATE produit
            SET nom_produit = ?, prix = ?, quantite_stock = ?, image = ?, categorie_id = ?
            WHERE id = ?
        ");
        $stmt->execute(array($nom, (float)$prix, (int)$quantite, $nomImage, $catId, $id));

        enregistrerMessage('success', 'Produit mis à jour avec succès.');
        header('Location: admin/produits.php');
        exit;
    }
}

$categories = $pdo->query("SELECT * FROM categorie ORDER BY nom_categorie")->fetchAll();

// En cas d'erreur, on garde les valeurs saisies, sinon les valeurs de la BDD
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($erreurs)) {
    $valeurs = $_POST;
} else {
    $valeurs = $produit;
}

$adminPage = 'produits';
$adminTitle = 'Modifier le produit';
$adminSubtitle = $produit['nom_produit'];
$adminTopAction = '<a href="admin/produits.php" class="btn btn-secondary btn-sm">← Retour</a>';

require 'includes/admin-layout-start.php';
?>

<div class="admin-form-panel">
  <div class="admin-product-preview">
    <div class="admin-product-preview-img">
      <?php $img = produitImage($produit['image']); ?>
      <?php if ($img != ''): ?>
        <img src="<?= htmlspecialchars($img) ?>" alt="">
      <?php else: ?>
        <span>🥖</span>
      <?php endif; ?>
    </div>
    <div>
      <span class="admin-preview-label">Produit en cours de modification</span>
      <strong><?= htmlspecialchars($produit['nom_produit']) ?></strong>
      <span class="admin-preview-meta">
        <?= number_format($produit['prix'], 2) ?> MAD · Stock: <?= (int)$produit['quantite_stock'] ?>
      </span>
    </div>
  </div>

  <?php if (!empty($erreurs)): ?>
    <div class="alert alert-danger">
      <?php foreach ($erreurs as $e): ?><div>⚠ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="admin-form">
    <div class="form-group">
      <label class="form-label" for="nom_produit">Nom du produit *</label>
      <input type="text" id="nom_produit" name="nom_produit" class="form-control"
             value="<?= htmlspecialchars($valeurs['nom_produit'] ?? '') ?>" required>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="prix">Prix (MAD) *</label>
        <input type="number" id="prix" name="prix" class="form-control" step="0.01" min="0"
               value="<?= htmlspecialchars($valeurs['prix'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label" for="quantite_stock">Quantité en stock *</label>
        <input type="number" id="quantite_stock" name="quantite_stock" class="form-control" min="0"
               value="<?= htmlspecialchars($valeurs['quantite_stock'] ?? '') ?>" required>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="categorie_id">Catégorie *</label>
      <select id="categorie_id" name="categorie_id" class="form-control" required>
        <option value="">— Sélectionner —</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>"
            <?= ((int)($valeurs['categorie_id'] ?? 0) == (int)$cat['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['nom_categorie']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label class="form-label" for="image">Changer l'image (optionnel)</label>
      <?php if ($img != ''): ?>
        <div class="admin-current-image">
          <img src="<?= htmlspecialchars($img) ?>" alt="">
          <span>Image actuelle</span>
        </div>
      <?php endif; ?>
      <input type="file" id="image" name="image" class="form-control" accept="image/*">
    </div>

    <div class="form-actions">
      <a href="admin/produits.php" class="btn btn-secondary">Annuler</a>
      <a href="supprimer.php?id=<?= $id ?>" class="btn btn-danger">Supprimer</a>
      <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
  </form>
</div>

<?php require 'includes/admin-layout-end.php'; ?>
