<?php

namespace App\Models;

class Produto
{
    public int    $id         = 0;
    public string $codigo     = '';
    public string $nome       = '';
    public string $descricao  = '';
    public float  $precoCusto = 0.0;
    public float  $precoVenda = 0.0;
    public int    $estoque    = 0;
    public string $unidade    = 'UN';
    public bool   $ativo      = true;

    public static function fromArray(array $row): self
    {
        $p             = new self();
        $p->id         = (int)   ($row['id']          ?? 0);
        $p->codigo     = (string)($row['codigo']       ?? '');
        $p->nome       = (string)($row['nome']         ?? '');
        $p->descricao  = (string)($row['descricao']    ?? '');
        $p->precoCusto = (float) ($row['preco_custo']  ?? 0);
        $p->precoVenda = (float) ($row['preco_venda']  ?? 0);
        $p->estoque    = (int)   ($row['estoque']      ?? 0);
        $p->unidade    = (string)($row['unidade']      ?? 'UN');
        $p->ativo      = (bool)  ($row['ativo']        ?? true);
        return $p;
    }
}
