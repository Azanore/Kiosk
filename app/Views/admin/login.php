<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion Admin</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin">
  <main>
    <div class="wrap">
      <form method="post" class="card">
        <h1>Connexion</h1>
        <?php if (!empty($error)): ?><div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required>
        <label for="password">Mot de passe</label>
        <input id="password" name="password" type="password" required>
        <div class="actions">
          <button class="btn" type="submit">Se connecter</button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
