<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Catégories</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/css/kiosk.css">
</head>
<body class="kiosk">
  <header class="kiosk">
    Catégories
    <a class="back" href="?r=kiosk/welcome">Accueil</a>
  </header>
  <div class="kiosk-cats-container">
    <main class="kiosk-cats grid">
      <?php if (empty($categories)): ?>
        <div class="card" style="grid-column: 1 / -1;">
          <div class="name">Aucune catégorie à afficher</div>
          <div class="muted" style="margin-top:6px;">Veuillez revenir plus tard</div>
          <div style="margin-top:12px;"><a class="btn" href="?r=kiosk/welcome">Accueil</a></div>
        </div>
      <?php else: ?>
      <?php foreach ($categories as $c): ?>
        <div class="card">
          <a href="?r=kiosk/products&id=<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></a>
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
