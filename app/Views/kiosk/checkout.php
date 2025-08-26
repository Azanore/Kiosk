<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Récapitulatif</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin:0; background:#fafafa; color:#111; }
    header { padding:16px 20px; font-size:20px; font-weight:600; }
    .wrap { padding:16px; }
    .card { background:#fff; border-radius:14px; padding:18px; box-shadow:0 2px 10px rgba(0,0,0,.06); max-width:680px; margin:0 auto; }
    .row { margin-bottom:14px; }
    .label { font-weight:600; margin-right:8px; }
    .total { font-size:20px; font-weight:700; color:#0a7; }
    .btn { display:inline-block; padding:12px 16px; border-radius:10px; background:#0a7; color:#fff; text-decoration:none; border:0; cursor:pointer; font-size:18px; }
    .back { margin-left:16px; color:#0a7; text-decoration:none; }
  </style>
</head>
<body>
  <header>
    Paiement
    <a class="back" href="?r=kiosk/cart">Retour</a>
  </header>
  <div class="wrap">
    <form class="card" method="post" action="?r=kiosk/confirm">
      <div class="row"><span class="label">Total:</span> <span class="total"><?= Format::money((float)$total) ?></span></div>
      <div class="row">
        <div class="label">Type de commande</div>
        <label><input type="radio" name="order_type" value="eat_in" checked> Sur place</label>
        <label style="margin-left:12px;"><input type="radio" name="order_type" value="takeaway"> À emporter</label>
      </div>
      <div class="row">
        <div class="label">Paiement</div>
        <label><input type="radio" name="payment" value="card"> Carte</label>
        <label style="margin-left:12px;"><input type="radio" name="payment" value="counter" checked> Comptoir</label>
      </div>
      <div class="row">
        <button class="btn" type="submit">Confirmer</button>
      </div>
    </form>
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
