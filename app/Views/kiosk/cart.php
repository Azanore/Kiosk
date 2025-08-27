<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panier</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/css/kiosk.css">
</head>
<body class="kiosk">
  <div class="kiosk-app">
    <header class="kiosk-header">
      <div class="kiosk-container">
        <div class="kiosk-brand"><span class="kiosk-dot"></span> Moroccan Café</div>
        <div class="kiosk-steps" style="margin-left:auto;">
          <a class="kiosk-step" href="?r=kiosk/categories">Continuer les achats</a>
          <div class="kiosk-step is-active">Panier</div>
        </div>
      </div>
    </header>
    <div class="kiosk-container">
      <div class="row">
        <div class="card kiosk-card">
          <div class="kiosk-title-lg" style="margin-bottom:10px;">Votre panier</div>
          <table class="kiosk-table">
      <thead>
        <tr><th>Article</th><th>Prix</th><th>Qté</th><th>Total</th><th></th></tr>
      </thead>
      <tbody>
      <?php if (empty($items)): ?>
        <tr class="kiosk-row"><td colspan="5" class="muted">Votre panier est vide.</td></tr>
      <?php else: foreach ($items as $it): ?>
        <tr class="kiosk-row">
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
      </tbody>
          </table>
          <div class="total kiosk-title-md" style="margin-top:12px;">Total: <?= Format::money((float)$total) ?></div>
          <div class="actions" style="margin-top:10px;">
        <?php if (!empty($items)): ?>
          <a class="btn kiosk-btn kiosk-btn-primary" href="?r=kiosk/checkout">Passer au paiement</a>
        <?php endif; ?>
          </div>
        </div>
      </div>
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

