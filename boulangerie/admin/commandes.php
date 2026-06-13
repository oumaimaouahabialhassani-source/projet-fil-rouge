<?php
/*
 * admin/commandes.php - Gestion des commandes (back office)
 * PHP : SELECT, UPDATE, gestion du stock
 */
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../includes/helpers.php';

exiger_connexion();

$adminPage = 'commandes';
$adminTitle = 'Commandes';
$adminSubtitle = 'Commandes à traiter et historique';

// Marquer une commande comme traitée → diminue le stock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'traiter') {
    $commandeId = (int)($_POST['id'] ?? 0);

    $stmt = $pdo->prepare("
        SELECT c.*, p.quantite_stock, p.nom_produit
        FROM commande c
        JOIN produit p ON p.id = c.produit_id
        WHERE c.id = ? AND c.statut = 'en_attente'
    ");
    $stmt->execute([$commandeId]);
    $commande = $stmt->fetch();

    if ($commande && $commande['quantite'] <= $commande['quantite_stock']) {
        $pdo->prepare("UPDATE produit SET quantite_stock = quantite_stock - ? WHERE id = ?")
            ->execute([$commande['quantite'], $commande['produit_id']]);
        $pdo->prepare("UPDATE commande SET statut = 'traitee' WHERE id = ?")
            ->execute([$commandeId]);
        enregistrerMessage('success', 'Commande traitée — stock mis à jour.');
    } else {
        enregistrerMessage('danger', 'Impossible de traiter (stock insuffisant).');
    }

    header('Location: commandes.php');
    exit;
}

$commandes = $pdo->query("
    SELECT c.*, p.nom_produit, p.prix
    FROM commande c
    JOIN produit p ON p.id = c.produit_id
    ORDER BY c.date_commande DESC
")->fetchAll();

$nbEnAttente = (int)$pdo->query("SELECT COUNT(*) FROM commande WHERE statut = 'en_attente'")->fetchColumn();

require __DIR__ . '/layout-start.php';
?>

<div class="admin-stats">
  <div class="admin-stat-card">
    <span class="admin-stat-label">Total commandes</span>
    <span class="admin-stat-value"><?= count($commandes) ?></span>
  </div>
  <div class="admin-stat-card admin-stat-warn">
    <span class="admin-stat-label">À traiter</span>
    <span class="admin-stat-value"><?= $nbEnAttente ?></span>
  </div>
</div>

<section class="admin-panel">
  <div class="admin-panel-head">
    <h2>Liste des commandes</h2>
  </div>

  <?php if (empty($commandes)): ?>
    <p class="admin-empty">Aucune commande pour le moment.</p>
  <?php else: ?>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Client</th>
            <th>Produit</th>
            <th>Qté</th>
            <th>Statut</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($commandes as $cmd): ?>
            <tr>
              <td><?= date('d/m/Y H:i', strtotime($cmd['date_commande'])) ?></td>
              <td>
                <strong><?= htmlspecialchars($cmd['nom_client']) ?></strong><br>
                <span class="text-muted"><?= htmlspecialchars($cmd['telephone']) ?></span>
              </td>
              <td><?= htmlspecialchars($cmd['nom_produit']) ?></td>
              <td><?= (int)$cmd['quantite'] ?></td>
              <td>
                <?php if ($cmd['statut'] === 'en_attente'): ?>
                  <span class="badge badge-warn">En attente</span>
                <?php else: ?>
                  <span class="badge badge-ok">Traitée</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($cmd['statut'] === 'en_attente'): ?>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="traiter">
                    <input type="hidden" name="id" value="<?= $cmd['id'] ?>">
                    <button type="submit" class="btn btn-primary btn-sm">Traiter</button>
                  </form>
                <?php else: ?>
                  —
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/layout-end.php'; ?>
