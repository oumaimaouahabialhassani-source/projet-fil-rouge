<?php
/*
 * login.php - Page de connexion administrateur
 * PHP : formulaire POST, $_POST, sessions, password_verify
 */
require_once 'db.php';
require_once 'auth.php';

if (est_connecte()) {
    header('Location: admin/index.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login'] ?? '');
    $mdp   = $_POST['mot_de_passe'] ?? '';

    if ($login == '' || $mdp == '') {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        // Vérifier l'utilisateur en base (SQL : SELECT WHERE)
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE login = ?");
        $stmt->execute(array($login));
        $user = $stmt->fetch();

        // Vérifier le mot de passe hashé
        if ($user && password_verify($mdp, $user['mot_de_passe'])) {
            $_SESSION['admin_id']    = $user['id'];
            $_SESSION['admin_login'] = $user['login'];
            header('Location: admin/index.php');
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
<body class="login-body">

<div class="login-split">
  <div class="login-split-left">
    <a href="index.php" class="login-back">← Retour au site</a>
    <div class="login-brand-block">
      <img src="image/logo.svg" alt="" width="72" height="72">
      <h1>Boulangerie Ouahabi</h1>
      <p>Espace d'administration du catalogue produits et catégories.</p>
    </div>
    <div class="login-deco">
      <span>Depuis 2001</span>
      <span>·</span>
      <span>Tanger</span>
    </div>
  </div>

  <div class="login-split-right">
    <div class="login-card">
      <h2>Connexion admin</h2>
      <p class="login-card-sub">Accédez au back office pour gérer votre catalogue.</p>

      <?php if ($erreur != ''): ?>
        <div class="alert alert-danger">⚠ <?= htmlspecialchars($erreur) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group">
          <label class="form-label" for="login">Identifiant</label>
          <input type="text" id="login" name="login" class="form-control"
                 placeholder="admin" value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="mot_de_passe">Mot de passe</label>
          <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control"
                 placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-primary login-submit">Se connecter</button>
      </form>
    </div>
  </div>
</div>

</body>
</html>
