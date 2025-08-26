<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Confirmation</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin:0; background:#0a7; color:#fff; }
    .wrap { display:flex; min-height:100vh; align-items:center; justify-content:center; text-align:center; padding:24px; }
    .num { font-size:64px; font-weight:800; letter-spacing:1px; }
    .muted { opacity:.9; margin-top:8px; }
    .btn { display:inline-block; margin-top:18px; padding:12px 18px; border-radius:10px; background:#fff; color:#0a7; text-decoration:none; font-weight:700; }
  </style>
</head>
<body>
  <div class="wrap">
    <div>
      <div>Numéro de commande</div>
      <div class="num">#<?= (int)$orderNumber ?></div>
      <div class="muted">Type: <?= $orderType === 'takeaway' ? 'À emporter' : 'Sur place' ?> — Paiement: <?= $payment === 'card' ? 'Carte' : 'Comptoir' ?></div>
      <a class="btn" href="?r=kiosk/welcome">Nouvelle commande</a>
    </div>
  </div>
  <script>
    // Retour automatique à l'accueil après ~12 secondes
    setTimeout(function(){ window.location.href='?r=kiosk/welcome'; }, 12000);
  </script>
</body>
</html>
