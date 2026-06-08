<?php

namespace App\Controllers;

use App\Repositories\EmpresaRepository;
use App\Repositories\ProdutoRepository;
use App\Repositories\UsuarioRepository;
use App\Repositories\VendaRepository;
use App\Services\EmpresaService;
use App\Services\VendaService;

class DashboardController
{
    private VendaService    $vendaService;
    private EmpresaService  $empresaService;
    private ProdutoRepository $produtos;
    private UsuarioRepository $usuarios;

    public function __construct()
    {
        $pdo                   = getDB();
        $this->produtos        = new ProdutoRepository($pdo);
        $this->usuarios        = new UsuarioRepository($pdo);
        $this->vendaService    = new VendaService(new VendaRepository($pdo), $this->produtos);
        $this->empresaService  = new EmpresaService(new EmpresaRepository($pdo));
    }

    public function index(): void
    {
        requireLogin();

        $empresaId = (int)($_GET['empresa_id'] ?? 0);
        $dados     = $this->vendaService->dashboard($empresaId);
        $empresas  = $this->empresaService->listarAtivas();

        render('dashboard/index', [
            'pageTitle'      => 'Dashboard',
            'totalHoje'      => $dados['total_hoje'],
            'countHoje'      => $dados['count_hoje'],
            'vendasRecentes' => $dados['recentes'],
            'totalProdutos'  => $this->produtos->countAtivos(),
            'totalUsuarios'  => $this->usuarios->countAtivos(),
            'erro'           => $_GET['erro'] ?? '',
            'empresas'       => $empresas,
            'empresaId'      => $empresaId,
        ]);
    }
}
