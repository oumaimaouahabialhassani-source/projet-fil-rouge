<?php
// ajouter.php - Formulaire d'ajout d'un nouveau produit
require_once 'db.php';
require_once 'auth.php';

// Page reservee a l'admin connecte
exiger_connexion();

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom      = trim($_POST['nom_produit'] ?? '');
    $prix     = $_POST['prix'] ?? '';
    $quantite = $_POST['quantite_stock'] ?? '';
    $catId    = (int)($_POST['categorie_id'] ?? 0);

    if ($nom == "") {
        $errors[] = "Le nom du produit est obligatoire.";
    }
    if (!is_numeric($prix) || $prix < 0) {
        $errors[] = "Le prix doit etre un nombre positif.";
    }
    if (!is_numeric($quantite) || $quantite < 0) {
        $errors[] = "La quantite doit etre un nombre positif.";
    }
    if ($catId <= 0) {
        $errors[] = "Veuillez choisir une categorie.";
    }

    // Gestion de l'image (optionnelle)
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $extensions_ok = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($ext, $extensions_ok)) {
            $errors[] = "Format d'image non supporte (jpg, png, webp, gif).";
        } elseif ($_FILES['image']['size'] > 2097152) {
            $errors[] = "Image trop grande (max 2 Mo).";
        } else {
            // Nom unique pour eviter d'ecraser une autre image
            $imageName = uniqid('prod_') . '.' . $ext;
        }
    }

    if (empty($errors)) {
        if ($imageName) {
            $destDir = __DIR__ . '/images/';
            if (!is_dir($destDir)) mkdir($destDir, 0755, true);
            move_uploaded_file($_FILES['image']['tmp_name'], $destDir . $imageName);
        }
        $stmt = $pdo->prepare("
            INSERT INTO produit (nom_produit, prix, quantite_stock, image, categorie_id)
            VALUES (:nom, :prix, :quantite, :image, :cat)
        ");
        $stmt->execute([
            ':nom'     => $nom,
            ':prix'    => (float)$prix,
            ':quantite'=> (int)$quantite,
            ':image'   => $imageName,
            ':cat'     => $catId,
        ]);
        $success = true;
    }
}

$stmtCat    = $pdo->query("SELECT * FROM categorie ORDER BY nom_categorie");
$categories = $stmtCat->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ajouter un produit - Boulangerie Ouahabi</title>
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
      <li><a href="produits.php">Produits</a></li>
      <li><a href="index.php#categories">Catégories</a></li>
    </ul>
    <div class="navbar-actions">
      <a href="produits.php" class="btn btn-secondary">← Retour</a>
    </div>
  </div>
</nav>

<div class="page-header" data-title="AJOUTER">
  <div class="page-header-inner">
    <p class="page-header-eyebrow">Gestion du catalogue</p>
    <h1>Ajouter un <em>produit</em></h1>
    <p class="breadcrumb">
      <a href="index.php">Accueil</a><span>/</span>
      <a href="produits.php">Produits</a><span>/</span>Ajouter
    </p>
  </div>
</div>

<div class="form-page">
  <div class="form-card">
    <h2 class="form-card-title">Nouveau produit</h2>
    <p class="form-card-subtitle">Remplissez les informations ci-dessous pour ajouter un produit au catalogue.</p>

    <?php if ($success): ?>
      <div class="alert alert-success">
        ✓ Produit ajouté avec succès !
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
               placeholder="Ex: Baguette tradition, Croissant au beurre..."
               value="<?= htmlspecialchars($_POST['nom_produit'] ?? '') ?>" required>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="prix">Prix (MAD) *</label>
          <input type="number" id="prix" name="prix" class="form-control"
                 placeholder="0.00" step="0.01" min="0"
                 value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="quantite_stock">Quantité en stock *</label>
          <input type="number" id="quantite_stock" name="quantite_stock" class="form-control"
                 placeholder="0" min="0"
                 value="<?= htmlspecialchars($_POST['quantite_stock'] ?? '') ?>" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="categorie_id">Catégorie *</label>
        <select id="categorie_id" name="categorie_id" class="form-control" required>
          <option value="">- Sélectionner une catégorie -</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"
              <?= (isset($_POST['categorie_id']) && $_POST['categorie_id'] == $cat['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['nom_categorie']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Image du produit</label>
        <div class="file-upload-wrap" id="uploadWrap">
          <label class="file-upload-label" for="image">
            <span class="file-upload-icon">📸</span>
            <span class="file-upload-text">
              <strong>Cliquez pour choisir</strong> ou glissez une image ici
            </span>
            <span id="fileName" style="font-size:0.8rem;color:var(--brown);font-weight:500;margin-top:4px;"></span>
          </label>
          <input type="file" id="image" name="image" accept="image/*" onchange="previewImg(this)">
        </div>
        <div id="previewWrap" class="img-preview-wrap" style="display:none;">
          <img id="imgPreview" src="" alt="Aperçu de l'image">
        </div>
      </div>

      <div class="form-actions">
        <a href="produits.php" class="btn btn-secondary">Annuler</a>
        <button type="submit" class="btn btn-primary">💾 Enregistrer le produit</button>
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
uw.addEventListener('dragover', e => { e.preventDefault(); uw.style.borderColor='var(--brown)'; uw.style.background='var(--beige)'; });
uw.addEventListener('dragleave', () => { uw.style.borderColor=''; uw.style.background=''; });
uw.addEventListener('drop', e => {
  e.preventDefault(); uw.style.borderColor=''; uw.style.background='';
  if (e.dataTransfer.files.length) {
    document.getElementById('image').files = e.dataTransfer.files;
    previewImg(document.getElementById('image'));
  }
});
</script>
</body>
</html>
