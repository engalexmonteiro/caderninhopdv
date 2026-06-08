<?php

namespace App\Models;

class TipoPagamento
{
    public int $id = 0;
    public string $codigo = '';
    public string $nome = '';
    public int $ordem = 0;
    public bool $ativo = true;
    public string $criadoEm = '';

    public static function fromArray(array $row): self
    {
        $tipo = new self();
        $tipo->id = (int) ($row['id'] ?? 0);
        $tipo->codigo = (string) ($row['codigo'] ?? '');
        $tipo->nome = (string) ($row['nome'] ?? '');
        $tipo->ordem = (int) ($row['ordem'] ?? 0);
        $tipo->ativo = (bool) ($row['ativo'] ?? true);
        $tipo->criadoEm = (string) ($row['criado_em'] ?? '');
        return $tipo;
    }
}
