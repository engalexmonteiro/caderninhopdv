<?php

namespace App\Services;

use App\Models\Venda;
use App\Models\VendaItem;
use App\Models\VendaPagamento;
use App\Repositories\ProdutoRepository;
use App\Repositories\VendaRepository;

class VendaService
{
    public function __construct(
        private VendaRepository   $vendas,
        private ProdutoRepository $produtos,
    ) {}

    /**
     * Processa e persiste uma venda.
     *
     * @param  array<array{id:int, qty:int, preco:float}> $itensInput
     * @return array{ok: bool, venda_id: int, erro: string}
     */
    public function processar(
        array  $itensInput,
        float  $desconto,
        string $formaPagamento,
        float  $valorPago,
        int    $usuarioId,
        int    $empresaId = 0,
        int    $caixaId = 0,
        array  $pagamentosInput = []
    ): array {
        if (empty($itensInput)) {
            return ['ok' => false, 'venda_id' => 0, 'erro' => 'Carrinho vazio.'];
        }

        $ids      = array_column($itensInput, 'id');
        $produtos = $this->produtos->findByIds($ids);

        $itens    = [];
        $subtotal = 0.0;

        foreach ($itensInput as $input) {
            $id    = (int)   $input['id'];
            $qty   = (int)   $input['qty'];
            $preco = (float) $input['preco'];

            if ($qty <= 0 || $preco <= 0) {
                continue;
            }

            if (!isset($produtos[$id])) {
                return ['ok' => false, 'venda_id' => 0, 'erro' => "Produto #$id não encontrado."];
            }

            $produto = $produtos[$id];

            if ($produto->movimentaEstoque && $produto->estoque < $qty) {
                return [
                    'ok'       => false,
                    'venda_id' => 0,
                    'erro'     => "Estoque insuficiente para \"{$produto->nome}\". Disponível: {$produto->estoque}.",
                ];
            }

            $item                = new VendaItem();
            $item->produtoId     = $id;
            $item->produtoNome   = $produto->nome;
            $item->quantidade    = $qty;
            $item->precoUnitario = $preco;
            $item->subtotal      = round($preco * $qty, 2);

            $itens[]  = $item;
            $subtotal += $item->subtotal;
        }

        if (empty($itens)) {
            return ['ok' => false, 'venda_id' => 0, 'erro' => 'Nenhum item válido no carrinho.'];
        }

        $desconto = min(max($desconto, 0), $subtotal);
        $total    = round($subtotal - $desconto, 2);

        $pagamentos = $this->normalizarPagamentos($pagamentosInput, $formaPagamento, $valorPago, $total);
        $valorPagoTotal = round(array_sum(array_map(fn (VendaPagamento $p): float => $p->valor, $pagamentos)), 2);
        $valorDinheiro = round(array_sum(array_map(
            fn (VendaPagamento $p): float => $this->isDinheiro($p->formaPagamento) ? $p->valor : 0.0,
            $pagamentos
        )), 2);
        $troco = max(round($valorPagoTotal - $total, 2), 0);

        if ($valorPagoTotal < $total) {
            return ['ok' => false, 'venda_id' => 0, 'erro' => 'Valor pago insuficiente.'];
        }

        if ($troco > 0 && $valorDinheiro < $troco) {
            return ['ok' => false, 'venda_id' => 0, 'erro' => 'Troco maior que o valor pago em dinheiro.'];
        }

        // Deduzir estoque de produtos que controlam quantidade.
        foreach ($itens as $item) {
            $produto = $produtos[$item->produtoId] ?? null;
            if ($produto && !$produto->movimentaEstoque) {
                continue;
            }

            if (!$this->produtos->decrementarEstoque($item->produtoId, $item->quantidade)) {
                return [
                    'ok'       => false,
                    'venda_id' => 0,
                    'erro'     => "Estoque insuficiente para \"{$item->produtoNome}\" no momento do fechamento.",
                ];
            }
        }

        $venda                = new Venda();
        $venda->usuarioId     = $usuarioId;
        $venda->empresaId     = $empresaId;
        $venda->caixaId       = $caixaId;
        $venda->total         = $total;
        $venda->desconto      = $desconto;
        $venda->formaPagamento = count($pagamentos) > 1 ? 'combinado' : $pagamentos[0]->formaPagamento;
        $venda->valorPago     = $valorPagoTotal;
        $venda->troco         = $troco;
        $venda->itens         = $itens;
        $venda->pagamentos    = $pagamentos;

        try {
            $vendaId = $this->vendas->save($venda);
            return ['ok' => true, 'venda_id' => $vendaId, 'erro' => ''];
        } catch (\Throwable $e) {
            return ['ok' => false, 'venda_id' => 0, 'erro' => 'Erro ao salvar venda: ' . $e->getMessage()];
        }
    }

    /**
     * @return VendaPagamento[]
     */
    private function normalizarPagamentos(array $pagamentosInput, string $formaPagamento, float $valorPago, float $total): array
    {
        $pagamentos = [];

        foreach ($pagamentosInput as $input) {
            $forma = trim((string) ($input['forma_pagamento'] ?? ''));
            $valor = round((float) ($input['valor'] ?? 0), 2);
            if ($forma === '' || $valor <= 0) {
                continue;
            }

            $pagamento = new VendaPagamento();
            $pagamento->formaPagamento = $forma;
            $pagamento->valor = $valor;
            $pagamentos[] = $pagamento;
        }

        if (!empty($pagamentos)) {
            return $pagamentos;
        }

        $pagamento = new VendaPagamento();
        $pagamento->formaPagamento = $formaPagamento;
        $pagamento->valor = $this->isDinheiro($formaPagamento) ? $valorPago : $total;
        return [$pagamento];
    }

    private function isDinheiro(string $formaPagamento): bool
    {
        return $formaPagamento === 'dinheiro' || str_starts_with($formaPagamento, 'dinheiro_');
    }

    public function relatorio(string $dataInicio, string $dataFim, int $empresaId = 0): array
    {
        return $this->vendas->findByPeriodo($dataInicio, $dataFim, $empresaId);
    }

    public function recibo(int $id): ?Venda
    {
        return $this->vendas->findByIdComItens($id);
    }

    public function dashboard(int $empresaId = 0): array
    {
        return [
            'total_hoje'  => $this->vendas->totalHoje($empresaId),
            'count_hoje'  => $this->vendas->countHoje($empresaId),
            'recentes'    => $this->vendas->findRecentes(10, $empresaId),
        ];
    }
}
