<?php
declare(strict_types=1);

class MenuService
{
    public function getActiveCategories(): array
    {
        $pdo = DB::pdo();
        $sql = "SELECT id, name, image_url FROM categories WHERE is_active = 1 ORDER BY name";
        return $pdo->query($sql)->fetchAll();
    }

    public function getProductsByCategory(int $categoryId): array
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("SELECT id, name, base_price, image_url FROM products WHERE category_id = ? AND is_available = 1 ORDER BY name");
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    public function getProduct(int $productId): ?array
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("SELECT id, category_id, name, description, base_price, image_url FROM products WHERE id = ? AND is_available = 1");
        $stmt->execute([$productId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
