<?php

namespace App\Controllers;

use App\Repositories\CaixaRepository;
use App\Repositories\EmpresaRepository;
use App\Repositories\FormaPagamentoRepository;
use App\Repositories\ProdutoRepository;
use App\Repositories\TipoPagamentoRepository;
use App\Repositories\VendaRepository;
use App\Services\CaixaService;
use App\Services\EmpresaService;
use App\Services\FormaPagamentoService;
use App\Services\ProdutoService;
use App\Services\VendaService;

class PdvController
{
    private VendaService   $vendaService;
    private ProdutoService $produtoService;
    private EmpresaService $empresaService;
    private FormaPagamentoService $formaPagamentoService;
    private CaixaService $caixaService;

    public function __construct()
    {
        $pdo                   = getDB();
        $produtoRepo           = new ProdutoRepository($pdo);
        $this->produtoService  = new ProdutoService($produtoRepo);
        $this->vendaService    = new VendaService(new VendaRepository($pdo), $produtoRepo);
        $this->empresaService  = new EmpresaService(new EmpresaRepository($pdo));
        $tipoRepo = new TipoPagamentoRepository($pdo);
        $this->formaPagamentoService = new FormaPagamentoService(new FormaPagamentoRepository($pdo), $tipoRepo);
        $this->caixaService = new CaixaService(new CaixaRepository($pdo));
    }

    public function index(): void
    {
        requireLogin();
        $caixa = $this->caixaService->caixaAberto(auth()['id']);

        render('pdv/index', [
            'pageTitle' => 'PDV — Ponto de Venda',
            'produtos'  => $this->produtoService->listarParaPdv(),
            'empresas'  => $this->empresaService->listarAtivas(),
            'tiposPagamento' => $this->formaPagamentoService->listarAtivas(),
            'caixa' => $caixa,
            'caixaResumo' => $caixa ? $this->caixaService->resumo($caixa) : null,
            'caixaOperacoes' => $caixa ? $this->caixaService->operacoes($caixa) : [],
            'caixaFlash' => flash('caixa'),
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

        $caixa = $this->caixaService->caixaAberto(auth()['id']);
        if (!$caixa) {
            jsonResponse(['ok' => false, 'erro' => 'Abra o caixa antes de finalizar vendas.'], 422);
        }

        $forma         = $data['forma_pagamento'] ?? 'dinheiro';
        $formasValidas = array_map(
            fn ($formaPagamento) => $formaPagamento->codigo,
            $this->formaPagamentoService->listarAtivas()
        );

        if (!in_array($forma, $formasValidas, true)) {
            jsonResponse(['ok' => false, 'erro' => 'Forma de pagamento inválida.'], 400);
        }

        foreach (($data['pagamentos'] ?? []) as $pagamento) {
            $formaPagamento = (string) ($pagamento['forma_pagamento'] ?? '');
            if ($formaPagamento !== '' && !in_array($formaPagamento, $formasValidas, true)) {
                jsonResponse(['ok' => false, 'erro' => 'Forma de pagamento invalida.'], 400);
            }
        }

        $result = $this->vendaService->processar(
            itensInput:     $data['itens'],
            desconto:       (float)($data['desconto']    ?? 0),
            formaPagamento: $forma,
            valorPago:      (float)($data['valor_pago']  ?? 0),
            usuarioId:      auth()['id'],
            empresaId:      (int)($data['empresa_id']    ?? 0),
            caixaId:        $caixa->id,
            pagamentosInput: $data['pagamentos'] ?? [],
        );

        jsonResponse($result, $result['ok'] ? 200 : 422);
    }
    public function abrirCaixa(): void
    {
        requireLogin();
        $result = $this->caixaService->abrir(auth()['id'], $_POST);
        redirect('/pdv', 'caixa', $result['ok'] ? 'Caixa aberto com sucesso.' : implode(' ', $result['errors']));
    }

    public function sangria(): void
    {
        requireLogin();
        $result = $this->caixaService->operacao(auth()['id'], 'sangria', $_POST);
        redirect('/pdv', 'caixa', $result['ok'] ? 'Sangria registrada com sucesso.' : implode(' ', $result['errors']));
    }

    public function reforco(): void
    {
        requireLogin();
        $result = $this->caixaService->operacao(auth()['id'], 'reforco', $_POST);
        redirect('/pdv', 'caixa', $result['ok'] ? 'Reforço registrado com sucesso.' : implode(' ', $result['errors']));
    }

    public function fecharCaixa(): void
    {
        requireLogin();
        $result = $this->caixaService->fechar(auth()['id'], $_POST);
        redirect('/pdv', 'caixa', $result['ok'] ? 'Caixa fechado com sucesso.' : implode(' ', $result['errors']));
    }
}
