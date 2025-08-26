<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Reçu</title>
  <style>
    body { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
    .center { text-align:center; }
    .big { font-size:28px; font-weight:800; }
    table { width:100%; border-collapse:collapse; }
    td { padding:4px 0; }
    .items td { border-bottom:1px dashed #ccc; }
    .items tr:last-child td { border-bottom:0; }
    .total { border-top:1px dashed #000; margin-top:8px; padding-top:8px; }
  </style>
</head>
<body>
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
        <td style="text-align:right;"><?= Format::money((float)$it['line_total']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <div class="total">
    Total: <?= Format::money((float)$order['total_price']) ?> — Paiement: <?= $order['payment_method'] === 'card' ? 'Carte' : 'Comptoir' ?> — Type: <?= $order['order_type'] === 'takeaway' ? 'À emporter' : 'Sur place' ?>
  </div>
  <?php if (($order['payment_method'] ?? '') === 'counter'): ?>
    <div class="center" style="margin-top:6px;">Merci de régler au comptoir</div>
  <?php endif; ?>
  <div class="center" style="margin-top:10px;">Merci et à bientôt</div>
  <div id="fallback" class="center" style="margin-top:12px; display:none;">
    <div class="muted" style="margin-bottom:6px;">Si l'impression ne démarre pas, vous pouvez :</div>
    <button onclick="window.close()" style="padding:8px 12px; border:0; border-radius:8px; background:#0a7; color:#fff; cursor:pointer;">Fermer</button>
    <a href="?r=dashboard/orders" style="margin-left:8px;">Retour au tableau de bord</a>
  </div>
  <style>@media print { #fallback { display:none !important; } }</style>
  <script>
    // Auto print on load and show fallback after print
    window.onload = function() { try { window.print(); } catch(e) {} };
    function showFallback(){ var el = document.getElementById('fallback'); if (el) el.style.display = 'block'; }
    if (window.matchMedia) { var mq = window.matchMedia('print'); if (mq && mq.addListener) { mq.addListener(function(m){ if (!m.matches) showFallback(); }); } }
    window.onafterprint = showFallback;
    // Also show fallback after a short delay in case print blocked
    setTimeout(showFallback, 2000);
  </script>
</body>
</html>
