<?php

class AuthController extends Controller {

    public function loginForm(): void {
        requireGuest();
        $this->view('auth.login');
    }

    public function login(): void {
        requireGuest();
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $res = ApiClient::post('/auth/login', [
            'email'    => $email,
            'password' => $password,
        ]);

        if (!empty($res['success']) && !empty($res['data']['token'])) {
            $_SESSION['token'] = $res['data']['token'];
            $_SESSION['user']  = $res['data']['user'];
            $this->redirect('dashboard');
        }

        setOld(['email' => $email]);
        $errors = $this->extractErrors($res);
        $this->view('auth.login', ['errors' => $errors]);
    }

    public function registerForm(): void {
        requireGuest();
        $this->view('auth.register');
    }

    public function register(): void {
        requireGuest();
        $data = [
            'username'  => trim($_POST['username'] ?? ''),
            'email'     => trim($_POST['email'] ?? ''),
            'password'  => $_POST['password'] ?? '',
            'full_name' => trim($_POST['full_name'] ?? ''),
            'phone'     => trim($_POST['phone'] ?? ''),
        ];

        $res = ApiClient::post('/auth/register', $data);

        if (!empty($res['success'])) {
            setFlash('success', 'Registration successful. Please login.');
            $this->redirect('auth/login');
        }

        setOld($data);
        $errors = $this->extractErrors($res);
        $this->view('auth.register', ['errors' => $errors]);
    }

    public function logout(): void {
        if (isLoggedIn()) {
            ApiClient::post('/auth/logout', [], true);
        }
        session_destroy();
        $this->redirect('auth/login');
    }

    private function extractErrors(array $res): array {
        if (!empty($res['data']) && is_array($res['data'])) {
            $msgs = [];
            foreach ($res['data'] as $field => $errs) {
                foreach ((array)$errs as $e) {
                    $msgs[] = $e;
                }
            }
            return $msgs;
        }
        return [$res['message'] ?? 'Something went wrong.'];
    }
}
