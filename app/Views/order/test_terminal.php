<?php
/** @var int $orderId */
/** @var int $orderNumber */
/** @var float $total */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Terminal de test - Paiement</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/css/display.css">
</head>
<body class="display-screen terminal-test">
  <div class="wrap">
    <h1>Paiement par carte (Test)</h1>
    <div class="muted">Commande N° <?= htmlspecialchars((string)$orderNumber, ENT_QUOTES, 'UTF-8') ?></div>
    <div class="amount">Total: <?= number_format($total, 2, '.', ' ') ?> DH</div>

    <div class="btns">
      <a class="btn approve" href="?r=order/testPaymentApprove&id=<?= urlencode((string)$orderId) ?>">Approuver</a>
      <a class="btn decline" href="?r=order/testPaymentDecline&id=<?= urlencode((string)$orderId) ?>">Refuser</a>
    </div>

    <div class="note">
      Ceci est un simulateur de terminal de paiement pour les tests. Aucun paiement réel n'est effectué.
    </div>
  </div>
</body>
</html>
