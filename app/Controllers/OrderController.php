<?php
declare(strict_types=1);

class OrderController extends BaseController
{
    public function pollStatus(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) { echo json_encode(['ok'=>false,'error'=>'id_required']); return; }
        try {
            $pdo = DB::pdo();
            $stmt = $pdo->prepare("SELECT id, status, payment_method, order_type, display_number, display_date FROM orders WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if (!$row) { echo json_encode(['ok'=>false,'error'=>'not_found']); return; }
            echo json_encode(['ok'=>true,'data'=>$row]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['ok'=>false,'error'=>'server_error']);
        }
    }

    public function printReceipt(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) { http_response_code(400); echo 'id requis'; return; }
        $svc = new ReceiptService();
        $data = $svc->getOrderWithItems($id);
        if (!$data) { http_response_code(404); echo 'Commande introuvable'; return; }
        $order = $data['order'];
        $items = $data['items'];
        $cfg = require BASE_PATH . '/app/Config/app.php';
        $this->render('order/receipt', [
            'order' => $order,
            'items' => $items,
            'cfg' => $cfg,
        ]);
    }

    public function startTestPayment(): void
    {
        // Simple in-app card terminal simulator (dev/test only)
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) { header('Location: ?r=kiosk/welcome'); return; }
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("SELECT id, display_number, total_price, status, payment_method FROM orders WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $o = $stmt->fetch();
        if (!$o) { header('Location: ?r=kiosk/welcome'); return; }
        // Only allow for awaiting_payment and card
        if ($o['payment_method'] !== 'card') { header('Location: ?r=kiosk/welcome'); return; }
        $this->render('order/test_terminal', [
            'orderId' => (int)$o['id'],
            'orderNumber' => (int)$o['display_number'],
            'total' => (float)$o['total_price'],
        ]);
    }

    public function testPaymentApprove(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) { header('Location: ?r=kiosk/welcome'); return; }
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("UPDATE orders SET status='paid', paid_at=NOW() WHERE id=? AND payment_method='card'");
        $stmt->execute([$id]);
        // Go to confirmation screen; it will handle auto-printing in background
        header('Location: ?r=kiosk/paid&id=' . $id);
    }

    public function testPaymentDecline(): void
    {
        // Just return to checkout; order stays awaiting_payment
        header('Location: ?r=kiosk/checkout');
    }

    public function switchToCounter(): void
    {
        // Allow switching a card order to counter (useful on timeout)
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) { header('Location: ?r=kiosk/welcome'); return; }
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("UPDATE orders SET payment_method='counter' WHERE id=? AND status='awaiting_payment'");
        $stmt->execute([$id]);
        header('Location: ?r=kiosk/paid&id=' . $id);
    }
}
