<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Paiement en cours</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin:0; background:#fff; color:#111; }
    .wrap { display:flex; min-height:100vh; align-items:center; justify-content:center; text-align:center; padding:24px; }
    .card { max-width:560px; width:100%; border:1px solid #eee; border-radius:16px; padding:24px; box-shadow:0 2px 12px rgba(0,0,0,.06); }
    .num { font-size:48px; font-weight:800; color:#0a7; }
    .muted { color:#666; }
    .spinner { width:40px; height:40px; border:4px solid #e2e8f0; border-top-color:#0a7; border-radius:50%; margin:18px auto; animation:spin 1s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div>Numéro de commande</div>
      <div class="num">#<?= (int)$orderNumber ?></div>
      <div class="muted" style="margin-top:8px;">Montant: <?= Format::money((float)$total) ?></div>
      <div class="spinner"></div>
      <div>Paiement par carte en cours… Veuillez suivre les instructions sur le terminal.</div>
      <div style="margin-top:12px;"><a href="?r=kiosk/welcome" style="color:#0a7;">Retour à l'accueil</a></div>
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
          div.innerHTML = '<div class="muted">Temps dépassé. Veuillez réessayer ou payer au comptoir.</div>'+
            '<div style="margin-top:10px;">'+
            '<button id="retryBtn" style="padding:10px 14px;border:0;border-radius:8px;background:#0a7;color:#fff;cursor:pointer;">Réessayer</button> '+
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
