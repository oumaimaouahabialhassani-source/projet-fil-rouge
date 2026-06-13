<?php
/*
 * ajouter.php - Ajouter un nouveau produit
 * PHP : formulaire POST, validation, upload d'image, sessions
 * SQL : INSERT INTO produit
 */
require_once 'db.php';
require_once 'auth.php';
require_once 'includes/helpers.php';

exiger_connexion();

$erreurs = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des données du formulaire ($_POST)
    $nom      = trim($_POST['nom_produit'] ?? '');
    $prix     = $_POST['prix'] ?? '';
    $quantite = $_POST['quantite_stock'] ?? '';
    $catId    = (int)($_POST['categorie_id'] ?? 0);

    // Validation des champs (PHP : conditions)
    if ($nom == '') $erreurs[] = "Le nom du produit est obligatoire.";
    if (!is_numeric($prix) || $prix < 0) $erreurs[] = "Le prix doit être un nombre positif.";
    if (!is_numeric($quantite) || $quantite < 0) $erreurs[] = "La quantité doit être un nombre positif.";
    if ($catId <= 0) $erreurs[] = "Veuillez choisir une catégorie.";

    // Upload de l'image (PHP : $_FILES)
    $nomImage = null;
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $formatsOk = array('jpg', 'jpeg', 'png', 'webp', 'gif');
        if (!in_array($ext, $formatsOk)) {
            $erreurs[] = "Format d'image non supporté (jpg, png, webp, gif).";
        } elseif ($_FILES['image']['size'] > 2097152) {
            $erreurs[] = "Image trop grande (max 2 Mo).";
        } else {
            $nomImage = uniqid('prod_') . '.' . $ext;
        }
    }

    if (empty($erreurs)) {
        if ($nomImage) {
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/images/' . $nomImage);
        }

        // Insertion en base de données (SQL : INSERT)
        $stmt = $pdo->prepare("
            INSERT INTO produit (nom_produit, prix, quantite_stock, image, categorie_id)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute(array($nom, (float)$prix, (int)$quantite, $nomImage, $catId));

        enregistrerMessage('success', 'Produit ajouté avec succès.');
        header('Location: admin/produits.php');
        exit;
    }
}

$categories = $pdo->query("SELECT * FROM categorie ORDER BY nom_categorie")->fetchAll();

$adminPage = 'ajouter';
$adminTitle = 'Nouveau produit';
$adminSubtitle = 'Ajouter un article au catalogue';
$adminTopAction = '<a href="admin/produits.php" class="btn btn-secondary btn-sm">← Retour à la liste</a>';

require 'includes/admin-layout-start.php';
?>

<div class="admin-form-panel">
  <?php if (!empty($erreurs)): ?>
    <div class="alert alert-danger">
      <?php foreach ($erreurs as $e): ?><div>⚠ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="admin-form">
    <div class="form-group">
      <label class="form-label" for="nom_produit">Nom du produit *</label>
      <input type="text" id="nom_produit" name="nom_produit" class="form-control"
             value="<?= htmlspecialchars($_POST['nom_produit'] ?? '') ?>" required>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="prix">Prix (MAD) *</label>
        <input type="number" id="prix" name="prix" class="form-control"
               step="0.01" min="0" value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label" for="quantite_stock">Quantité en stock *</label>
        <input type="number" id="quantite_stock" name="quantite_stock" class="form-control"
               min="0" value="<?= htmlspecialchars($_POST['quantite_stock'] ?? '') ?>" required>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="categorie_id">Catégorie *</label>
      <select id="categorie_id" name="categorie_id" class="form-control" required>
        <option value="">— Sélectionner une catégorie —</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>"
            <?= (isset($_POST['categorie_id']) && $_POST['categorie_id'] == $cat['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['nom_categorie']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label class="form-label" for="image">Image du produit</label>
      <input type="file" id="image" name="image" class="form-control" accept="image/*">
    </div>

    <div class="form-actions">
      <a href="admin/produits.php" class="btn btn-secondary">Annuler</a>
      <button type="submit" class="btn btn-primary">Enregistrer le produit</button>
    </div>
  </form>
</div>

<?php require 'includes/admin-layout-end.php'; ?>
