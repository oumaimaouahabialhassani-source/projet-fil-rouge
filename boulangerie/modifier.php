<?php
/**
 * modifier.php — Formulaire de modification d'un produit existant
 */
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: produits.php'); exit; }

// Récupérer le produit
$stmtGet = $pdo->prepare("SELECT * FROM produit WHERE id = ?");
$stmtGet->execute([$id]);
$produit = $stmtGet->fetch();
if (!$produit) { header('Location: produits.php'); exit; }

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom      = trim($_POST['nom_produit'] ?? '');
    $prix     = $_POST['prix'] ?? '';
    $quantite = $_POST['quantite_stock'] ?? '';
    $catId    = (int)($_POST['categorie_id'] ?? 0);

    if ($nom === '')                               $errors[] = "Le nom du produit est obligatoire.";
    if (!is_numeric($prix) || (float)$prix < 0)   $errors[] = "Le prix doit être un nombre positif.";
    if (!is_numeric($quantite) || (int)$quantite < 0) $errors[] = "La quantité doit être un entier positif.";
    if ($catId <= 0)                               $errors[] = "Veuillez sélectionner une catégorie.";

    $imageName = $produit['image']; // garder l'ancienne par défaut

    if (!empty($_FILES['image']['name'])) {
        $allowed  = ['image/jpeg','image/png','image/webp','image/gif'];
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mimeType, $allowed))         $errors[] = "Format image non supporté.";
        elseif ($_FILES['image']['size'] > 2097152) $errors[] = "Image trop grande (max 2 Mo).";
        else {
            // Supprimer ancienne image
            if (!empty($produit['image']) && file_exists(__DIR__.'/images/'.$produit['image'])) {
                unlink(__DIR__.'/images/'.$produit['image']);
            }
            $ext       = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = uniqid('prod_', true) . '.' . strtolower($ext);
            $destDir   = __DIR__ . '/images/';
            if (!is_dir($destDir)) mkdir($destDir, 0755, true);
            move_uploaded_file($_FILES['image']['tmp_name'], $destDir . $imageName);
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE produit
            SET nom_produit = :nom,
                prix        = :prix,
                quantite_stock = :quantite,
                image       = :image,
                categorie_id = :cat
            WHERE id = :id
        ");
        $stmt->execute([
            ':nom'     => $nom,
            ':prix'    => (float)$prix,
            ':quantite'=> (int)$quantite,
            ':image'   => $imageName,
            ':cat'     => $catId,
            ':id'      => $id,
        ]);
        $success = true;
        // Rafraîchir les données affichées
        $stmtGet->execute([$id]);
        $produit = $stmtGet->fetch();
    }
}

$stmtCat    = $pdo->query("SELECT * FROM categorie ORDER BY nom_categorie");
$categories = $stmtCat->fetchAll();

// Valeurs affichées (POST si erreur, sinon DB)
$val = $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($errors) ? $_POST : $produit;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier — <?= htmlspecialchars($produit['nom_produit']) ?> — Boulangerie Ouahabi</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
  <div class="navbar-inner">
    <a href="index.php" class="navbar-brand">
      <span class="brand-main">Boulangerie Ouahabi</span>
      <span class="brand-sub">Artisan Boulanger · Casablanca</span>
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

<div class="page-header" data-title="MODIFIER">
  <div class="page-header-inner">
    <p class="page-header-eyebrow">Gestion du catalogue</p>
    <h1>Modifier <em><?= htmlspecialchars($produit['nom_produit']) ?></em></h1>
    <p class="breadcrumb">
      <a href="index.php">Accueil</a><span>/</span>
      <a href="produits.php">Produits</a><span>/</span>Modifier
    </p>
  </div>
</div>

<div class="form-page">
  <div class="form-card">

    <!-- Aperçu actuel du produit -->
    <div style="display:flex;align-items:center;gap:1.25rem;padding:1rem 1.25rem;background:var(--cream);border-radius:var(--radius);border:1px solid var(--border);margin-bottom:2rem;">
      <div style="width:64px;height:64px;border-radius:var(--radius);overflow:hidden;background:var(--beige);display:flex;align-items:center;justify-content:center;font-size:2rem;flex-shrink:0;">
        <?php if (!empty($produit['image']) && file_exists('images/'.$produit['image'])): ?>
          <img src="images/<?= htmlspecialchars($produit['image']) ?>" style="width:100%;height:100%;object-fit:cover;">
        <?php else: ?>
          🥖
        <?php endif; ?>
      </div>
      <div>
        <div style="font-size:0.68rem;letter-spacing:0.14em;text-transform:uppercase;color:var(--tan);margin-bottom:2px;">Produit en cours de modification</div>
        <div style="font-family:var(--font-display);font-size:1.15rem;font-weight:700;color:var(--brown-dark);"><?= htmlspecialchars($produit['nom_produit']) ?></div>
        <div style="font-size:0.82rem;color:var(--text-muted);">Prix actuel: <?= number_format($produit['prix'],2) ?> MAD · Stock: <?= (int)$produit['quantite_stock'] ?></div>
      </div>
    </div>

    <h2 class="form-card-title">Modifier le produit</h2>
    <p class="form-card-subtitle">Modifiez les champs souhaités puis enregistrez.</p>

    <?php if ($success): ?>
      <div class="alert alert-success">
        ✓ Produit mis à jour avec succès !
        <a href="produits.php" style="color:inherit;font-weight:600;margin-left:8px;">Voir le catalogue →</a>
      </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $e): ?><div>⚠ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

      <div class="form-group">
        <label class="form-label" for="nom_produit">Nom du produit *</label>
        <input type="text" id="nom_produit" name="nom_produit" class="form-control"
               value="<?= htmlspecialchars($val['nom_produit'] ?? '') ?>" required>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="prix">Prix (MAD) *</label>
          <input type="number" id="prix" name="prix" class="form-control"
                 step="0.01" min="0"
                 value="<?= htmlspecialchars($val['prix'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="quantite_stock">Quantité en stock *</label>
          <input type="number" id="quantite_stock" name="quantite_stock" class="form-control"
                 min="0"
                 value="<?= htmlspecialchars($val['quantite_stock'] ?? '') ?>" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="categorie_id">Catégorie *</label>
        <select id="categorie_id" name="categorie_id" class="form-control" required>
          <option value="">— Sélectionner —</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"
              <?= ((int)($val['categorie_id'] ?? 0) === (int)$cat['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['nom_categorie']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Changer l'image <span style="font-weight:300;text-transform:none;letter-spacing:0;">(optionnel)</span></label>

        <?php if (!empty($produit['image']) && file_exists('images/'.$produit['image'])): ?>
          <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
            <img src="images/<?= htmlspecialchars($produit['image']) ?>" style="height:70px;border-radius:var(--radius);border:1px solid var(--border);">
            <span style="font-size:0.8rem;color:var(--text-muted);">Image actuelle — téléversez-en une nouvelle pour la remplacer.</span>
          </div>
        <?php endif; ?>

        <div class="file-upload-wrap" id="uploadWrap">
          <label class="file-upload-label" for="image">
            <span class="file-upload-icon">📸</span>
            <span class="file-upload-text"><strong>Cliquez</strong> ou glissez une nouvelle image</span>
            <span id="fileName" style="font-size:0.8rem;color:var(--brown);font-weight:500;"></span>
          </label>
          <input type="file" id="image" name="image" accept="image/*" onchange="previewImg(this)">
        </div>
        <div id="previewWrap" class="img-preview-wrap" style="display:none;">
          <img id="imgPreview" src="" alt="Aperçu">
        </div>
      </div>

      <div class="form-actions">
        <a href="produits.php" class="btn btn-secondary">Annuler</a>
        <a href="supprimer.php?id=<?= $id ?>" class="btn btn-danger" onclick="return confirm('Supprimer ce produit ?')">✕ Supprimer</a>
        <button type="submit" class="btn btn-primary">💾 Enregistrer les modifications</button>
      </div>

    </form>
  </div>
</div>

<footer class="footer">
  <div class="footer-inner">
    <div>
      <div class="footer-brand">Boulangerie Ouahabi</div>
      <div class="footer-tagline">Artisan Boulanger · Casablanca · Depuis 1985</div>
    </div>
    <div class="footer-copy">© <?= date('Y') ?> Boulangerie Ouahabi</div>
  </div>
</footer>

<script>
function previewImg(input) {
  const wrap = document.getElementById('previewWrap');
  const img  = document.getElementById('imgPreview');
  const name = document.getElementById('fileName');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => { img.src = e.target.result; wrap.style.display = 'block'; };
    reader.readAsDataURL(input.files[0]);
    name.textContent = input.files[0].name;
  }
}
const uw = document.getElementById('uploadWrap');
uw.addEventListener('dragover', e => { e.preventDefault(); uw.style.borderColor='var(--brown)'; });
uw.addEventListener('dragleave', () => { uw.style.borderColor=''; });
uw.addEventListener('drop', e => {
  e.preventDefault(); uw.style.borderColor='';
  if (e.dataTransfer.files.length) {
    document.getElementById('image').files = e.dataTransfer.files;
    previewImg(document.getElementById('image'));
  }
});
</script>
</body>
</html>
