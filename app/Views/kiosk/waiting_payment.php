<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Paiement en cours</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/css/kiosk.css">
</head>
<body class="kiosk kiosk-wait">
  <div class="kiosk-app">
    <header class="kiosk-header">
      <div class="kiosk-container">
        <div class="kiosk-brand"><span class="kiosk-dot"></span> Moroccan Café</div>
        <div class="kiosk-steps" style="margin-left:auto;">
          <div class="kiosk-step is-active">Paiement</div>
        </div>
      </div>
    </header>
    <div class="wrap">
      <div class="card kiosk-card">
      <div>Numéro de commande</div>
      <div class="num">#<?= (int)$orderNumber ?></div>
      <div class="muted" style="margin-top:8px;">Montant: <?= Format::money((float)$total) ?></div>
      <div class="spinner"></div>
      <div>Paiement par carte en cours… Veuillez suivre les instructions sur le terminal.</div>
      <div style="margin-top:12px;"><a class="back kiosk-btn kiosk-btn-ghost" href="?r=kiosk/welcome">Retour à l'accueil</a></div>
      </div>
    </div>
  </div>
  <script>
    const orderId = <?= (int)$orderId ?>;
    let tries = 0;
    const maxTries = 40; // ~2 minutes at 3s interval
    let stopped = false;
    async function poll() {
      if (stopped) return;
      try {
        const res = await fetch(`?r=order/pollStatus&id=${orderId}`, { cache: 'no-store' });
        const data = await res.json();
        if (data && data.ok && data.data) {
          if (data.data.status === 'paid') {
            window.location.href = `?r=kiosk/paid&id=${orderId}`;
            return;
          }
        }
      } catch (e) {}
      tries++;
      if (tries < maxTries) {
        setTimeout(poll, 3000);
      } else {
        stopped = true;
        // Show timeout actions
        const card = document.querySelector('.card');
        if (card) {
          const div = document.createElement('div');
          div.style.marginTop = '12px';
          div.innerHTML = '<div class="muted">Temps dépassé. Veuillez réessayer ou basculer au comptoir.</div>'+
            '<div style="margin-top:10px;">'+
            '<button id="retryBtn" style="padding:10px 14px;border:0;border-radius:8px;background:#0a7;color:#fff;cursor:pointer;">Réessayer</button> '+
            '<a id="counterBtn" href="?r=order/switchToCounter&id=' + orderId + '" class="btn" style="margin-left:8px;">Basculer au comptoir</a> '+
            '<a href="?r=kiosk/welcome" style="margin-left:8px;">Accueil</a>'+
            '</div>';
          card.appendChild(div);
          const rb = document.getElementById('retryBtn');
          if (rb) rb.onclick = function(){ tries = 0; stopped = false; poll(); };
        }
      }
    }
    poll();

    // Inactivity auto-reset (~kiosk_idle_seconds)
    (function(){
      var idleMs = (<?= (int)(require dirname(__DIR__, 3) . '/Config/app.php')['kiosk_idle_seconds'] ?? 90 ?>) * 1000;
      var timer = setTimeout(function(){ window.location.href='?r=kiosk/welcome'; }, idleMs);
      function reset(){ clearTimeout(timer); timer = setTimeout(function(){ window.location.href='?r=kiosk/welcome'; }, idleMs); }
      ['click','keydown','touchstart','mousemove'].forEach(function(ev){ document.addEventListener(ev, reset, {passive:true}); });
    })();
  </script>
</body>
</html>

