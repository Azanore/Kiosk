<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Menu (Catégories & Produits)</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin">
  <header class="admin-header">
    <div class="brand">Administration</div>
    <nav class="admin-nav">
      <a href="?r=dashboard/orders">Commandes</a>
      <a href="?r=dashboard/menu" aria-current="page">Menu</a>
      <a href="?r=auth/logout" class="danger">Déconnexion</a>
    </nav>
  </header>
  <main>
    <h1>Menu</h1>
    <section class="section panel">
      <h2>Catégories</h2>
      <form method="post" action="?r=dashboard/saveCategory" class="row-form">
        <input type="hidden" name="id" value="">
        <label for="cat-name-new" class="visually-hidden">Nom</label>
        <input id="cat-name-new" class="grow" type="text" name="name" placeholder="Nom de la catégorie" required>
        <label for="cat-order-new" class="visually-hidden">Ordre</label>
        <input id="cat-order-new" class="w-xxs" type="number" name="sort_order" placeholder="Ordre">
        <div class="actions">
          <button class="btn btn-primary" type="submit">Ajouter</button>
        </div>
      </form>
      <table>
        <thead>
          <tr>
            <th>Nom</th>
            <th>Ordre</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($categories ?? [])): ?>
          <tr>
            <td colspan="4" class="muted">Aucune catégorie pour le moment.</td>
          </tr>
        <?php else: foreach (($categories ?? []) as $c): ?>
          <tr>
            <td>
              <form method="post" action="?r=dashboard/saveCategory" class="row-form">
                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                <label class="visually-hidden" for="cat-name-<?= (int)$c['id'] ?>">Nom</label>
                <input id="cat-name-<?= (int)$c['id'] ?>" class="grow" type="text" name="name" value="<?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?>" required>
            </td>
            <td>
                <label class="visually-hidden" for="cat-order-<?= (int)$c['id'] ?>">Ordre</label>
                <input id="cat-order-<?= (int)$c['id'] ?>" class="w-xxs" type="number" name="sort_order" value="<?= htmlspecialchars((string)$c['sort_order'], ENT_QUOTES, 'UTF-8') ?>">
            </td>
            <td>
              <?php $catActive = (int)$c['is_active'] === 1; ?>
              <span class="badge" style="background:<?= $catActive ? '#16a34a' : '#dc2626' ?>; color:#fff;">
                <?= $catActive ? 'Actif' : 'Inactif' ?>
              </span>
            </td>
            <td>
                <button class="btn btn-primary" type="submit">Enregistrer</button>
              </form>
              <form method="post" action="?r=dashboard/toggleCategory" class="inline">
                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                <button class="btn btn-secondary" type="submit"><?= ((int)$c['is_active'] === 1) ? 'Désactiver' : 'Activer' ?></button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </section>

    <section class="section panel">
      <h2>Produits</h2>
      <form method="get" action="" class="row-form">
        <input type="hidden" name="r" value="dashboard/menu">
        <label for="filter-category" class="visually-hidden">Catégorie</label>
        <select id="filter-category" name="category_id" onchange="this.form.submit()">
            <option value="0" <?= (isset($category_id) && (int)$category_id === 0) ? 'selected' : '' ?>>Toutes</option>
            <?php foreach (($categories ?? []) as $c): ?>
              <option value="<?= (int)$c['id'] ?>" <?= ((int)($category_id ?? 0) === (int)$c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
      </form>

      <details open>
        <summary>Ajouter un produit</summary>
        <form method="post" action="?r=dashboard/saveProduct" class="row-form">
          <input type="hidden" name="id" value="">
          <label for="prod-category-new" class="visually-hidden">Catégorie</label>
          <select id="prod-category-new" name="category_id" required>
              <?php foreach (($categories ?? []) as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= ((int)($category_id ?? 0) === (int)$c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></option>
              <?php endforeach; ?>
          </select>
          <label for="prod-name-new" class="visually-hidden">Nom</label>
          <input id="prod-name-new" class="grow" type="text" name="name" placeholder="Nom" required>
          <label for="prod-price-new" class="visually-hidden">Prix</label>
          <input id="prod-price-new" class="w-xs" type="number" step="0.01" name="base_price" placeholder="Prix" required>
          <label for="prod-image-new" class="visually-hidden">URL de l'image</label>
          <input id="prod-image-new" class="grow" type="text" name="image_url" placeholder="URL de l'image">
          <label for="prod-order-new" class="visually-hidden">Ordre</label>
          <input id="prod-order-new" class="w-xxs" type="number" name="sort_order" placeholder="Ordre">
          <label for="prod-desc-new" class="visually-hidden">Description (optionnel)</label>
          <textarea id="prod-desc-new" class="grow" name="description" placeholder="Description (optionnel)"></textarea>
          <div class="actions">
            <button class="btn btn-primary" type="submit">Ajouter</button>
          </div>
        </form>
      </details>

      <table>
        <thead>
          <tr>
            <th>Produit</th>
            <th>Catégorie</th>
            <th>Prix</th>
            <th>Ordre</th>
            <th>Statut</th>
            <th>Image</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($products ?? [])): ?>
          <tr>
            <td colspan="7" class="muted">Aucun produit à afficher.</td>
          </tr>
        <?php else: foreach (($products ?? []) as $p): ?>
          <tr>
            <td>
              <form method="post" action="?r=dashboard/saveProduct" class="row-form">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <label class="visually-hidden" for="prod-name-<?= (int)$p['id'] ?>">Nom</label>
                <input id="prod-name-<?= (int)$p['id'] ?>" class="grow" type="text" name="name" value="<?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?>" required>
            </td>
            <td>
                <select name="category_id">
                  <?php foreach (($categories ?? []) as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= ((int)$p['category_id'] === (int)$c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></option>
                  <?php endforeach; ?>
                </select>
            </td>
            <td>
                <label class="visually-hidden" for="prod-price-<?= (int)$p['id'] ?>">Prix</label>
                <input id="prod-price-<?= (int)$p['id'] ?>" class="w-xs" type="number" step="0.01" name="base_price" value="<?= htmlspecialchars(number_format((float)$p['base_price'], 2, '.', ''), ENT_QUOTES, 'UTF-8') ?>" required>
            </td>
            <td>
                <label class="visually-hidden" for="prod-order-<?= (int)$p['id'] ?>">Ordre</label>
                <input id="prod-order-<?= (int)$p['id'] ?>" class="w-xxs" type="number" name="sort_order" value="<?= htmlspecialchars((string)$p['sort_order'], ENT_QUOTES, 'UTF-8') ?>">
            </td>
            <td>
              <?php $pAvail = (int)$p['is_available'] === 1; ?>
              <span class="badge" style="background:<?= $pAvail ? '#16a34a' : '#dc2626' ?>; color:#fff;">
                <?= $pAvail ? 'Disponible' : 'Indisponible' ?>
              </span>
            </td>
            <td>
                <label class="visually-hidden" for="prod-image-<?= (int)$p['id'] ?>">Image</label>
                <input id="prod-image-<?= (int)$p['id'] ?>" class="grow" type="text" name="image_url" value="<?= htmlspecialchars((string)$p['image_url'], ENT_QUOTES, 'UTF-8') ?>" placeholder="URL image">
            </td>
            <td>
                <button class="btn btn-primary" type="submit">Enregistrer</button>
              </form>
              <form method="post" action="?r=dashboard/toggleProduct" class="inline">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <button class="btn btn-secondary" type="submit"><?= ((int)$p['is_available'] === 1) ? 'Désactiver' : 'Activer' ?></button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </section>
  </main>
</body>
</html>
