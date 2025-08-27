<?php
declare(strict_types=1);

class ReceiptService
{
    public function getOrderWithItems(int $orderId): ?array
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        if (!$order) { return null; }
        $stmt2 = $pdo->prepare("SELECT product_name, quantity, price_each, line_total FROM order_items WHERE order_id = ? ORDER BY id");
        $stmt2->execute([$orderId]);
        $items = $stmt2->fetchAll();
        return [ 'order' => $order, 'items' => $items ];
    }
}
