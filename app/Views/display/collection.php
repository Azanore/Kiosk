<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Écran de collecte</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/css/display.css">
</head>
<body class="display-screen">
  <header class="display-header">
    <div class="title">Commandes - Écran de collecte</div>
    <div class="muted">Récupérez votre reçu au kiosque</div>
  </header>
  <main class="display">
    <div class="columns">
      <div class="col preparing">
        <h1>En préparation</h1>
        <div id="prep" class="grid"></div>
      </div>
      <div class="col ready">
        <h1>Prêtes</h1>
        <div id="ready" class="grid"></div>
      </div>
    </div>
  </main>

  <audio id="readySound" src="assets/audio/ready.mp3" preload="auto"></audio>

  <script>
    const prepEl = document.getElementById('prep');
    const readyEl = document.getElementById('ready');
    const snd = document.getElementById('readySound');

    let lastReadySet = new Set();

    function render(list, el) {
      el.innerHTML = '';
      list.forEach(num => {
        const d = document.createElement('div');
        d.className = 'card';
        d.textContent = '#' + num;
        el.appendChild(d);
      });
    }

    async function fetchData() {
      try {
        const res = await fetch('?r=display/collectionData', { cache: 'no-store' });
        const json = await res.json();
        if (!json.ok) return;
        const prep = json.preparing || [];
        const ready = json.ready || [];
        render(prep, prepEl);
        render(ready, readyEl);
        // Detect new ready numbers and play sound
        const nowSet = new Set(ready);
        let hasNew = false;
        for (const n of nowSet) {
          if (!lastReadySet.has(n)) { hasNew = true; break; }
        }
        if (hasNew) {
          try { snd.currentTime = 0; snd.play().catch(()=>{}); } catch(e) {}
        }
        lastReadySet = nowSet;
      } catch (e) {
        // ignore transient errors
      }
    }

    setInterval(fetchData, 3000);
    fetchData();
  </script>
</body>
</html>
