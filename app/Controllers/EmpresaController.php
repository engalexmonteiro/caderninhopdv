<?php

namespace App\Controllers;

use App\Models\Empresa;
use App\Repositories\EmpresaRepository;
use App\Services\EmpresaService;

class EmpresaController
{
    private EmpresaService $service;

    public function __construct()
    {
        $this->service = new EmpresaService(new EmpresaRepository(getDB()));
    }

    public function index(): void
    {
        requireAdmin();
        render('empresas/lista', [
            'pageTitle' => 'Empresas / CNPJs',
            'empresas'  => $this->service->listar(),
            'flash'     => flash('empresa'),
        ]);
    }

    public function create(): void
    {
        requireAdmin();
        render('empresas/form', [
            'pageTitle' => 'Nova Empresa',
            'empresa'   => new Empresa(),
            'errors'    => [],
        ]);
    }

    public function store(): void
    {
        requireAdmin();
        $result = $this->service->salvar($_POST, $_FILES, 0);
        if (!$result['ok']) {
            render('empresas/form', [
                'pageTitle' => 'Nova Empresa',
                'empresa'   => $this->buildFromPost(),
                'errors'    => $result['errors'],
            ]);
            return;
        }
        redirect('/empresas', 'empresa', 'Empresa cadastrada com sucesso.');
    }

    public function edit(string $id): void
    {
        requireAdmin();
        $empresa = $this->service->buscarPorId((int) $id);
        if (!$empresa) {
            redirect('/empresas');
        }
        render('empresas/form', [
            'pageTitle' => 'Editar Empresa',
            'empresa'   => $empresa,
            'errors'    => [],
        ]);
    }

    public function update(string $id): void
    {
        requireAdmin();
        $result = $this->service->salvar($_POST, $_FILES, (int) $id);
        if (!$result['ok']) {
            $empresa = $this->service->buscarPorId((int) $id) ?? new Empresa();
            $posted  = $this->buildFromPost();
            $posted->id        = $empresa->id;
            $posted->logomarca = $empresa->logomarca;
            render('empresas/form', [
                'pageTitle' => 'Editar Empresa',
                'empresa'   => $posted,
                'errors'    => $result['errors'],
            ]);
            return;
        }
        redirect('/empresas', 'empresa', 'Empresa atualizada com sucesso.');
    }

    public function toggle(string $id): void
    {
        requireAdmin();
        $this->service->toggle((int) $id);
        redirect('/empresas', 'empresa', 'Status da empresa alterado.');
    }

    private function buildFromPost(): Empresa
    {
        $e               = new Empresa();
        $e->razaoSocial  = trim($_POST['razao_social']  ?? '');
        $e->nomeFantasia = trim($_POST['nome_fantasia'] ?? '');
        $e->cnpj         = trim($_POST['cnpj']         ?? '');
        $e->email        = trim($_POST['email']        ?? '');
        $e->telefone     = trim($_POST['telefone']     ?? '');
        $e->endereco     = trim($_POST['endereco']     ?? '');
        $e->cidade       = trim($_POST['cidade']       ?? '');
        $e->estado       = trim($_POST['estado']       ?? '');
        $e->cep          = trim($_POST['cep']          ?? '');
        $e->ativo        = isset($_POST['ativo']);
        return $e;
    }
}
