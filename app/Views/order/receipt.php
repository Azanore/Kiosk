<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Reçu</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/css/receipt.css">
</head>
<body class="receipt-print">
  <div class="receipt-wrap">
    <div class="receipt">
  <div class="center">
    <div class="big"><?= htmlspecialchars($cfg['cafe_name'] ?? 'Café', ENT_QUOTES, 'UTF-8') ?></div>
    <?php if (!empty($cfg['cafe_address'])): ?><div><?= htmlspecialchars($cfg['cafe_address'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if (!empty($cfg['cafe_phone'])): ?><div>Tel: <?= htmlspecialchars($cfg['cafe_phone'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <div style="margin-top:8px;">Date/Heure: <?= htmlspecialchars(substr((string)($order['created_at'] ?? $order['display_date']), 0, 19), ENT_QUOTES, 'UTF-8') ?></div>
    <div class="big" style="margin-top:6px;">N° <?= (int)$order['display_number'] ?></div>
  </div>
  <hr>
  <table class="items">
    <?php foreach ($items as $it): ?>
      <tr>
        <td><?= (int)$it['quantity'] ?> × <?= htmlspecialchars($it['product_name'], ENT_QUOTES, 'UTF-8') ?></td>
        <td style="text-align:right;">&nbsp;<?= Format::money((float)$it['line_total']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <div class="total">
    <div>Total: <?= Format::money((float)$order['total_price']) ?></div>
    <div>Paiement: <?= $order['payment_method'] === 'card' ? 'Carte' : 'Comptoir' ?></div>
    <div>Type: <?= $order['order_type'] === 'takeaway' ? 'À emporter' : 'Sur place' ?></div>
  </div>
  <?php if (($order['payment_method'] ?? '') === 'counter'): ?>
    <div class="center" style="margin-top:6px;">Merci de régler au comptoir</div>
  <?php endif; ?>
  <div class="center" style="margin-top:10px;">Merci et à bientôt</div>
    </div>
    <div id="fallback" class="receipt-actions center" style="display:none;">
      <div class="muted" style="margin-bottom:6px;">Si l'impression ne démarre pas, vous pouvez :</div>
      <a href="?r=dashboard/orders">Retour au tableau de bord</a>
    </div>
  </div>
  <script>
    // Manual print: show actions by default; no auto window.print()
    (function(){
      var fb = document.getElementById('fallback');
      if (fb) fb.style.display = 'block';
      // Add an Imprimer button programmatically next to Fermer for clarity
      try {
        var printBtn = document.createElement('button');
        printBtn.textContent = 'Imprimer';
        printBtn.style.cssText = 'padding:8px 12px; border:0; border-radius:8px; background:#06c; color:#fff; cursor:pointer; margin-right:8px;';
        printBtn.onclick = function(){ window.print(); };
        fb.insertBefore(printBtn, fb.firstChild);
      } catch(e) { /* no-op */ }
    })();
  </script>
</body>
</html>
