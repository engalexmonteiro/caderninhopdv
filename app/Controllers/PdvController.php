<?php

namespace App\Controllers;

use App\Repositories\ProdutoRepository;
use App\Repositories\VendaRepository;
use App\Services\ProdutoService;
use App\Services\VendaService;

class PdvController
{
    private VendaService   $vendaService;
    private ProdutoService $produtoService;

    public function __construct()
    {
        $pdo                  = getDB();
        $produtoRepo          = new ProdutoRepository($pdo);
        $this->produtoService = new ProdutoService($produtoRepo);
        $this->vendaService   = new VendaService(new VendaRepository($pdo), $produtoRepo);
    }

    public function index(): void
    {
        requireLogin();

        render('pdv/index', [
            'pageTitle' => 'PDV — Ponto de Venda',
            'produtos'  => $this->produtoService->listarParaPdv(),
        ]);
    }

    public function finalizar(): void
    {
        requireLogin();

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data || empty($data['itens'])) {
            jsonResponse(['ok' => false, 'erro' => 'Dados inválidos.'], 400);
        }

        $formasValidas = ['dinheiro', 'cartao_credito', 'cartao_debito', 'pix'];
        $forma         = $data['forma_pagamento'] ?? 'dinheiro';

        if (!in_array($forma, $formasValidas, true)) {
            jsonResponse(['ok' => false, 'erro' => 'Forma de pagamento inválida.'], 400);
        }

        $result = $this->vendaService->processar(
            itensInput:     $data['itens'],
            desconto:       (float)($data['desconto']    ?? 0),
            formaPagamento: $forma,
            valorPago:      (float)($data['valor_pago']  ?? 0),
            usuarioId:      auth()['id'],
        );

        jsonResponse($result, $result['ok'] ? 200 : 422);
    }
}
