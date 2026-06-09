<?php
/*
 * admin/categories.php - Gestion des catégories (CRUD)
 * PHP : formulaires POST, validation, upload d'image
 * SQL : INSERT, UPDATE, DELETE, SELECT
 */
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../includes/helpers.php';

exiger_connexion();

$adminPage = 'categories';
$adminTitle = 'Gestion des catégories';
$adminSubtitle = 'Organisez votre catalogue par familles de produits';

$erreurs = array();

// Traitement du formulaire (PHP : $_POST et $_FILES)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $nom    = trim($_POST['nom_categorie'] ?? '');
    $id     = (int)($_POST['id'] ?? 0);

    // --- AJOUTER une catégorie ---
    if ($action == 'add') {
        if ($nom == '') {
            $erreurs[] = 'Le nom de la catégorie est obligatoire.';
        } else {
            $verif = $pdo->prepare("SELECT COUNT(*) FROM categorie WHERE nom_categorie = ?");
            $verif->execute(array($nom));
            if ($verif->fetchColumn() > 0) {
                $erreurs[] = 'Cette catégorie existe déjà.';
            } else {
                // Upload de l'image (optionnel)
                $nomImage = null;
                if (!empty($_FILES['image']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $formatsOk = array('jpg', 'jpeg', 'png', 'webp', 'gif');
                    if (!in_array($ext, $formatsOk)) {
                        $erreurs[] = 'Format non supporté (jpg, png, webp, gif).';
                    } elseif ($_FILES['image']['size'] > 2097152) {
                        $erreurs[] = 'Image trop grande (max 2 Mo).';
                    } else {
                        $nomImage = uniqid('cat_') . '.' . $ext;
                        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../images/' . $nomImage);
                    }
                }

                if (empty($erreurs)) {
                    $pdo->prepare("INSERT INTO categorie (nom_categorie, image) VALUES (?, ?)")
                        ->execute(array($nom, $nomImage));
                    enregistrerMessage('success', 'Catégorie ajoutée avec succès.');
                    header('Location: categories.php');
                    exit;
                }
            }
        }
    }

    // --- MODIFIER une catégorie ---
    if ($action == 'edit' && $id > 0) {
        $req = $pdo->prepare("SELECT * FROM categorie WHERE id = ?");
        $req->execute(array($id));
        $categorieActuelle = $req->fetch();

        if (!$categorieActuelle) {
            $erreurs[] = 'Catégorie introuvable.';
        } elseif ($nom == '') {
            $erreurs[] = 'Le nom de la catégorie est obligatoire.';
        } else {
            $verif = $pdo->prepare("SELECT COUNT(*) FROM categorie WHERE nom_categorie = ? AND id != ?");
            $verif->execute(array($nom, $id));
            if ($verif->fetchColumn() > 0) {
                $erreurs[] = 'Ce nom est déjà utilisé.';
            } else {
                $nomImage = $categorieActuelle['image'];

                if (!empty($_FILES['image']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $formatsOk = array('jpg', 'jpeg', 'png', 'webp', 'gif');
                    if (!in_array($ext, $formatsOk)) {
                        $erreurs[] = 'Format non supporté.';
                    } elseif ($_FILES['image']['size'] > 2097152) {
                        $erreurs[] = 'Image trop grande (max 2 Mo).';
                    } else {
                        if (!empty($categorieActuelle['image']) && file_exists(__DIR__ . '/../images/' . $categorieActuelle['image'])) {
                            unlink(__DIR__ . '/../images/' . $categorieActuelle['image']);
                        }
                        $nomImage = uniqid('cat_') . '.' . $ext;
                        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../images/' . $nomImage);
                    }
                }

                if (empty($erreurs)) {
                    $pdo->prepare("UPDATE categorie SET nom_categorie = ?, image = ? WHERE id = ?")
                        ->execute(array($nom, $nomImage, $id));
                    enregistrerMessage('success', 'Catégorie mise à jour.');
                    header('Location: categories.php');
                    exit;
                }
            }
        }
    }

    // --- SUPPRIMER une catégorie ---
    if ($action == 'delete' && $id > 0) {
        $compte = $pdo->prepare("SELECT COUNT(*) FROM produit WHERE categorie_id = ?");
        $compte->execute(array($id));
        $nbProduits = (int)$compte->fetchColumn();

        if ($nbProduits > 0) {
            $erreurs[] = "Impossible de supprimer : $nbProduits produit(s) lié(s).";
        } else {
            $req = $pdo->prepare("SELECT image FROM categorie WHERE id = ?");
            $req->execute(array($id));
            $imageASupprimer = $req->fetchColumn();
            if (!empty($imageASupprimer) && file_exists(__DIR__ . '/../images/' . $imageASupprimer)) {
                unlink(__DIR__ . '/../images/' . $imageASupprimer);
            }

            $pdo->prepare("DELETE FROM categorie WHERE id = ?")->execute(array($id));
            enregistrerMessage('success', 'Catégorie supprimée.');
            header('Location: categories.php');
            exit;
        }
    }
}

// Liste des catégories avec le nombre de produits (SQL : JOIN + COUNT)
$categories = $pdo->query("
    SELECT c.*, COUNT(p.id) AS nb_produits
    FROM categorie c
    LEFT JOIN produit p ON p.categorie_id = c.id
    GROUP BY c.id
    ORDER BY c.nom_categorie
")->fetchAll();

$idModification = (int)($_GET['edit'] ?? 0);
$categorieAModifier = null;
foreach ($categories as $c) {
    if ($c['id'] == $idModification) {
        $categorieAModifier = $c;
        break;
    }
}

require __DIR__ . '/layout-start.php';
?>

<?php if (!empty($erreurs)): ?>
  <div class="alert alert-danger admin-flash">
    <?php foreach ($erreurs as $e): ?><div>⚠ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="admin-grid-2 admin-grid-categories">
  <section class="admin-panel">
    <div class="admin-panel-head">
      <h2><?= $categorieAModifier ? 'Modifier la catégorie' : 'Nouvelle catégorie' ?></h2>
    </div>
    <form method="POST" enctype="multipart/form-data" class="admin-form">
      <input type="hidden" name="action" value="<?= $categorieAModifier ? 'edit' : 'add' ?>">
      <?php if ($categorieAModifier): ?>
        <input type="hidden" name="id" value="<?= $categorieAModifier['id'] ?>">
      <?php endif; ?>

      <div class="form-group">
        <label class="form-label" for="nom_categorie">Nom de la catégorie</label>
        <input type="text" id="nom_categorie" name="nom_categorie" class="form-control"
               placeholder="Ex: Viennoiserie, Pâtisserie..."
               value="<?= htmlspecialchars($categorieAModifier['nom_categorie'] ?? $_POST['nom_categorie'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label class="form-label">Image de la catégorie</label>
        <?php if ($categorieAModifier): ?>
          <div class="admin-current-image">
            <img src="../<?= htmlspecialchars(categorieImage($categorieAModifier)) ?>" alt="">
            <span>Image actuelle</span>
          </div>
        <?php endif; ?>
        <input type="file" name="image" class="form-control" accept="image/*">
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">
          <?= $categorieAModifier ? 'Enregistrer' : '+ Ajouter' ?>
        </button>
        <?php if ($categorieAModifier): ?>
          <a href="categories.php" class="btn btn-secondary">Annuler</a>
        <?php endif; ?>
      </div>
    </form>
  </section>

  <section class="admin-panel">
    <div class="admin-panel-head">
      <h2>Catégories existantes</h2>
      <span class="admin-count"><?= count($categories) ?></span>
    </div>

    <?php if (empty($categories)): ?>
      <p class="admin-empty">Aucune catégorie. Créez-en une pour commencer.</p>
    <?php else: ?>
      <div class="admin-cat-list">
        <?php foreach ($categories as $cat): ?>
          <div class="admin-cat-item">
            <div class="admin-cat-icon">
              <img src="../<?= htmlspecialchars(categorieImage($cat)) ?>" alt="">
            </div>
            <div class="admin-cat-info">
              <strong><?= htmlspecialchars($cat['nom_categorie']) ?></strong>
              <span><?= (int)$cat['nb_produits'] ?> produit<?= $cat['nb_produits'] > 1 ? 's' : '' ?></span>
            </div>
            <div class="admin-cat-actions">
              <a href="categories.php?edit=<?= $cat['id'] ?>" class="btn btn-secondary btn-sm">Modifier</a>
              <?php if ((int)$cat['nb_produits'] == 0): ?>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cette catégorie ?');">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                  <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</div>

<?php require __DIR__ . '/layout-end.php'; ?>
