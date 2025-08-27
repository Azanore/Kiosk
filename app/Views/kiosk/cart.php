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
  <header class="kiosk">
    Panier
    <a class="back" href="?r=kiosk/categories">Continuer les achats</a>
  </header>
  <div class="row">
    <table>
      <thead>
        <tr><th>Article</th><th>Prix</th><th>Qté</th><th>Total</th><th></th></tr>
      </thead>
      <tbody>
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
      </tbody>
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
