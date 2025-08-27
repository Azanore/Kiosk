<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Récapitulatif</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/css/kiosk.css">
</head>
<body class="kiosk">
  <div class="kiosk-app">
    <header class="kiosk-header">
      <div class="kiosk-container">
        <div class="kiosk-brand"><span class="kiosk-dot"></span> Moroccan Café</div>
        <div class="kiosk-steps" style="margin-left:auto;">
          <a class="kiosk-step" href="?r=kiosk/cart">Panier</a>
          <div class="kiosk-step is-active">Paiement</div>
        </div>
      </div>
    </header>
    <div class="kiosk-container">
      <div class="detail-container">
        <div class="detail card kiosk-card">
          <form class="row-form" method="post" action="?r=kiosk/confirm">
            <div class="row"><span class="label">Total:</span> <span class="total"><?= Format::money((float)$total) ?></span></div>
            <div class="row">
              <div class="label">Type de commande</div>
              <div class="inline">
                <label><input type="radio" name="order_type" value="eat_in" checked> Sur place</label>
                <label><input type="radio" name="order_type" value="takeaway"> À emporter</label>
              </div>
            </div>
            <div class="row">
              <div class="label">Paiement</div>
              <div class="inline">
                <label><input type="radio" name="payment" value="card"> Carte</label>
                <label><input type="radio" name="payment" value="counter" checked> Comptoir</label>
              </div>
            </div>
            <div class="actions">
              <button class="btn kiosk-btn kiosk-btn-primary" type="submit">Confirmer et payer</button>
            </div>
          </form>
        </div>
      </div>
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
