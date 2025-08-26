<?php
declare(strict_types=1);

class OrderService
{
    public function createOrder(string $orderType, string $paymentMethod, array $items): array
    {
        // Validate inputs to enum values
        $orderType = $orderType === 'takeaway' ? 'takeaway' : 'eat_in';
        $paymentMethod = $paymentMethod === 'card' ? 'card' : 'counter';
        if (empty($items)) {
            throw new InvalidArgumentException('No items');
        }

        $pdo = DB::pdo();
        $pdo->beginTransaction();
        try {
            $displayDate = (new DateTime('now', new DateTimeZone('Africa/Casablanca')))->format('Y-m-d');

            // Lock the set for today to allocate next display_number
            $stmt = $pdo->prepare("SELECT COALESCE(MAX(display_number), 0) AS max_num FROM orders WHERE display_date = ? FOR UPDATE");
            $stmt->execute([$displayDate]);
            $row = $stmt->fetch();
            $nextNumber = ((int)($row['max_num'] ?? 0)) + 1;

            // Compute total
            $total = 0.0;
            foreach ($items as $it) {
                $total += (float)$it['price'] * (int)$it['qty'];
            }

            // Insert order
            $stmt = $pdo->prepare("INSERT INTO orders (display_number, display_date, status, payment_method, order_type, total_price) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$nextNumber, $displayDate, 'awaiting_payment', $paymentMethod, $orderType, $total]);
            $orderId = (int)$pdo->lastInsertId();

            // Insert items (snapshotting name/price)
            $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price_each, line_total, options_json) VALUES (?,?,?,?,?,?,?)");
            foreach ($items as $it) {
                $lineTotal = (float)$it['price'] * (int)$it['qty'];
                $stmtItem->execute([
                    $orderId,
                    (int)$it['id'],
                    (string)$it['name'],
                    (int)$it['qty'],
                    (float)$it['price'],
                    $lineTotal,
                    null,
                ]);
            }

            $pdo->commit();
            return [
                'id' => $orderId,
                'display_number' => $nextNumber,
                'display_date' => $displayDate,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'order_type' => $orderType,
            ];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            throw $e;
        }
    }
}
