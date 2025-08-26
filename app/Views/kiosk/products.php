<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Produits</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin:0; background:#fafafa; color:#111; }
    header { padding:16px 20px; font-size:20px; font-weight:600; }
    .grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px; padding:16px; }
    .card { background:#fff; border-radius:14px; padding:18px; text-align:center; box-shadow:0 2px 10px rgba(0,0,0,.06); }
    .name { font-size:18px; margin:8px 0; }
    .price { color:#0a7; font-weight:600; margin-bottom:10px; }
    .btn { display:inline-block; padding:10px 14px; border-radius:10px; background:#0a7; color:#fff; text-decoration:none; }
    .back { margin-left:16px; color:#0a7; text-decoration:none; }
    img { max-width:100%; height:140px; object-fit:cover; border-radius:10px; background:#eee; }
  </style>
</head>
<body>
  <header>
    Produits
    <a class="back" href="?r=kiosk/categories">Retour</a>
  </header>
  <main class="grid">
    <?php foreach ($products as $p): ?>
      <div class="card">
        <?php if (!empty($p['image_url'])): ?>
          <img src="<?= htmlspecialchars($p['image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="">
        <?php endif; ?>
        <div class="name"><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></div>
        <div class="price"><?= Format::money((float)$p['base_price']) ?></div>
        <a class="btn" href="?r=kiosk/productDetail&id=<?= (int)$p['id'] ?>">Choisir</a>
      </div>
    <?php endforeach; ?>
  </main>
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
