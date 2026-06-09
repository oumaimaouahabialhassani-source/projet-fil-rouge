<?php
// login.php - Page de connexion de l'administrateur
require_once 'db.php';
require_once 'auth.php';

// Si l'admin est deja connecte, inutile de revenir ici
if (est_connecte()) {
    header('Location: index.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $mdp   = $_POST['mot_de_passe'] ?? '';

    if ($login === '' || $mdp === '') {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        // On cherche l'utilisateur dans la base
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        // On verifie le mot de passe (compare avec le hash stocke)
        if ($user && password_verify($mdp, $user['mot_de_passe'])) {
            $_SESSION['admin_id']    = $user['id'];
            $_SESSION['admin_login'] = $user['login'];
            header('Location: index.php');
            exit;
        } else {
            $erreur = "Login ou mot de passe incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion admin - Boulangerie Ouahabi</title>
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
      <li><a href="produits.php">Produits</a></li>
    </ul>
    <div class="navbar-actions">
      <a href="index.php" class="btn btn-secondary">← Retour</a>
    </div>
  </div>
</nav>

<div class="login-page">
  <div class="login-card">
    <h2 class="form-card-title" style="text-align:center;">Espace administrateur</h2>
    <p class="form-card-subtitle" style="text-align:center;border-bottom:none;padding-bottom:0;margin-bottom:1.75rem;">
      Connectez-vous pour gerer le catalogue.
    </p>

    <?php if ($erreur !== ''): ?>
      <div class="alert alert-danger">⚠ <?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label class="form-label" for="login">Login</label>
        <input type="text" id="login" name="login" class="form-control"
               placeholder="admin" value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label class="form-label" for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control"
               placeholder="••••••••" required>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
        Se connecter
      </button>
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

</body>
</html>
