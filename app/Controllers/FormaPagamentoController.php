<?php

namespace App\Controllers;

use App\Models\FormaPagamento;
use App\Repositories\FormaPagamentoRepository;
use App\Repositories\TipoPagamentoRepository;
use App\Services\FormaPagamentoService;
use App\Services\TipoPagamentoService;

class FormaPagamentoController
{
    private FormaPagamentoService $service;
    private TipoPagamentoService $tipoService;

    public function __construct()
    {
        $pdo = getDB();
        $tipoRepo = new TipoPagamentoRepository($pdo);
        $this->service = new FormaPagamentoService(new FormaPagamentoRepository($pdo), $tipoRepo);
        $this->tipoService = new TipoPagamentoService($tipoRepo);
    }

    public function index(): void
    {
        requireAdmin();
        render('formas_pagamento/lista', [
            'pageTitle' => 'Formas de Pagamento',
            'formas' => $this->service->listar(),
            'flash' => flash('forma_pagamento'),
        ]);
    }

    public function create(): void
    {
        requireAdmin();
        render('formas_pagamento/form', [
            'pageTitle' => 'Nova Forma de Pagamento',
            'forma' => new FormaPagamento(),
            'tipos' => $this->tipoService->listar(),
            'errors' => [],
        ]);
    }

    public function store(): void
    {
        requireAdmin();
        $result = isset($_POST['gerar_parcelas'])
            ? $this->service->gerarParcelas($_POST)
            : $this->service->salvar($_POST);

        if (!$result['ok']) {
            render('formas_pagamento/form', [
                'pageTitle' => 'Nova Forma de Pagamento',
                'forma' => $result['forma'],
                'tipos' => $this->tipoService->listar(),
                'errors' => $result['errors'],
            ]);
            return;
        }

        redirect('/financeiro/formas-pagamento', 'forma_pagamento', 'Forma de pagamento cadastrada com sucesso.');
    }

    public function edit(string $id): void
    {
        requireAdmin();
        $forma = $this->service->buscarPorId((int) $id);
        if (!$forma) {
            redirect('/financeiro/formas-pagamento');
        }

        render('formas_pagamento/form', [
            'pageTitle' => 'Editar Forma de Pagamento',
            'forma' => $forma,
            'tipos' => $this->tipoService->listar(),
            'errors' => [],
        ]);
    }

    public function update(string $id): void
    {
        requireAdmin();
        $result = $this->service->salvar($_POST, (int) $id);

        if (!$result['ok']) {
            render('formas_pagamento/form', [
                'pageTitle' => 'Editar Forma de Pagamento',
                'forma' => $result['forma'],
                'tipos' => $this->tipoService->listar(),
                'errors' => $result['errors'],
            ]);
            return;
        }

        redirect('/financeiro/formas-pagamento', 'forma_pagamento', 'Forma de pagamento atualizada com sucesso.');
    }

    public function toggle(string $id): void
    {
        requireAdmin();
        $this->service->toggle((int) $id);
        redirect('/financeiro/formas-pagamento', 'forma_pagamento', 'Status da forma de pagamento alterado.');
    }
}
