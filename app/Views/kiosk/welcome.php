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
  <div class="kiosk-app">
    <header class="kiosk-header">
      <div class="kiosk-container">
        <div class="kiosk-brand"><span class="kiosk-dot"></span> Moroccan Café</div>
      </div>
    </header>
    <main>
      <div class="kiosk-container">
        <div class="wrap">
          <div class="card kiosk-card">
            <div class="title kiosk-title-xl">Bienvenue</div>
            <p class="hint kiosk-text-lg">Appuyez pour commencer votre commande.</p>
            <div style="margin-top:18px;">
              <a class="btn btn-lg kiosk-btn kiosk-btn-accent kiosk-btn-lg" href="?r=kiosk/categories">Commencer la commande</a>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
  <script>
    // Idle auto-reset: if no interaction for a long time, stay here (it's the home)
    // Other pages will redirect back here on idle.
  </script>
</body>
</html>
