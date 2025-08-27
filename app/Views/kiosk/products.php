<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Produits</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/css/kiosk.css">
</head>
<body class="kiosk">
  <header class="kiosk">
    Produits
    <a class="back" href="?r=kiosk/categories">Retour</a>
  </header>
  <div class="kiosk-products-container">
    <main class="kiosk-products grid">
      <?php if (empty($products)): ?>
        <div class="card" style="grid-column: 1 / -1;">
          <div class="name">Aucun produit à afficher</div>
          <div class="muted" style="margin-top:6px;">Veuillez sélectionner une autre catégorie ou revenir plus tard</div>
          <div style="margin-top:12px;"><a class="btn" href="?r=kiosk/categories">Catégories</a></div>
        </div>
      <?php else: ?>
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
      <?php endif; ?>
    </main>
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
