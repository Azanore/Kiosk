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
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        if (!$order) { http_response_code(404); echo 'Commande introuvable'; return; }
        $stmt2 = $pdo->prepare("SELECT product_name, quantity, price_each, line_total FROM order_items WHERE order_id = ? ORDER BY id");
        $stmt2->execute([$id]);
        $items = $stmt2->fetchAll();
        $cfg = require BASE_PATH . '/app/Config/app.php';
        $this->render('order/receipt', [
            'order' => $order,
            'items' => $items,
            'cfg' => $cfg,
        ]);
    }
}
