<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Paiement réussi</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/css/kiosk.css">
</head>
<body class="kiosk kiosk-confirm">
  <div class="kiosk-app">
    <div class="kiosk-center kiosk-confirm-hero">
      <div class="kiosk-card kiosk-confirm-card">
        <div class="kiosk-confirm-icon" aria-hidden="true">
          <!-- simple success check icon -->
          <svg width="72" height="72" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="11" stroke="currentColor" stroke-width="2" opacity="0.25"/>
            <path d="M7 12.5l3.2 3.2L17 9" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h1 class="kiosk-confirm-title">Paiement réussi</h1>
        <div class="kiosk-confirm-sub">Numéro de commande</div>
        <div class="kiosk-order-number">#<?= (int)$orderNumber ?></div>
        <div class="kiosk-confirm-meta">
          Type: <?= $orderType === 'takeaway' ? 'À emporter' : 'Sur place' ?> — Paiement: <?= $payment === 'card' ? 'Carte' : 'Comptoir' ?>
        </div>
        <div class="kiosk-confirm-note">Veuillez récupérer votre reçu au kiosque maintenant.</div>
        <div class="kiosk-confirm-help">Si le reçu ne s’imprime pas, contactez le personnel.</div>
        <div class="kiosk-space-md"></div>
        <a class="kiosk-btn kiosk-btn-confirm kiosk-btn-lg" href="?r=kiosk/welcome">Nouvelle commande</a>
      </div>
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

