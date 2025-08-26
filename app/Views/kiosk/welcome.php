<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bienvenue – Kiosk</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin:0; background:#fafafa; color:#111; }
    .wrap { display:flex; min-height:100vh; align-items:center; justify-content:center; }
    .card { text-align:center; padding:32px; background:#fff; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,.08); }
    .title { font-size:28px; margin:0 0 16px; }
    .btn { display:inline-block; padding:18px 28px; font-size:20px; border-radius:12px; background:#0a7; color:#fff; text-decoration:none; }
    .hint { margin-top:12px; color:#666; font-size:14px; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h1 class="title">Bienvenue au kiosque</h1>
      <a class="btn" href="?r=kiosk/categories">Commencer la commande</a>
      <div class="hint">MVP – Utilisez le bouton pour continuer.</div>
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
