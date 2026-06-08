<?php

namespace App\Models;

class Venda
{
    public int    $id             = 0;
    public int    $usuarioId      = 0;
    public string $usuarioNome    = '';
    public float  $total          = 0.0;
    public float  $desconto       = 0.0;
    public string $formaPagamento = 'dinheiro';
    public float  $valorPago      = 0.0;
    public float  $troco          = 0.0;
    public string $status         = 'concluida';
    public string $criadoEm       = '';

    /** @var VendaItem[] */
    public array $itens = [];

    public static function fromArray(array $row): self
    {
        $v                 = new self();
        $v->id             = (int)   ($row['id']              ?? 0);
        $v->usuarioId      = (int)   ($row['usuario_id']      ?? 0);
        $v->usuarioNome    = (string)($row['usuario_nome']    ?? '');
        $v->total          = (float) ($row['total']           ?? 0);
        $v->desconto       = (float) ($row['desconto']        ?? 0);
        $v->formaPagamento = (string)($row['forma_pagamento'] ?? 'dinheiro');
        $v->valorPago      = (float) ($row['valor_pago']      ?? 0);
        $v->troco          = (float) ($row['troco']           ?? 0);
        $v->status         = (string)($row['status']          ?? 'concluida');
        $v->criadoEm       = (string)($row['criado_em']       ?? '');
        return $v;
    }

    public static function formaLabel(string $forma): string
    {
        return match ($forma) {
            'cartao_credito' => 'Cartão Crédito',
            'cartao_debito'  => 'Cartão Débito',
            'pix'            => 'PIX',
            default          => 'Dinheiro',
        };
    }
}
