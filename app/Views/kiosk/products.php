<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Produits</title>
  <link rel="stylesheet" href="<?= $baseUrl('assets/css/app.css') ?>">
  <link rel="stylesheet" href="<?= $baseUrl('assets/css/kiosk.css') ?>">
</head>
<body class="kiosk">
  <div class="kiosk-app">
    <header class="kiosk-header">
      <div class="kiosk-container">
        <div class="kiosk-brand"><span class="kiosk-dot"></span> Moroccan Café</div>
        <div class="kiosk-current-page">
          <div class="kiosk-step is-active">Produits</div>
        </div>
        <div class="kiosk-return">
          <a class="kiosk-step" href="?r=kiosk/categories">Catégories</a>
        </div>
      </div>
    </header>
    <div class="kiosk-products-container">
      <main class="kiosk-products grid kiosk-grid cols-3">
      <?php if (empty($products)): ?>
        <div class="card kiosk-card" style="grid-column: 1 / -1;">
          <div class="name">Aucun produit à afficher</div>
          <div class="muted" style="margin-top:6px;">Veuillez sélectionner une autre catégorie ou revenir plus tard</div>
          <div style="margin-top:12px;"><a class="btn kiosk-btn kiosk-btn-primary" href="?r=kiosk/categories">Catégories</a></div>
        </div>
      <?php else: ?>
      <?php foreach ($products as $p): ?>
        <div class="card kiosk-card" onclick="window.location.href='?r=kiosk/productDetail&id=<?= (int)$p['id'] ?>'">
          <?php if (!empty($p['image_url'])): ?>
            <img src="<?= $baseUrl(ltrim($p['image_url'] ?? '', '/')) ?>" alt="<?= htmlspecialchars('Photo de ' . $p['name'], ENT_QUOTES, 'UTF-8') ?>">
          <?php endif; ?>
          <div class="name"><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></div>
          <div class="price"><?= Format::money((float)$p['base_price']) ?></div>
          <a class="btn kiosk-btn kiosk-btn-accent" href="?r=kiosk/productDetail&id=<?= (int)$p['id'] ?>" onclick="event.stopPropagation();">Choisir</a>
        </div>
      <?php endforeach; ?>
      <?php endif; ?>
      </main>
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

