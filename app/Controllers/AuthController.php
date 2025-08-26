<?php
declare(strict_types=1);

class AuthController extends BaseController
{
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim((string)($_POST['email'] ?? ''));
            $pass = (string)($_POST['password'] ?? '');
            $pdo = DB::pdo();
            $stmt = $pdo->prepare("SELECT id, email, password_hash, is_active FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            $ok = false;
            if ($user && (int)$user['is_active'] === 1) {
                $hash = (string)$user['password_hash'];
                // MVP: allow default seed to use password 'admin123' when placeholder hash present
                if ($hash === '$2y$10$CHANGE_ME_HASH' && $pass === 'admin123') {
                    $ok = true;
                } elseif (password_verify($pass, $hash)) {
                    $ok = true;
                }
            }
            if ($ok) {
                $_SESSION['admin'] = ['id' => (int)$user['id'], 'email' => (string)$user['email']];
                header('Location: ?r=dashboard/orders');
                return;
            }
            $this->render('admin/login', ['error' => 'Identifiants invalides']);
            return;
        }
        $this->render('admin/login');
    }

    public function logout(): void
    {
        unset($_SESSION['admin']);
        header('Location: ?r=auth/login');
    }
}
