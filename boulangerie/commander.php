<?php
/*
 * commander.php - Passer une commande (site public)
 * PHP : $_GET, $_POST, INSERT, validation simple
 */
require_once 'db.php';
require_once 'auth.php';
require_once 'includes/helpers.php';

$produitId = (int)($_POST['produit_id'] ?? $_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT p.*, c.nom_categorie
    FROM produit p
    LEFT JOIN categorie c ON p.categorie_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$produitId]);
$produit = $stmt->fetch();

if (!$produit) {
    header('Location: produits.php');
    exit;
}

// Enregistrer la commande (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom      = trim($_POST['nom_client'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $quantite = (int)($_POST['quantite'] ?? 0);

    if ($nom === '' || $telephone === '' || $quantite < 1) {
        $erreur = 'Veuillez remplir tous les champs.';
    } elseif ($quantite > $produit['quantite_stock']) {
        $erreur = 'Stock insuffisant (disponible : ' . (int)$produit['quantite_stock'] . ').';
    } else {
        $pdo->prepare("
            INSERT INTO commande (produit_id, nom_client, telephone, quantite, statut)
            VALUES (?, ?, ?, ?, 'en_attente')
        ")->execute([$produitId, $nom, $telephone, $quantite]);

        enregistrerMessage('success', 'Commande enregistrée ! Nous vous contacterons bientôt.');
        header('Location: produits.php');
        exit;
    }
}

$activePage = 'produits';
$pimg = produitImage($produit['image']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Commander - <?= htmlspecialchars($produit['nom_produit']) ?></title>
  <link rel="stylesheet" href="<?= baseUrl() ?>css/style.css">
</head>
<body>

<?php require 'includes/site-header.php'; ?>

<section class="catalog">
  <div class="container-wide">
    <header class="catalog-top">
      <h1>Commander</h1>
      <p><a href="produits.php">← Retour au catalogue</a></p>
    </header>

    <div class="order-box">
      <div class="order-product">
        <?php if ($pimg): ?>
          <img src="<?= htmlspecialchars($pimg) ?>" alt="">
        <?php else: ?>
          <div class="product-img-empty">🥖</div>
        <?php endif; ?>
        <div>
          <h2><?= htmlspecialchars($produit['nom_produit']) ?></h2>
          <p class="product-price"><?= number_format($produit['prix'], 2) ?> MAD</p>
          <p class="order-stock">Stock disponible : <?= (int)$produit['quantite_stock'] ?></p>
        </div>
      </div>

      <?php if (!empty($erreur)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
      <?php endif; ?>

      <?php if ($produit['quantite_stock'] < 1): ?>
        <p class="order-unavailable">Ce produit n'est plus disponible.</p>
      <?php else: ?>
        <form method="POST" class="order-form">
          <input type="hidden" name="produit_id" value="<?= $produitId ?>">

          <div class="form-group">
            <label class="form-label" for="nom_client">Votre nom</label>
            <input type="text" id="nom_client" name="nom_client" class="form-control" required
                   value="<?= htmlspecialchars($_POST['nom_client'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label class="form-label" for="telephone">Téléphone</label>
            <input type="text" id="telephone" name="telephone" class="form-control" required
                   value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label class="form-label" for="quantite">Quantité</label>
            <input type="number" id="quantite" name="quantite" class="form-control" min="1"
                   max="<?= (int)$produit['quantite_stock'] ?>" value="<?= (int)($_POST['quantite'] ?? 1) ?>" required>
          </div>

          <button type="submit" class="btn btn-primary">Envoyer la commande</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require 'includes/site-footer.php'; ?>
</body>
</html>
