<?php
declare(strict_types=1);

class AuthController extends BaseController
{
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim((string)($_POST['email'] ?? ''));
            $pass = (string)($_POST['password'] ?? '');
            $auth = new AuthService();
            $user = $auth->verifyLogin($email, $pass);
            if ($user) {
                $_SESSION['admin'] = $user;
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
