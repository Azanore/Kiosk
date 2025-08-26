<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panier</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin:0; background:#fafafa; color:#111; }
    header { padding:16px 20px; font-size:20px; font-weight:600; }
    table { width:100%; border-collapse:collapse; background:#fff; box-shadow:0 2px 10px rgba(0,0,0,.06); }
    th, td { padding:12px 14px; border-bottom:1px solid #eee; text-align:left; }
    .qty a { display:inline-block; padding:6px 10px; background:#eee; border-radius:8px; text-decoration:none; color:#111; }
    .row { padding:16px; }
    .total { text-align:right; font-size:18px; font-weight:700; margin-top:12px; }
    .actions { display:flex; gap:12px; justify-content:flex-end; margin-top:16px; }
    .btn { display:inline-block; padding:12px 16px; border-radius:10px; background:#0a7; color:#fff; text-decoration:none; }
    .muted { color:#666; }
  </style>
</head>
<body>
  <header>
    Panier
    <a class="muted" href="?r=kiosk/categories" style="margin-left:12px;">Continuer les achats</a>
  </header>
  <div class="row">
    <table>
      <tr><th>Article</th><th>Prix</th><th>Qté</th><th>Total</th><th></th></tr>
      <?php if (empty($items)): ?>
        <tr><td colspan="5" class="muted">Votre panier est vide.</td></tr>
      <?php else: foreach ($items as $it): ?>
        <tr>
          <td><?= htmlspecialchars($it['name'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= Format::money((float)$it['price']) ?></td>
          <td class="qty">
            <a href="?r=kiosk/decQty&id=<?= (int)$it['id'] ?>">−</a>
            <strong style="margin:0 8px;"><?= (int)$it['qty'] ?></strong>
            <a href="?r=kiosk/incQty&id=<?= (int)$it['id'] ?>">+</a>
          </td>
          <td><?= Format::money((float)$it['price'] * (int)$it['qty']) ?></td>
          <td><a class="muted" href="?r=kiosk/remove&id=<?= (int)$it['id'] ?>">Supprimer</a></td>
        </tr>
      <?php endforeach; endif; ?>
    </table>
    <div class="total">Total: <?= Format::money((float)$total) ?></div>
    <div class="actions">
      <?php if (!empty($items)): ?>
        <a class="btn" href="?r=kiosk/checkout">Passer au paiement</a>
      <?php endif; ?>
    </div>
  </div>
  <script>
    (function(){
      var idleMs = (<?= (int)(require dirname(__DIR__, 3) . '/Config/app.php')['kiosk_idle_seconds'] ?? 90 ?>) * 1000;
      var timer = setTimeout(function(){ window.location.href='?r=kiosk/welcome'; }, idleMs);
      function reset(){ clearTimeout(timer); timer = setTimeout(function(){ window.location.href='?r=kiosk/welcome'; }, idleMs); }
      ['click','keydown','touchstart','mousemove'].forEach(function(ev){ document.addEventListener(ev, reset, {passive:true}); });
    })();
  </script>
</body>
</html>
