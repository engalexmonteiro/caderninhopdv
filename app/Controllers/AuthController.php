<?php

namespace App\Controllers;

use App\Repositories\UsuarioRepository;
use App\Services\AuthService;

class AuthController
{
    private AuthService $service;

    public function __construct()
    {
        $this->service = new AuthService(new UsuarioRepository(getDB()));
    }

    public function showLogin(): void
    {
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
        render('auth/login', ['pageTitle' => 'Login', 'erro' => '']);
    }

    public function login(): void
    {
        if (isLoggedIn()) {
            redirect('/dashboard');
        }

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha']      ?? '';

        if ($email === '' || $senha === '') {
            render('auth/login', ['pageTitle' => 'Login', 'erro' => 'Preencha e-mail e senha.']);
            return;
        }

        $user = $this->service->login($email, $senha);

        if ($user === null) {
            render('auth/login', ['pageTitle' => 'Login', 'erro' => 'E-mail ou senha inválidos.']);
            return;
        }

        session_regenerate_id(true);
        $_SESSION['user_id']     = $user['id'];
        $_SESSION['user_nome']   = $user['nome'];
        $_SESSION['user_perfil'] = $user['perfil'];

        redirect('/dashboard');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        redirect('/login');
    }
}
