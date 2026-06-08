<?php

namespace App\Models;

class VendaItem
{
    public int    $produtoId     = 0;
    public string $produtoCodigo = '';
    public string $produtoNome   = '';
    public int    $quantidade    = 1;
    public float  $precoUnitario = 0.0;
    public float  $subtotal      = 0.0;

    public static function fromArray(array $row): self
    {
        $item = new self();
        $item->produtoId = (int) ($row['produto_id'] ?? 0);
        $item->produtoCodigo = (string) ($row['produto_codigo'] ?? '');
        $item->produtoNome = (string) ($row['produto_nome'] ?? '');
        $item->quantidade = (int) ($row['quantidade'] ?? 1);
        $item->precoUnitario = (float) ($row['preco_unitario'] ?? 0);
        $item->subtotal = (float) ($row['subtotal'] ?? 0);
        return $item;
    }
}
