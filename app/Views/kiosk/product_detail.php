<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin:0; background:#fafafa; color:#111; }
    header { padding:16px 20px; font-size:20px; font-weight:600; }
    .wrap { padding:16px; }
    .card { background:#fff; border-radius:14px; padding:18px; box-shadow:0 2px 10px rgba(0,0,0,.06); max-width:680px; margin:0 auto; }
    .name { font-size:22px; font-weight:700; margin-bottom:10px; }
    .price { color:#0a7; font-weight:600; margin-bottom:12px; }
    .btn { display:inline-block; padding:12px 16px; border-radius:10px; background:#0a7; color:#fff; text-decoration:none; }
    .back { margin-left:16px; color:#0a7; text-decoration:none; }
    img { max-width:100%; height:220px; object-fit:cover; border-radius:10px; background:#eee; margin-bottom:12px; }
    .muted { color:#666; font-size:14px; }
  </style>
</head>
<body>
  <header>
    Détail produit
    <a class="back" href="?r=kiosk/products&id=<?= (int)$product['category_id'] ?>">Retour</a>
  </header>
  <div class="wrap">
    <div class="card">
      <?php if (!empty($product['image_url'])): ?>
        <img src="<?= htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="">
      <?php endif; ?>
      <div class="name"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></div>
      <div class="price"><?= Format::money((float)$product['base_price']) ?></div>
      <div class="muted">MVP: options minimales seront ajoutées plus tard.</div>
      <form method="post" action="?r=kiosk/addToCart&id=<?= (int)$product['id'] ?>">
        <label for="qty" class="muted">Quantité</label>
        <input id="qty" name="qty" type="number" value="1" min="1" style="font-size:16px; padding:8px; width:80px; margin:0 8px;">
        <button class="btn" type="submit">Ajouter au panier</button>
      </form>
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
