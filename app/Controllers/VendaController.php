<?php

namespace App\Controllers;

use App\Models\Venda;
use App\Repositories\EmpresaRepository;
use App\Repositories\ProdutoRepository;
use App\Repositories\VendaRepository;
use App\Services\EmpresaService;
use App\Services\VendaService;

class VendaController
{
    private VendaService   $service;
    private EmpresaService $empresaService;

    public function __construct()
    {
        $pdo                  = getDB();
        $this->service        = new VendaService(new VendaRepository($pdo), new ProdutoRepository($pdo));
        $this->empresaService = new EmpresaService(new EmpresaRepository($pdo));
    }

    public function index(): void
    {
        requireAdmin();

        $dataInicio = $_GET['de']          ?? date('Y-m-01');
        $dataFim    = $_GET['ate']         ?? date('Y-m-d');
        $empresaId  = (int)($_GET['empresa_id'] ?? 0);

        $vendas       = $this->service->relatorio($dataInicio, $dataFim, $empresaId);
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
            'empresas'     => $this->empresaService->listarAtivas(),
            'empresaId'    => $empresaId,
        ]);
    }

    public function recibo(string $id): void
    {
        requireLogin();

        $venda = $this->service->recibo((int) $id);
        if (!$venda) {
            http_response_code(404);
            echo 'Venda não encontrada.';
            return;
        }

        require BASE_PATH . '/views/vendas/recibo.php';
    }
}
