<?php
declare(strict_types=1);

class BaseController
{
    protected function render(string $view, array $params = []): void
    {
        $viewFile = dirname(__DIR__) . '/Views/' . ltrim($view, '/') . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'Vue introuvable: ' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8');
            return;
        }
        extract($params, EXTR_SKIP);
        include $viewFile;
    }

    protected function isAdmin(): bool
    {
        return !empty($_SESSION['admin']) && is_array($_SESSION['admin']);
    }

    protected function requireAdmin(): void
    {
        if (!$this->isAdmin()) {
            header('Location: ?r=auth/login');
            exit;
        }
    }
}
