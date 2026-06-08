<?php

namespace App\Controllers;

use App\Models\Venda;
use App\Repositories\ProdutoRepository;
use App\Repositories\VendaRepository;
use App\Services\VendaService;

class VendaController
{
    private VendaService $service;

    public function __construct()
    {
        $pdo           = getDB();
        $this->service = new VendaService(new VendaRepository($pdo), new ProdutoRepository($pdo));
    }

    public function index(): void
    {
        requireAdmin();

        $dataInicio = $_GET['de']  ?? date('Y-m-01');
        $dataFim    = $_GET['ate'] ?? date('Y-m-d');

        $vendas      = $this->service->relatorio($dataInicio, $dataFim);
        $totalPeriodo = array_sum(array_map(
            fn(Venda $v) => $v->status === 'concluida' ? $v->total : 0,
            $vendas
        ));

        render('vendas/lista', [
            'pageTitle'    => 'Vendas',
            'vendas'       => $vendas,
            'dataInicio'   => $dataInicio,
            'dataFim'      => $dataFim,
            'totalPeriodo' => $totalPeriodo,
        ]);
    }
}
