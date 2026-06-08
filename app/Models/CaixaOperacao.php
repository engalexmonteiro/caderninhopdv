<?php

namespace App\Models;

class CaixaOperacao
{
    public int $id = 0;
    public int $caixaId = 0;
    public int $usuarioId = 0;
    public string $tipo = '';
    public float $valor = 0.0;
    public string $observacao = '';
    public string $criadoEm = '';

    public static function fromArray(array $row): self
    {
        $operacao = new self();
        $operacao->id = (int) ($row['id'] ?? 0);
        $operacao->caixaId = (int) ($row['caixa_id'] ?? 0);
        $operacao->usuarioId = (int) ($row['usuario_id'] ?? 0);
        $operacao->tipo = (string) ($row['tipo'] ?? '');
        $operacao->valor = (float) ($row['valor'] ?? 0);
        $operacao->observacao = (string) ($row['observacao'] ?? '');
        $operacao->criadoEm = (string) ($row['criado_em'] ?? '');
        return $operacao;
    }
}
