<?php

namespace App\Controllers;

use App\Models\TipoPagamento;
use App\Repositories\TipoPagamentoRepository;
use App\Services\TipoPagamentoService;

class TipoPagamentoController
{
    private TipoPagamentoService $service;

    public function __construct()
    {
        $this->service = new TipoPagamentoService(new TipoPagamentoRepository(getDB()));
    }

    public function index(): void
    {
        requireAdmin();

        render('tipos_pagamento/lista', [
            'pageTitle' => 'Tipos de Pagamento',
            'tipos' => $this->service->listar(),
            'flash' => flash('tipo_pagamento'),
        ]);
    }

    public function create(): void
    {
        requireAdmin();

        render('tipos_pagamento/form', [
            'pageTitle' => 'Novo Tipo de Pagamento',
            'tipo' => new TipoPagamento(),
            'errors' => [],
        ]);
    }

    public function store(): void
    {
        requireAdmin();

        $result = $this->service->salvar($_POST);
        if (!$result['ok']) {
            render('tipos_pagamento/form', [
                'pageTitle' => 'Novo Tipo de Pagamento',
                'tipo' => $result['tipo'],
                'errors' => $result['errors'],
            ]);
            return;
        }

        redirect('/financeiro/tipos-pagamento', 'tipo_pagamento', 'Tipo de pagamento cadastrado com sucesso.');
    }

    public function edit(string $id): void
    {
        requireAdmin();

        $tipo = $this->service->buscarPorId((int) $id);
        if (!$tipo) {
            redirect('/financeiro/tipos-pagamento');
        }

        render('tipos_pagamento/form', [
            'pageTitle' => 'Editar Tipo de Pagamento',
            'tipo' => $tipo,
            'errors' => [],
        ]);
    }

    public function update(string $id): void
    {
        requireAdmin();

        $result = $this->service->salvar($_POST, (int) $id);
        if (!$result['ok']) {
            render('tipos_pagamento/form', [
                'pageTitle' => 'Editar Tipo de Pagamento',
                'tipo' => $result['tipo'],
                'errors' => $result['errors'],
            ]);
            return;
        }

        redirect('/financeiro/tipos-pagamento', 'tipo_pagamento', 'Tipo de pagamento atualizado com sucesso.');
    }

    public function toggle(string $id): void
    {
        requireAdmin();
        $this->service->toggle((int) $id);
        redirect('/financeiro/tipos-pagamento', 'tipo_pagamento', 'Status do tipo de pagamento alterado.');
    }
}
