<?php

namespace App\Controllers;

use App\Models\Usuario;
use App\Repositories\PerfilUsuarioRepository;
use App\Repositories\UsuarioRepository;
use App\Services\AuthService;
use App\Services\PerfilUsuarioService;
use App\Services\UsuarioService;

class UsuarioController
{
    private UsuarioService $service;
    private PerfilUsuarioService $perfilService;

    public function __construct()
    {
        $pdo = getDB();
        $repo = new UsuarioRepository($pdo);
        $perfilRepo = new PerfilUsuarioRepository($pdo);

        $this->service = new UsuarioService($repo, new AuthService($repo), $perfilRepo);
        $this->perfilService = new PerfilUsuarioService($perfilRepo);
    }

    public function index(): void
    {
        requireAdmin();
        render('usuarios/lista', [
            'pageTitle' => 'Usuarios',
            'usuarios' => $this->service->listar(),
            'perfis' => $this->perfilService->listar(),
            'flash' => flash('usuario'),
        ]);
    }

    public function create(): void
    {
        requireAdmin();
        render('usuarios/form', [
            'pageTitle' => 'Novo Usuario',
            'usuario' => new Usuario(),
            'perfis' => $this->perfilService->listarAtivos(),
            'errors' => [],
        ]);
    }

    public function store(): void
    {
        requireAdmin();

        $result = $this->service->salvar($_POST);

        if (!$result['ok']) {
            $u = new Usuario();
            $u->nome = trim($_POST['nome'] ?? '');
            $u->email = trim($_POST['email'] ?? '');
            $u->perfil = trim($_POST['perfil'] ?? 'usuario');
            $u->ativo = isset($_POST['ativo']);

            render('usuarios/form', [
                'pageTitle' => 'Novo Usuario',
                'usuario' => $u,
                'perfis' => $this->perfilService->listarAtivos(),
                'errors' => $result['errors'],
            ]);
            return;
        }

        redirect('/usuarios', 'usuario', 'Usuario cadastrado com sucesso.');
    }

    public function edit(string $id): void
    {
        requireAdmin();

        $usuario = $this->service->buscarPorId((int) $id);
        if (!$usuario) {
            redirect('/usuarios');
        }

        render('usuarios/form', [
            'pageTitle' => 'Editar Usuario',
            'usuario' => $usuario,
            'perfis' => $this->perfilService->listar(),
            'errors' => [],
        ]);
    }

    public function update(string $id): void
    {
        requireAdmin();

        $result = $this->service->salvar($_POST, (int) $id);

        if (!$result['ok']) {
            $usuario = $this->service->buscarPorId((int) $id) ?? new Usuario();
            $usuario->nome = trim($_POST['nome'] ?? '');
            $usuario->email = trim($_POST['email'] ?? '');
            $usuario->perfil = trim($_POST['perfil'] ?? 'usuario');
            $usuario->ativo = isset($_POST['ativo']);

            render('usuarios/form', [
                'pageTitle' => 'Editar Usuario',
                'usuario' => $usuario,
                'perfis' => $this->perfilService->listar(),
                'errors' => $result['errors'],
            ]);
            return;
        }

        redirect('/usuarios', 'usuario', 'Usuario atualizado com sucesso.');
    }

    public function toggle(string $id): void
    {
        requireAdmin();

        if ((int) $id === auth()['id']) {
            redirect('/usuarios', 'usuario', 'Voce nao pode desativar seu proprio usuario.');
        }

        $this->service->toggle((int) $id);
        redirect('/usuarios', 'usuario', 'Status do usuario alterado.');
    }
}
