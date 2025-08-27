<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/css/kiosk.css">
</head>
<body class="kiosk">
  <header class="kiosk">
    Détail produit
    <a class="back" href="?r=kiosk/products&id=<?= (int)$product['category_id'] ?>">Retour</a>
  </header>
  <div class="detail-container">
    <div class="detail card">
      <?php if (!empty($product['image_url'])): ?>
        <img src="<?= htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="">
      <?php endif; ?>
      <div class="name"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></div>
      <div class="price"><?= Format::money((float)$product['base_price']) ?></div>
      <form method="post" action="?r=kiosk/addToCart&id=<?= (int)$product['id'] ?>" class="row-form">
        <label for="qty" class="muted">Quantité</label>
        <div class="qty-stepper">
          <button type="button" id="decQty" class="btn dec" aria-label="Diminuer" onclick="(function(){var q=document.getElementById('qty'); if(!q) return; q.stepDown(); if(parseInt(q.value||'1',10)<1) q.value=1;})();">−</button>
          <input id="qty" name="qty" type="number" value="1" min="1" step="1" inputmode="numeric" class="w-xxs">
          <button type="button" id="incQty" class="btn inc" aria-label="Augmenter" onclick="(function(){var q=document.getElementById('qty'); if(!q) return; q.stepUp();})();">+</button>
        </div>
        <div class="actions">
          <button class="btn" type="submit">Ajouter au panier</button>
        </div>
      </form>
    </div>
  </div>
  <script>
    (function(){
      var idleMs = (<?= (int)(require dirname(__DIR__, 3) . '/Config/app.php')['kiosk_idle_seconds'] ?? 90 ?>) * 1000;
      var timer = setTimeout(function(){ window.location.href='?r=kiosk/welcome'; }, idleMs);
      function reset(){ clearTimeout(timer); timer = setTimeout(function(){ window.location.href='?r=kiosk/welcome'; }, idleMs); }
      ['click','keydown','touchstart','mousemove'].forEach(function(ev){ document.addEventListener(ev, reset, {passive:true}); });

      // Quantity stepper: minimal JS using native stepUp/stepDown
      var qty = document.getElementById('qty');
      var dec = document.getElementById('decQty');
      var inc = document.getElementById('incQty');
      if (qty && dec && inc) {
        dec.addEventListener('click', function(){
          qty.stepDown();
          if (parseInt(qty.value || '1', 10) < 1) qty.value = 1;
        });
        inc.addEventListener('click', function(){
          qty.stepUp();
        });
        qty.addEventListener('change', function(){
          if (parseInt(qty.value || '1', 10) < 1) qty.value = 1;
        });
      }
    })();
  </script>
</body>
</html>
