<?php

namespace App\Models;

class VendaPagamento
{
    public int $id = 0;
    public int $vendaId = 0;
    public string $formaPagamento = '';
    public float $valor = 0.0;

    public static function fromArray(array $row): self
    {
        $pagamento = new self();
        $pagamento->id = (int) ($row['id'] ?? 0);
        $pagamento->vendaId = (int) ($row['venda_id'] ?? 0);
        $pagamento->formaPagamento = (string) ($row['forma_pagamento'] ?? '');
        $pagamento->valor = (float) ($row['valor'] ?? 0);
        return $pagamento;
    }
}
