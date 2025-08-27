<?php
declare(strict_types=1);

class DashboardController extends BaseController
{
    public function orders(): void
    {
        $this->requireAdmin();
        $pdo = DB::pdo();
        // Auto-cancel unpaid orders older than N minutes (from config) to keep the queue clean
        // Cancels only orders that are still in 'awaiting_payment'
        try {
            $cfg = require BASE_PATH . '/app/Config/app.php';
            $mins = (int)($cfg['auto_cancel_minutes'] ?? 15);
            if ($mins > 0) {
                $pdo->exec("UPDATE orders SET status='cancelled' WHERE status='awaiting_payment' AND created_at < (NOW() - INTERVAL " . $mins . " MINUTE)");
            }
        } catch (Throwable $e) {
            // Silent fail to avoid breaking the orders page
        }
        $scope = isset($_GET['scope']) ? (string)$_GET['scope'] : 'today';
        $q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
        if ($scope === 'all') {
            if ($q !== '' && ctype_digit($q)) {
                $stmt = $pdo->prepare("SELECT id, display_number, display_date, status, payment_method, order_type, total_price, created_at FROM orders WHERE display_number = ? ORDER BY created_at DESC LIMIT 200");
                $stmt->execute([(int)$q]);
            } else {
                $stmt = $pdo->query("SELECT id, display_number, display_date, status, payment_method, order_type, total_price, created_at FROM orders ORDER BY created_at DESC LIMIT 200");
            }
        } else {
            // default 'today' uses display_date which matches the daily reset logic
            if ($q !== '' && ctype_digit($q)) {
                $stmt = $pdo->prepare("SELECT id, display_number, display_date, status, payment_method, order_type, total_price, created_at FROM orders WHERE display_date = CURRENT_DATE() AND display_number = ? ORDER BY created_at DESC LIMIT 200");
                $stmt->execute([(int)$q]);
            } else {
                $stmt = $pdo->prepare("SELECT id, display_number, display_date, status, payment_method, order_type, total_price, created_at FROM orders WHERE display_date = CURRENT_DATE() ORDER BY created_at DESC LIMIT 200");
                $stmt->execute();
            }
        }
        $rows = $stmt->fetchAll();
        $this->render('admin/orders', [ 'orders' => $rows, 'scope' => $scope, 'q' => $q ]);
    }

    public function updateStatus(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Méthode non autorisée'; return; }
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $status = (string)($_POST['status'] ?? '');
        $allowed = ['awaiting_payment','paid','preparing','ready','completed','cancelled'];
        if ($id <= 0 || !in_array($status, $allowed, true)) { http_response_code(400); echo 'Paramètres invalides'; return; }
        $pdo = DB::pdo();
        // Fetch current status/payment
        $cur = $pdo->prepare("SELECT status, payment_method FROM orders WHERE id = ? LIMIT 1");
        $cur->execute([$id]);
        $row = $cur->fetch();
        if (!$row) { http_response_code(404); echo 'Commande introuvable'; return; }
        $currentStatus = (string)$row['status'];
        // Enforce: cannot move to preparing/ready/completed if not paid yet
        $restrictedTargets = ['preparing','ready','completed'];
        if ($currentStatus === 'awaiting_payment' && in_array($status, $restrictedTargets, true)) {
            $scope = isset($_POST['scope']) ? (string)$_POST['scope'] : 'today';
            $q = isset($_POST['q']) ? trim((string)$_POST['q']) : '';
            $redir = '?r=dashboard/orders&scope=' . urlencode($scope);
            if ($q !== '') { $redir .= '&q=' . urlencode($q); }
            header('Location: ' . $redir);
            return;
        }
        $stmt = $pdo->prepare("UPDATE orders SET status = ?, paid_at = CASE WHEN ? = 'paid' THEN NOW() ELSE paid_at END WHERE id = ?");
        $stmt->execute([$status, $status, $id]);
        $scope = isset($_POST['scope']) ? (string)$_POST['scope'] : 'today';
        $q = isset($_POST['q']) ? trim((string)$_POST['q']) : '';
        $redir = '?r=dashboard/orders&scope=' . urlencode($scope);
        if ($q !== '') { $redir .= '&q=' . urlencode($q); }
        header('Location: ' . $redir);
    }

    public function menu(): void
    {
        $this->requireAdmin();
        $pdo = DB::pdo();
        // Categories
        $categories = $pdo->query("SELECT id, name, is_active, sort_order FROM categories ORDER BY COALESCE(sort_order,9999), name")->fetchAll();
        // Products (optionally filtered by category)
        $catId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
        if ($catId > 0) {
            $stmt = $pdo->prepare("SELECT p.id, p.category_id, p.name, p.base_price, p.is_available, p.sort_order, c.name AS category_name, p.image_url FROM products p JOIN categories c ON c.id=p.category_id WHERE p.category_id = ? ORDER BY COALESCE(p.sort_order,9999), p.name LIMIT 300");
            $stmt->execute([$catId]);
        } else {
            $stmt = $pdo->query("SELECT p.id, p.category_id, p.name, p.base_price, p.is_available, p.sort_order, c.name AS category_name, p.image_url FROM products p JOIN categories c ON c.id=p.category_id ORDER BY c.name, COALESCE(p.sort_order,9999), p.name LIMIT 300");
        }
        $products = $stmt->fetchAll();
        $this->render('admin/menu', [ 'categories' => $categories, 'products' => $products, 'category_id' => $catId ]);
    }

    public function saveCategory(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Méthode non autorisée'; return; }
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $name = trim((string)($_POST['name'] ?? ''));
        // Checkbox removed from UI: preserve current value on update; default to 1 on insert
        $pdo = DB::pdo();
        if ($id > 0) {
            $cur = $pdo->prepare("SELECT is_active FROM categories WHERE id = ?");
            $cur->execute([$id]);
            $row = $cur->fetch();
            $isActive = $row ? (int)$row['is_active'] : 1;
        } else {
            $isActive = 1;
        }
        $sort = isset($_POST['sort_order']) && $_POST['sort_order'] !== '' ? (int)$_POST['sort_order'] : null;
        if ($name === '') { http_response_code(400); echo 'Nom requis'; return; }
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, is_active = ?, sort_order = ? WHERE id = ?");
            $stmt->execute([$name, $isActive, $sort, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO categories (name, is_active, sort_order) VALUES (?, ?, ?)");
            $stmt->execute([$name, $isActive, $sort]);
        }
        header('Location: ?r=dashboard/menu');
    }

    public function toggleCategory(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Méthode non autorisée'; return; }
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) { http_response_code(400); echo 'ID invalide'; return; }
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("UPDATE categories SET is_active = 1 - is_active WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: ?r=dashboard/menu');
    }

    public function saveProduct(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Méthode non autorisée'; return; }
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $name = trim((string)($_POST['name'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $price = (float)($_POST['base_price'] ?? 0);
        $image = trim((string)($_POST['image_url'] ?? ''));
        // Checkbox removed from UI: preserve current value on update; default to 1 on insert
        $pdo = DB::pdo();
        if ($id > 0) {
            $cur = $pdo->prepare("SELECT is_available FROM products WHERE id = ?");
            $cur->execute([$id]);
            $prow = $cur->fetch();
            $isAvailable = $prow ? (int)$prow['is_available'] : 1;
        } else {
            $isAvailable = 1;
        }
        $sort = isset($_POST['sort_order']) && $_POST['sort_order'] !== '' ? (int)$_POST['sort_order'] : null;
        if ($categoryId <= 0 || $name === '') { http_response_code(400); echo 'Paramètres requis'; return; }
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE products SET category_id=?, name=?, description=?, base_price=?, image_url=?, is_available=?, sort_order=? WHERE id=?");
            $stmt->execute([$categoryId, $name, $description, $price, $image, $isAvailable, $sort, $id]);
            // Keep context when editing an existing product
            header('Location: ?r=dashboard/menu&category_id=' . $categoryId);
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, base_price, image_url, is_available, sort_order) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([$categoryId, $name, $description, $price, $image, $isAvailable, $sort]);
            // After creating a new product, do not filter – the select controls filtering
            header('Location: ?r=dashboard/menu');
        }
    }

    public function toggleProduct(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Méthode non autorisée'; return; }
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) { http_response_code(400); echo 'ID invalide'; return; }
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("UPDATE products SET is_available = 1 - is_available WHERE id = ?");
        $stmt->execute([$id]);
        // Always return to unfiltered menu (select control manages filtering)
        header('Location: ?r=dashboard/menu');
    }
}
