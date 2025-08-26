<?php
declare(strict_types=1);

// Moroccan Café Kiosk – MVP front controller
// Routing style: ?r=controller/action

define('BASE_PATH', dirname(__DIR__));

// Load app config
$cfg = require BASE_PATH . '/app/Config/app.php';

// Timezone from config
if (!empty($cfg['timezone'])) {
    date_default_timezone_set((string)$cfg['timezone']);
}

// Error handling based on environment
$env = $cfg['env'] ?? 'dev';
if ($env === 'prod') {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
} else {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// Sessions for kiosk cart/admin auth
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simple autoloader (no namespaces)
spl_autoload_register(function (string $class): void {
    $paths = [
        BASE_PATH . '/app/Controllers/' . $class . '.php',
        BASE_PATH . '/app/Services/' . $class . '.php',
        BASE_PATH . '/app/Models/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});

$r = isset($_GET['r']) ? trim((string)$_GET['r']) : '';
if ($r === '') {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><html lang="fr"><meta charset="utf-8"><title>Kiosk</title><body style="font-family:sans-serif;padding:24px"><h1>Moroccan Café Kiosk – MVP</h1><p>Router en ligne. Utilisez ?r=controller/action</p></body></html>';
    exit;
}

[$controllerPart, $action] = array_pad(explode('/', $r, 2), 2, null);
$controllerName = $controllerPart ? ucfirst($controllerPart) . 'Controller' : 'KioskController';
$action = $action ?: 'welcome';

if (!class_exists($controllerName)) {
    http_response_code(404);
    echo 'Controller introuvable: ' . htmlspecialchars($controllerName, ENT_QUOTES, 'UTF-8');
    exit;
}

$controller = new $controllerName();
if (!method_exists($controller, $action)) {
    http_response_code(404);
    echo 'Action introuvable: ' . htmlspecialchars($action, ENT_QUOTES, 'UTF-8');
    exit;
}

$controller->$action();
