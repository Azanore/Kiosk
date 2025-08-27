<?php
// Kiosk welcome page
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bienvenue</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/css/kiosk.css">
</head>
<body class="kiosk kiosk-welcome">
  <div class="wrap">
    <div class="card">
      <div class="title">Bienvenue</div>
      <p class="hint">Appuyez pour commencer votre commande.</p>
      <div style="margin-top:18px;">
        <a class="btn btn-lg" href="?r=kiosk/categories">Commencer</a>
      </div>
    </div>
  </div>
  <script>
    // Idle auto-reset: if no interaction for a long time, stay here (it's the home)
    // Other pages will redirect back here on idle.
  </script>
</body>
</html>
