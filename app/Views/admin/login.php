<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion Admin</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/css/admin.css">
  <style>
    body.admin {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--color-surface);
    }
    body.admin main {
      width: 100%;
      max-width: 400px;
      padding: 2rem;
    }
    .login-form {
      width: 100%;
      background: white;
      padding: 2rem;
      border-radius: var(--radius);
      box-shadow: var(--shadow-1);
    }
    .login-form h1 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: var(--color-text);
    }
    .login-form .error {
      background: #fee2e2;
      color: #b91c1c;
      padding: 0.75rem 1rem;
      border-radius: var(--radius);
      margin-bottom: 1.5rem;
      text-align: center;
    }
    .login-form .actions {
      margin-top: 1.5rem;
    }
    .login-form .btn {
      width: 100%;
      padding: 0.75rem;
      font-size: 1rem;
    }
  </style>
</head>
<body class="admin">
  <main>
    <form method="post" class="login-form">
      <h1>Connexion Admin</h1>
      <?php if (!empty($error)): ?><div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
      <label for="email">Email</label>
      <input id="email" name="email" type="email" required>
      <label for="password">Mot de passe</label>
      <input id="password" name="password" type="password" required>
      <div class="actions">
        <button class="btn btn-primary" type="submit">Se connecter</button>
      </div>
    </form>
  </main>
</body>
</html>
