<?php

namespace App\Models;

class FormaPagamento
{
    public int $id = 0;
    public int $tipoPagamentoId = 0;
    public string $tipoCodigo = '';
    public string $tipoNome = '';
    public string $codigo = '';
    public string $nome = '';
    public int $parcelas = 1;
    public int $ordem = 0;
    public bool $ativo = true;
    public string $criadoEm = '';

    public static function fromArray(array $row): self
    {
        $forma = new self();
        $forma->id = (int) ($row['id'] ?? 0);
        $forma->tipoPagamentoId = (int) ($row['tipo_pagamento_id'] ?? 0);
        $forma->tipoCodigo = (string) ($row['tipo_codigo'] ?? '');
        $forma->tipoNome = (string) ($row['tipo_nome'] ?? '');
        $forma->codigo = (string) ($row['codigo'] ?? '');
        $forma->nome = (string) ($row['nome'] ?? '');
        $forma->parcelas = (int) ($row['parcelas'] ?? 1);
        $forma->ordem = (int) ($row['ordem'] ?? 0);
        $forma->ativo = (bool) ($row['ativo'] ?? true);
        $forma->criadoEm = (string) ($row['criado_em'] ?? '');
        return $forma;
    }
}
