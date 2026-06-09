<?php
/*
 * admin/produits.php - Liste et recherche des produits (back office)
 * PHP : $_GET, formulaires, requêtes SQL (SELECT, JOIN, LIKE, WHERE)
 */
require_once __DIR__ . '/../db.php';

$adminPage = 'produits';
$adminTitle = 'Gestion des produits';
$adminSubtitle = 'Modifier, supprimer ou consulter votre catalogue';

// Récupération des filtres depuis l'URL (méthode GET)
$recherche = trim($_GET['q'] ?? '');
$idCategorie = (int)($_GET['categorie'] ?? 0);

// Construction de la requête SQL avec JOIN
$sql = "
    SELECT p.*, c.nom_categorie
    FROM produit p
    LEFT JOIN categorie c ON p.categorie_id = c.id
";
$params = array();
$conditions = array();

// Filtre par nom de produit ou nom de catégorie (SQL : LIKE)
if ($recherche != '') {
    $conditions[] = "(p.nom_produit LIKE ? OR c.nom_categorie LIKE ?)";
    $params[] = '%' . $recherche . '%';
    $params[] = '%' . $recherche . '%';
}

// Filtre par catégorie (SQL : WHERE)
if ($idCategorie > 0) {
    $conditions[] = "p.categorie_id = ?";
    $params[] = $idCategorie;
}

if (count($conditions) > 0) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}
$sql .= ' ORDER BY p.nom_produit';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produits = $stmt->fetchAll();

// Liste des catégories pour le menu déroulant
$categories = $pdo->query("SELECT * FROM categorie ORDER BY nom_categorie")->fetchAll();
$filtreActif = ($recherche != '' || $idCategorie > 0);

$adminTopAction = '<a href="../ajouter.php" class="btn btn-primary btn-sm">+ Nouveau produit</a>';

require __DIR__ . '/layout-start.php';
?>

<!-- Formulaire de recherche et filtre (HTML : form + GET) -->
<div class="admin-toolbar">
  <form method="GET" action="produits.php" class="admin-search-form">
    <input type="text" name="q" class="form-control"
           placeholder="Rechercher un produit..."
           value="<?= htmlspecialchars($recherche) ?>">
    <select name="categorie" class="form-control" onchange="this.form.submit()">
      <option value="0">Toutes les catégories</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>" <?= $idCategorie == $cat['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat['nom_categorie']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Rechercher</button>
    <?php if ($filtreActif): ?>
      <a href="produits.php" class="btn btn-ghost-admin btn-sm">Réinitialiser</a>
    <?php endif; ?>
  </form>
  <span class="admin-count"><?= count($produits) ?> produit<?= count($produits) > 1 ? 's' : '' ?></span>
</div>

<?php if (empty($produits)): ?>
  <div class="admin-empty-state">
    <div class="admin-empty-icon"><?= $filtreActif ? '🔍' : '🍞' ?></div>
    <h3><?= $filtreActif ? 'Aucun résultat' : 'Aucun produit' ?></h3>
    <p>
      <?php if ($filtreActif): ?>
        Aucun produit ne correspond à votre recherche.
      <?php else: ?>
        Commencez par ajouter votre premier produit au catalogue.
      <?php endif; ?>
    </p>
    <?php if ($filtreActif): ?>
      <a href="produits.php" class="btn btn-secondary">Réinitialiser</a>
    <?php else: ?>
      <a href="../ajouter.php" class="btn btn-primary">+ Ajouter un produit</a>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Produit</th>
          <th>Catégorie</th>
          <th>Prix</th>
          <th>Stock</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($produits as $p): ?>
          <tr>
            <td>
              <div class="admin-table-product">
                <div class="admin-table-thumb">
                  <?php $img = produitImage($p['image']); ?>
                  <?php if ($img != ''): ?>
                    <img src="../<?= htmlspecialchars($img) ?>" alt="">
                  <?php else: ?>
                    <span>🥖</span>
                  <?php endif; ?>
                </div>
                <strong><?= htmlspecialchars($p['nom_produit']) ?></strong>
              </div>
            </td>
            <td><span class="admin-badge"><?= htmlspecialchars($p['nom_categorie'] ?? '-') ?></span></td>
            <td class="admin-table-price"><?= number_format($p['prix'], 2) ?> MAD</td>
            <td>
              <span class="admin-stock <?= $p['quantite_stock'] < 5 ? 'admin-stock-low' : '' ?>">
                <?= (int)$p['quantite_stock'] ?>
              </span>
            </td>
            <td>
              <div class="admin-table-actions">
                <a href="../modifier.php?id=<?= $p['id'] ?>" class="btn btn-secondary btn-sm">Modifier</a>
                <a href="../supprimer.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm">Supprimer</a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/layout-end.php'; ?>
