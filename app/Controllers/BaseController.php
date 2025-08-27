<?php
declare(strict_types=1);

class BaseController
{
    protected function baseUrl(string $path = ''): string
    {
        // Remove any leading slashes from the path
        $path = ltrim($path, '/');
        // Get the base URL from the current request
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        // Remove any trailing slashes
        $basePath = rtrim($basePath, '/');
        // Combine the base URL with the requested path
        return $basePath . ($path ? "/$path" : '');
    }

    protected function render(string $view, array $params = []): void
    {
        $viewFile = dirname(__DIR__) . '/Views/' . ltrim($view, '/') . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'Vue introuvable: ' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8');
            return;
        }
        
        // Make baseUrl available to all views
        $baseUrl = function(string $path = '') {
            return $this->baseUrl($path);
        };
        
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
