<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion Admin</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; background:#fafafa; margin:0; }
    .wrap { display:flex; min-height:100vh; align-items:center; justify-content:center; }
    form { background:#fff; padding:24px; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.08); width:320px; }
    h1 { margin:0 0 16px; font-size:22px; }
    label { display:block; margin:8px 0 4px; }
    input { width:100%; padding:10px; font-size:16px; border-radius:8px; border:1px solid #ddd; }
    .btn { width:100%; padding:12px; margin-top:14px; border:0; border-radius:10px; background:#0a7; color:#fff; font-size:16px; cursor:pointer; }
    .error { color:#b00; margin-bottom:8px; }
  </style>
</head>
<body>
  <div class="wrap">
    <form method="post">
      <h1>Connexion</h1>
      <?php if (!empty($error)): ?><div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
      <label for="email">Email</label>
      <input id="email" name="email" type="email" required>
      <label for="password">Mot de passe</label>
      <input id="password" name="password" type="password" required>
      <button class="btn" type="submit">Se connecter</button>
    </form>
  </div>
</body>
</html>
