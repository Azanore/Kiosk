<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Menu (Catégories & Produits)</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin:0; background:#fafafa; color:#111; }
    header { padding:16px 20px; font-size:20px; font-weight:600; display:flex; justify-content:space-between; align-items:center; }
    .wrap { padding:16px; }
    h2 { margin:10px 0; font-size:18px; }
    table { width:100%; border-collapse:collapse; background:#fff; }
    th, td { padding:8px 10px; border-bottom:1px solid #eee; text-align:left; }
    input[type=text], input[type=number], select { padding:6px 8px; border:1px solid #ccc; border-radius:6px; }
    textarea { padding:6px 8px; border:1px solid #ccc; border-radius:6px; width:100%; height:60px; }
    .btn { padding:6px 10px; border-radius:8px; background:#0a7; color:#fff; border:0; cursor:pointer; }
    .btn.secondary { background:#555; }
    .row-form { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
    .logout { color:#b00; text-decoration:none; }
    .header-links a { margin-right:10px; }
    .section { margin-bottom:22px; }
  </style>
</head>
<body>
  <header>
    <div>Tableau de bord – Menu</div>
    <div class="header-links">
      <a href="?r=dashboard/orders">Commandes</a>
      <a class="logout" href="?r=auth/logout">Déconnexion</a>
    </div>
  </header>
  <div class="wrap">
    <div class="section">
      <h2>Catégories</h2>
      <form method="post" action="?r=dashboard/saveCategory" class="row-form" style="margin:8px 0;">
        <input type="hidden" name="id" value="">
        <input type="text" name="name" placeholder="Nom de la catégorie" required>
        <input type="number" name="sort_order" placeholder="Ordre" style="width:90px;">
        <label><input type="checkbox" name="is_active" checked> Active</label>
        <button class="btn" type="submit">Ajouter</button>
      </form>
      <table>
        <tr>
          <th>Nom</th><th>Ordre</th><th>Active</th><th>Actions</th>
        </tr>
        <?php foreach (($categories ?? []) as $c): ?>
          <tr>
            <td>
              <form method="post" action="?r=dashboard/saveCategory" class="row-form">
                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                <input type="text" name="name" value="<?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?>" required>
            </td>
            <td>
                <input type="number" name="sort_order" value="<?= htmlspecialchars((string)$c['sort_order'], ENT_QUOTES, 'UTF-8') ?>" style="width:90px;">
            </td>
            <td>
                <label><input type="checkbox" name="is_active" <?= ((int)$c['is_active'] === 1) ? 'checked' : '' ?>> Active</label>
            </td>
            <td>
                <button class="btn" type="submit">Enregistrer</button>
              </form>
              <form method="post" action="?r=dashboard/toggleCategory" style="display:inline-block; margin-left:6px;">
                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                <button class="btn secondary" type="submit">Basculer</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

    <div class="section">
      <h2>Produits</h2>
      <form method="get" action="" class="row-form" style="margin:8px 0;">
        <input type="hidden" name="r" value="dashboard/menu">
        <label>Catégorie:
          <select name="category_id" onchange="this.form.submit()">
            <option value="0" <?= (isset($category_id) && (int)$category_id === 0) ? 'selected' : '' ?>>Toutes</option>
            <?php foreach (($categories ?? []) as $c): ?>
              <option value="<?= (int)$c['id'] ?>" <?= ((int)($category_id ?? 0) === (int)$c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
          </select>
        </label>
      </form>

      <details open style="margin:8px 0 12px;">
        <summary>Ajouter un produit</summary>
        <form method="post" action="?r=dashboard/saveProduct" class="row-form" style="margin-top:8px;">
          <input type="hidden" name="id" value="">
          <label>Catégorie
            <select name="category_id" required>
              <?php foreach (($categories ?? []) as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= ((int)($category_id ?? 0) === (int)$c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <input type="text" name="name" placeholder="Nom" required>
          <input type="number" step="0.01" name="base_price" placeholder="Prix" style="width:120px;" required>
          <input type="text" name="image_url" placeholder="URL de l'image" style="min-width:220px;">
          <input type="number" name="sort_order" placeholder="Ordre" style="width:90px;">
          <label><input type="checkbox" name="is_available" checked> Disponible</label>
          <div style="width:100%"></div>
          <textarea name="description" placeholder="Description (optionnel)"></textarea>
          <button class="btn" type="submit">Ajouter</button>
        </form>
      </details>

      <table>
        <tr>
          <th>Produit</th><th>Catégorie</th><th>Prix</th><th>Ordre</th><th>Dispo</th><th>Image</th><th>Actions</th>
        </tr>
        <?php foreach (($products ?? []) as $p): ?>
          <tr>
            <td style="min-width:200px;">
              <form method="post" action="?r=dashboard/saveProduct" class="row-form">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <input type="text" name="name" value="<?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?>" required>
            </td>
            <td>
                <select name="category_id">
                  <?php foreach (($categories ?? []) as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= ((int)$p['category_id'] === (int)$c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></option>
                  <?php endforeach; ?>
                </select>
            </td>
            <td>
                <input type="number" step="0.01" name="base_price" value="<?= htmlspecialchars(number_format((float)$p['base_price'], 2, '.', ''), ENT_QUOTES, 'UTF-8') ?>" style="width:120px;" required>
            </td>
            <td>
                <input type="number" name="sort_order" value="<?= htmlspecialchars((string)$p['sort_order'], ENT_QUOTES, 'UTF-8') ?>" style="width:90px;">
            </td>
            <td>
                <label><input type="checkbox" name="is_available" <?= ((int)$p['is_available'] === 1) ? 'checked' : '' ?>> Oui</label>
            </td>
            <td style="min-width:240px;">
                <input type="text" name="image_url" value="<?= htmlspecialchars((string)$p['image_url'], ENT_QUOTES, 'UTF-8') ?>" placeholder="URL image">
            </td>
            <td>
                <button class="btn" type="submit">Enregistrer</button>
              </form>
              <form method="post" action="?r=dashboard/toggleProduct" style="display:inline-block; margin-left:6px;">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <input type="hidden" name="category_id" value="<?= (int)$p['category_id'] ?>">
                <button class="btn secondary" type="submit">Basculer</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</body>
</html>
