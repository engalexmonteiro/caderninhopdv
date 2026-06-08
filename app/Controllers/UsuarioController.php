<?php

namespace App\Controllers;

use App\Models\Usuario;
use App\Repositories\UsuarioRepository;
use App\Services\AuthService;
use App\Services\UsuarioService;

class UsuarioController
{
    private UsuarioService $service;

    public function __construct()
    {
        $repo          = new UsuarioRepository(getDB());
        $this->service = new UsuarioService($repo, new AuthService($repo));
    }

    public function index(): void
    {
        requireAdmin();
        render('usuarios/lista', [
            'pageTitle' => 'Usuários',
            'usuarios'  => $this->service->listar(),
            'flash'     => flash('usuario'),
        ]);
    }

    public function create(): void
    {
        requireAdmin();
        render('usuarios/form', [
            'pageTitle' => 'Novo Usuário',
            'usuario'   => new Usuario(),
            'errors'    => [],
        ]);
    }

    public function store(): void
    {
        requireAdmin();

        $result = $this->service->salvar($_POST);

        if (!$result['ok']) {
            $u         = new Usuario();
            $u->nome   = trim($_POST['nome']   ?? '');
            $u->email  = trim($_POST['email']  ?? '');
            $u->perfil = $_POST['perfil'] === 'admin' ? 'admin' : 'usuario';
            $u->ativo  = isset($_POST['ativo']);

            render('usuarios/form', [
                'pageTitle' => 'Novo Usuário',
                'usuario'   => $u,
                'errors'    => $result['errors'],
            ]);
            return;
        }

        redirect('/usuarios', 'usuario', 'Usuário cadastrado com sucesso.');
    }

    public function edit(string $id): void
    {
        requireAdmin();

        $usuario = $this->service->buscarPorId((int) $id);
        if (!$usuario) {
            redirect('/usuarios');
        }

        render('usuarios/form', [
            'pageTitle' => 'Editar Usuário',
            'usuario'   => $usuario,
            'errors'    => [],
        ]);
    }

    public function update(string $id): void
    {
        requireAdmin();

        $result = $this->service->salvar($_POST, (int) $id);

        if (!$result['ok']) {
            $usuario         = $this->service->buscarPorId((int) $id) ?? new Usuario();
            $usuario->nome   = trim($_POST['nome']   ?? '');
            $usuario->email  = trim($_POST['email']  ?? '');
            $usuario->perfil = $_POST['perfil'] === 'admin' ? 'admin' : 'usuario';
            $usuario->ativo  = isset($_POST['ativo']);

            render('usuarios/form', [
                'pageTitle' => 'Editar Usuário',
                'usuario'   => $usuario,
                'errors'    => $result['errors'],
            ]);
            return;
        }

        redirect('/usuarios', 'usuario', 'Usuário atualizado com sucesso.');
    }

    public function toggle(string $id): void
    {
        requireAdmin();

        // Impede desativar o próprio usuário
        if ((int) $id === auth()['id']) {
            redirect('/usuarios', 'usuario', 'Você não pode desativar seu próprio usuário.');
        }

        $this->service->toggle((int) $id);
        redirect('/usuarios', 'usuario', 'Status do usuário alterado.');
    }
}
