<?php
declare(strict_types=1);

class KioskController extends BaseController
{
    public function welcome(): void
    {
        $this->render('kiosk/welcome', []);
    }

    public function categories(): void
    {
        $menu = new MenuService();
        $cats = $menu->getActiveCategories();
        $this->render('kiosk/categories', [
            'categories' => $cats,
        ]);
    }

    public function products(): void
    {
        $categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($categoryId <= 0) {
            header('Location: ?r=kiosk/categories');
            return;
        }
        $menu = new MenuService();
        $products = $menu->getProductsByCategory($categoryId);
        $this->render('kiosk/products', [
            'categoryId' => $categoryId,
            'products' => $products,
        ]);
    }

    public function productDetail(): void
    {
        $productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($productId <= 0) {
            header('Location: ?r=kiosk/categories');
            return;
        }
        $menu = new MenuService();
        $product = $menu->getProduct($productId);
        if (!$product) {
            header('Location: ?r=kiosk/categories');
            return;
        }
        $this->render('kiosk/product_detail', [
            'product' => $product,
        ]);
    }

    public function addToCart(): void
    {
        $productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($productId <= 0) { header('Location: ?r=kiosk/categories'); return; }
        $qty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;
        $menu = new MenuService();
        $product = $menu->getProduct($productId);
        if (!$product) { header('Location: ?r=kiosk/categories'); return; }

        $_SESSION['cart'] = $_SESSION['cart'] ?? [];
        if (!isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => (float)$product['base_price'],
                'qty' => 0,
            ];
        }
        $_SESSION['cart'][$productId]['qty'] += $qty;
        header('Location: ?r=kiosk/cart');
    }

    public function cart(): void
    {
        $items = $_SESSION['cart'] ?? [];
        $total = 0.0;
        foreach ($items as $it) { $total += $it['price'] * $it['qty']; }
        $this->render('kiosk/cart', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function incQty(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0 && isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += 1;
        }
        header('Location: ?r=kiosk/cart');
    }

    public function decQty(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0 && isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] -= 1;
            if ($_SESSION['cart'][$id]['qty'] <= 0) { unset($_SESSION['cart'][$id]); }
        }
        header('Location: ?r=kiosk/cart');
    }

    public function remove(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0 && isset($_SESSION['cart'][$id])) { unset($_SESSION['cart'][$id]); }
        header('Location: ?r=kiosk/cart');
    }

    public function checkout(): void
    {
        $items = $_SESSION['cart'] ?? [];
        if (!$items) { header('Location: ?r=kiosk/categories'); return; }
        $total = 0.0; foreach ($items as $it) { $total += $it['price'] * $it['qty']; }
        $this->render('kiosk/checkout', [
            'total' => $total,
        ]);
    }

    public function confirm(): void
    {
        if (empty($_SESSION['cart'])) { header('Location: ?r=kiosk/categories'); return; }
        $orderType = ($_POST['order_type'] ?? 'eat_in') === 'takeaway' ? 'takeaway' : 'eat_in';
        $payment = ($_POST['payment'] ?? 'counter') === 'card' ? 'card' : 'counter';

        $items = array_values($_SESSION['cart']);
        try {
            $svc = new OrderService();
            $order = $svc->createOrder($orderType, $payment, $items);
        } catch (Throwable $e) {
            http_response_code(500);
            echo 'Erreur lors de la création de la commande.';
            return;
        }
        // Clear cart after successful creation
        $_SESSION['cart'] = [];

        if ($payment === 'card') {
            // Choose between simulator and polling based on config
            $cfg = require BASE_PATH . '/app/Config/app.php';
            $provider = (string)($cfg['payment_provider'] ?? 'simulator');
            if ($provider === 'simulator') {
                header('Location: ?r=order/startTestPayment&id=' . (int)$order['id']);
                return;
            }
            // Polling screen (standalone terminal flow)
            $this->render('kiosk/waiting_payment', [
                'orderId' => (int)$order['id'],
                'orderNumber' => (int)$order['display_number'],
                'total' => (float)$order['total'],
            ]);
            return;
        }

        // Counter payment: show confirmation immediately (receipt will auto-print in background)
        $cfg = require BASE_PATH . '/app/Config/app.php';
        $this->render('kiosk/confirm', [
            'orderNumber' => (int)$order['display_number'],
            'orderType' => $order['order_type'],
            'payment' => $order['payment_method'],
            'orderId' => (int)$order['id'],
            'confirmSeconds' => (int)($cfg['confirm_return_seconds'] ?? 12),
        ]);
    }

    public function paid(): void
    {
        // After card payment recorded by admin, redirect here to show confirmation
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) { header('Location: ?r=kiosk/welcome'); return; }
        try {
            $pdo = DB::pdo();
            $stmt = $pdo->prepare("SELECT display_number, order_type, payment_method FROM orders WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $o = $stmt->fetch();
            if (!$o) { header('Location: ?r=kiosk/welcome'); return; }
            $cfg = require BASE_PATH . '/app/Config/app.php';
            $this->render('kiosk/confirm', [
                'orderNumber' => (int)$o['display_number'],
                'orderType' => (string)$o['order_type'],
                'payment' => (string)$o['payment_method'],
                'orderId' => (int)$id,
                'confirmSeconds' => (int)($cfg['confirm_return_seconds'] ?? 12),
            ]);
        } catch (Throwable $e) {
            header('Location: ?r=kiosk/welcome');
        }
    }
}
