<?php
declare(strict_types=1);

class DisplayController extends BaseController
{
    // Customer-facing collection screen
    public function collection(): void
    {
        $this->render('display/collection', []);
    }

    // JSON data for polling: preparing and ready numbers for today
    public function collectionData(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $pdo = DB::pdo();
            $stmt = $pdo->prepare("SELECT display_number, status FROM orders WHERE display_date = CURRENT_DATE() AND status IN ('preparing','ready') ORDER BY display_number ASC");
            $stmt->execute();
            $rows = $stmt->fetchAll();
            $preparing = [];
            $ready = [];
            foreach ($rows as $r) {
                if ($r['status'] === 'preparing') { $preparing[] = (int)$r['display_number']; }
                if ($r['status'] === 'ready') { $ready[] = (int)$r['display_number']; }
            }
            echo json_encode(['ok'=>true,'preparing'=>$preparing,'ready'=>$ready]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['ok'=>false]);
        }
    }
}
