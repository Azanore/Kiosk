<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Confirmation</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/css/kiosk.css">
</head>
<body class="kiosk kiosk-confirm">
  <div class="wrap">
    <div>
      <div>Numéro de commande</div>
      <div class="num">#<?= (int)$orderNumber ?></div>
      <div class="muted">Type: <?= $orderType === 'takeaway' ? 'À emporter' : 'Sur place' ?> — Paiement: <?= $payment === 'card' ? 'Carte' : 'Comptoir' ?></div>
      <div class="notice">Veuillez récupérer votre reçu au kiosque maintenant.</div>
      <div class="muted" style="margin-top:6px; font-size:14px;">Si le reçu ne s’imprime pas, contactez le personnel.</div>
      <a class="btn" href="?r=kiosk/welcome">Nouvelle commande</a>
    </div>
  </div>
  <script>
    // Retour automatique à l'accueil après n secondes (config)
    var seconds = <?= isset($confirmSeconds) ? (int)$confirmSeconds : 12 ?>;
    if (!seconds || seconds < 1) { seconds = 12; }
    setTimeout(function(){ window.location.href='?r=kiosk/welcome'; }, seconds * 1000);
  </script>
</body>
</html>
