<?php

namespace App\Controllers;

use App\Models\PerfilUsuario;
use App\Repositories\PerfilUsuarioRepository;
use App\Services\PerfilUsuarioService;

class PerfilUsuarioController
{
    private PerfilUsuarioService $service;

    public function __construct()
    {
        $this->service = new PerfilUsuarioService(new PerfilUsuarioRepository(getDB()));
    }

    public function index(): void
    {
        requireAdmin();
        render('perfis_usuario/lista', [
            'pageTitle' => 'Perfis de Usuario',
            'perfis' => $this->service->listar(),
            'flash' => flash('perfil_usuario'),
        ]);
    }

    public function create(): void
    {
        requireAdmin();
        render('perfis_usuario/form', [
            'pageTitle' => 'Novo Perfil de Usuario',
            'perfil' => new PerfilUsuario(),
            'errors' => [],
        ]);
    }

    public function store(): void
    {
        requireAdmin();
        $result = $this->service->salvar($_POST);

        if (!$result['ok']) {
            render('perfis_usuario/form', [
                'pageTitle' => 'Novo Perfil de Usuario',
                'perfil' => $result['perfil'],
                'errors' => $result['errors'],
            ]);
            return;
        }

        redirect('/usuarios/perfis', 'perfil_usuario', 'Perfil cadastrado com sucesso.');
    }

    public function edit(string $id): void
    {
        requireAdmin();
        $perfil = $this->service->buscarPorId((int) $id);
        if (!$perfil) {
            redirect('/usuarios/perfis');
        }

        render('perfis_usuario/form', [
            'pageTitle' => 'Editar Perfil de Usuario',
            'perfil' => $perfil,
            'errors' => [],
        ]);
    }

    public function update(string $id): void
    {
        requireAdmin();
        $result = $this->service->salvar($_POST, (int) $id);

        if (!$result['ok']) {
            render('perfis_usuario/form', [
                'pageTitle' => 'Editar Perfil de Usuario',
                'perfil' => $result['perfil'],
                'errors' => $result['errors'],
            ]);
            return;
        }

        redirect('/usuarios/perfis', 'perfil_usuario', 'Perfil atualizado com sucesso.');
    }

    public function toggle(string $id): void
    {
        requireAdmin();
        $this->service->toggle((int) $id);
        redirect('/usuarios/perfis', 'perfil_usuario', 'Status do perfil alterado.');
    }
}
