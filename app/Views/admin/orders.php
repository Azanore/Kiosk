<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Commandes</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin:0; background:#fafafa; color:#111; }
    header { padding:16px 20px; font-size:20px; font-weight:600; display:flex; justify-content:space-between; align-items:center; }
    table { width:100%; border-collapse:collapse; background:#fff; }
    th, td { padding:10px 12px; border-bottom:1px solid #eee; text-align:left; }
    .wrap { padding:16px; }
    .btn { padding:6px 10px; border-radius:8px; background:#0a7; color:#fff; border:0; cursor:pointer; }
    .logout { color:#b00; text-decoration:none; }
  </style>
</head>
<body>
  <header>
    <div>
      Tableau de bord
      <span style="margin-left:16px; font-size:14px; font-weight:400;">
        <a href="?r=dashboard/orders&scope=today" style="margin-right:8px;<?= (isset($scope) && $scope==='today') ? 'text-decoration:underline;' : '' ?>">Aujourd'hui</a>
        <a href="?r=dashboard/orders&scope=all" <?= (isset($scope) && $scope==='all') ? 'style="text-decoration:underline;"' : '' ?>>Tout</a>
        <a href="?r=dashboard/menu" style="margin-left:12px;">Menu</a>
      </span>
    </div>
    <div><a class="logout" href="?r=auth/logout">Déconnexion</a></div>
  </header>
  <div class="wrap">
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
      foreach ($order as $st):
        $list = $groups[$st] ?? [];
        $count = count($list);
        if ($count === 0) continue;
    ?>
      <h3 style="margin:6px 0 8px; font-size:18px; font-weight:700;"><?= $labels[$st] ?> (<?= $count ?>)</h3>
      <table>
        <tr>
          <th>#</th><th>Date</th><th>Paiement</th><th>Type</th><th>Total</th><th>Actions</th>
        </tr>
        <?php foreach ($list as $o): ?>
          <tr>
            <td><?= (int)$o['display_number'] ?></td>
            <td><?= htmlspecialchars($o['display_date'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= $o['payment_method'] === 'card' ? 'Carte' : 'Comptoir' ?></td>
            <td><?= $o['order_type'] === 'takeaway' ? 'À emporter' : 'Sur place' ?></td>
            <td><?= Format::money((float)$o['total_price']) ?></td>
            <td>
              <?php if ($o['status'] === 'awaiting_payment'): ?>
                <form method="post" action="?r=dashboard/updateStatus" style="display:inline-block; margin-right:6px;">
                  <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                  <input type="hidden" name="status" value="paid">
                  <input type="hidden" name="scope" value="<?= htmlspecialchars($scope ?? 'today', ENT_QUOTES, 'UTF-8') ?>">
                  <button class="btn" type="submit">Marquer payé</button>
                </form>
              <?php endif; ?>
              <form method="post" action="?r=dashboard/updateStatus" style="display:inline-block;">
                <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                <input type="hidden" name="scope" value="<?= htmlspecialchars($scope ?? 'today', ENT_QUOTES, 'UTF-8') ?>">
                <select name="status">
                  <option value="paid">Payé</option>
                  <option value="preparing">En préparation</option>
                  <option value="ready">Prêt</option>
                  <option value="completed">Terminé</option>
                  <option value="cancelled">Annulé</option>
                </select>
                <button class="btn" type="submit">Mettre à jour</button>
              </form>
              <a class="btn" href="?r=order/printReceipt&id=<?= (int)$o['id'] ?>" style="text-decoration:none;">Imprimer</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
      <div style="height:8px;"></div>
    <?php endforeach; ?>
  </div>
  <script>
    // Auto-refresh every 15s to keep board up-to-date
    setTimeout(function(){ location.reload(); }, 15000);
  </script>
</body>
</html>
