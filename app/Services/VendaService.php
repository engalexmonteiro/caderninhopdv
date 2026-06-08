<?php

namespace App\Services;

use App\Models\Venda;
use App\Models\VendaItem;
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
        int    $usuarioId
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

            if ($produto->estoque < $qty) {
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
        $troco    = $formaPagamento === 'dinheiro' ? max(round($valorPago - $total, 2), 0) : 0.0;

        if ($formaPagamento === 'dinheiro' && $valorPago < $total) {
            return ['ok' => false, 'venda_id' => 0, 'erro' => 'Valor pago insuficiente.'];
        }

        // Deduzir estoque de cada produto
        foreach ($itens as $item) {
            $ok = $this->produtos->decrementarEstoque($item->produtoId, $item->quantidade);
            if (!$ok) {
                return [
                    'ok'       => false,
                    'venda_id' => 0,
                    'erro'     => "Estoque insuficiente para \"{$item->produtoNome}\" no momento do fechamento.",
                ];
            }
        }

        $venda                = new Venda();
        $venda->usuarioId     = $usuarioId;
        $venda->total         = $total;
        $venda->desconto      = $desconto;
        $venda->formaPagamento = $formaPagamento;
        $venda->valorPago     = $formaPagamento === 'dinheiro' ? $valorPago : $total;
        $venda->troco         = $troco;
        $venda->itens         = $itens;

        try {
            $vendaId = $this->vendas->save($venda);
            return ['ok' => true, 'venda_id' => $vendaId, 'erro' => ''];
        } catch (\Throwable $e) {
            return ['ok' => false, 'venda_id' => 0, 'erro' => 'Erro ao salvar venda: ' . $e->getMessage()];
        }
    }

    public function relatorio(string $dataInicio, string $dataFim): array
    {
        return $this->vendas->findByPeriodo($dataInicio, $dataFim);
    }

    public function dashboard(): array
    {
        return [
            'total_hoje'  => $this->vendas->totalHoje(),
            'count_hoje'  => $this->vendas->countHoje(),
            'recentes'    => $this->vendas->findRecentes(10),
        ];
    }
}
