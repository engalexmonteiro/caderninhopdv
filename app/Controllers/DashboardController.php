<?php

namespace App\Controllers;

use App\Repositories\ProdutoRepository;
use App\Repositories\UsuarioRepository;
use App\Repositories\VendaRepository;
use App\Services\VendaService;

class DashboardController
{
    private VendaService $vendaService;
    private ProdutoRepository $produtos;
    private UsuarioRepository $usuarios;

    public function __construct()
    {
        $pdo                = getDB();
        $this->produtos     = new ProdutoRepository($pdo);
        $this->usuarios     = new UsuarioRepository($pdo);
        $this->vendaService = new VendaService(new VendaRepository($pdo), $this->produtos);
    }

    public function index(): void
    {
        requireLogin();

        $dados = $this->vendaService->dashboard();

        render('dashboard/index', [
            'pageTitle'      => 'Dashboard',
            'totalHoje'      => $dados['total_hoje'],
            'countHoje'      => $dados['count_hoje'],
            'vendasRecentes' => $dados['recentes'],
            'totalProdutos'  => $this->produtos->countAtivos(),
            'totalUsuarios'  => $this->usuarios->countAtivos(),
            'erro'           => $_GET['erro'] ?? '',
        ]);
    }
}
