<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Catégories</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin:0; background:#fafafa; color:#111; }
    header { padding:16px 20px; font-size:20px; font-weight:600; }
    .grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px; padding:16px; }
    .card { background:#fff; border-radius:14px; padding:24px; text-align:center; box-shadow:0 2px 10px rgba(0,0,0,.06); }
    .card a { display:block; text-decoration:none; color:#111; font-size:20px; padding:10px 0; }
    .back { margin-left:16px; color:#0a7; text-decoration:none; }
  </style>
</head>
<body>
  <header>
    Catégories
    <a class="back" href="?r=kiosk/welcome">Accueil</a>
  </header>
  <main class="grid">
    <?php foreach ($categories as $c): ?>
      <div class="card">
        <a href="?r=kiosk/products&id=<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></a>
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
