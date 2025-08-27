<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Commandes</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin">
  <header class="admin-header">
    <div class="brand">Administration</div>
    <nav class="admin-nav">
      <a href="?r=dashboard/orders" aria-current="page">Commandes</a>
      <a href="?r=dashboard/menu">Menu</a>
      <a href="?r=auth/logout" class="danger">Déconnexion</a>
    </nav>
  </header>
  <main>
    <h1>Commandes</h1>
    <section class="section panel">
      <form method="get" action="" class="row-form">
        <input type="hidden" name="r" value="dashboard/orders">
        <input type="hidden" name="scope" value="<?= htmlspecialchars($scope ?? 'today', ENT_QUOTES, 'UTF-8') ?>">
        <label for="search-order" class="visually-hidden">Numéro de commande</label>
        <input id="search-order" class="w-xs" type="text" name="q" value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="# de commande">
        <div class="actions">
          <button class="btn btn-primary" type="submit">Rechercher</button>
          <?php if (!empty($q)): ?>
            <a class="btn btn-outline" href="?r=dashboard/orders&scope=<?= htmlspecialchars($scope ?? 'today', ENT_QUOTES, 'UTF-8') ?>">Effacer</a>
          <?php endif; ?>
        </div>
      </form>
      <div class="row" style="margin-top:6px;">
        <a href="?r=dashboard/orders&scope=today<?= isset($q) && $q!=='' ? ('&q='.urlencode($q)) : '' ?>" <?= (isset($scope) && $scope==='today') ? 'aria-current="page"' : '' ?>>Aujourd'hui</a>
        <span style="margin:0 8px; color:#999;">|</span>
        <a href="?r=dashboard/orders&scope=all<?= isset($q) && $q!=='' ? ('&q='.urlencode($q)) : '' ?>" <?= (isset($scope) && $scope==='all') ? 'aria-current="page"' : '' ?>>Tout</a>
      </div>
    </section>
    <?php
      $order = ['awaiting_payment','paid','preparing','ready','completed','cancelled'];
      $labels = [
        'awaiting_payment' => "En attente de paiement",
        'paid' => 'Payé',
        'preparing' => 'En préparation',
        'ready' => 'Prêt',
        'completed' => 'Terminé',
        'cancelled' => 'Annulé',
      ];
      $groups = [];
      foreach ($orders as $o) { $groups[$o['status']][] = $o; }
      $totalCount = 0; foreach ($order as $st) { $totalCount += count($groups[$st] ?? []); }
      if ($totalCount === 0): ?>
      <section class="section panel">
        <h2>Commandes</h2>
        <div class="muted">Aucune commande à afficher pour ce filtre.</div>
      </section>
    <?php endif; ?>
    <?php
      foreach ($order as $st):
        $list = $groups[$st] ?? [];
        $count = count($list);
        if ($count === 0) continue;
    ?>
      <section class="section panel">
        <h2><?= $labels[$st] ?> (<?= $count ?>)</h2>
        <table>
          <thead>
            <tr>
              <th>#</th><th>Date</th><th>Statut</th><th>Paiement</th><th>Type</th><th>Total</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
        <?php foreach ($list as $o): ?>
          <tr>
            <td><?= (int)$o['display_number'] ?></td>
            <td><?= htmlspecialchars($o['display_date'], ENT_QUOTES, 'UTF-8') ?></td>
            <td>
              <?php
                $badge = [
                  'awaiting_payment' => '#b78 "En attente"',
                  'paid' => '#0a7 "Payé"',
                  'preparing' => '#06c "En préparation"',
                  'ready' => '#f80 "Prêt"',
                  'completed' => '#555 "Terminé"',
                  'cancelled' => '#b00 "Annulé"',
                ];
                $style = '#555 "Statut"';
                if (isset($badge[$o['status']])) { $style = $badge[$o['status']]; }
                // parse style like "#color label"
                preg_match('/^(#[0-9a-fA-F]{3,6})\s+"(.+)"$/', $style, $m);
                $color = $m[1] ?? '#555';
                $text = $m[2] ?? htmlspecialchars($o['status'], ENT_QUOTES, 'UTF-8');
              ?>
              <span style="display:inline-block; padding:2px 8px; border-radius:999px; background:<?= $color ?>; color:#fff; font-size:12px; font-weight:700;">
                <?= $text ?>
              </span>
            </td>
            <td><?= $o['payment_method'] === 'card' ? 'Carte' : 'Comptoir' ?></td>
            <td><?= $o['order_type'] === 'takeaway' ? 'À emporter' : 'Sur place' ?></td>
            <td><?= Format::money((float)$o['total_price']) ?></td>
            <td>
              <?php $cur = $o['status']; ?>
              <?php if ($cur === 'awaiting_payment'): ?>
                <form method="post" action="?r=dashboard/updateStatus" class="inline">
                  <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                  <input type="hidden" name="status" value="paid">
                  <input type="hidden" name="scope" value="<?= htmlspecialchars($scope ?? 'today', ENT_QUOTES, 'UTF-8') ?>">
                  <?php if (!empty($q)): ?><input type="hidden" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
                  <button class="btn btn-success" type="submit">Marquer payé</button>
                </form>
                <form method="post" action="?r=dashboard/updateStatus" class="inline">
                  <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                  <input type="hidden" name="status" value="cancelled">
                  <input type="hidden" name="scope" value="<?= htmlspecialchars($scope ?? 'today', ENT_QUOTES, 'UTF-8') ?>">
                  <?php if (!empty($q)): ?><input type="hidden" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
                  <button class="btn btn-danger" type="submit">Annuler</button>
                </form>
              <?php else: ?>
                <!-- generic select for non-awaiting_payment statuses -->
                <form method="post" action="?r=dashboard/updateStatus" class="inline">
                  <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                  <input type="hidden" name="scope" value="<?= htmlspecialchars($scope ?? 'today', ENT_QUOTES, 'UTF-8') ?>">
                  <?php if (!empty($q)): ?><input type="hidden" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
                  <select name="status">
                    <option value="paid">Payé</option>
                    <option value="preparing">En préparation</option>
                    <option value="ready">Prêt</option>
                    <option value="completed">Terminé</option>
                    <option value="cancelled">Annulé</option>
                  </select>
                  <button class="btn btn-primary" type="submit">Mettre à jour</button>
                </form>
              <?php endif; ?>
              <?php if ($cur !== 'cancelled'): ?>
                <a class="btn btn-secondary" href="?r=order/printReceipt&id=<?= (int)$o['id'] ?>">Imprimer</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
          </tbody>
        </table>
      </section>
    <?php endforeach; ?>
  </main>
  <div class="refresh-floating">
    <button id="refreshBtn" class="btn" type="button">Rafraîchir</button>
  </div>
  <script>
    // Manual refresh button
    (function(){
      var b = document.getElementById('refreshBtn');
      if (b) { b.addEventListener('click', function(){ location.reload(); }); }
    })();
  </script>
</body>
</html>
