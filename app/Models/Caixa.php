<?php

namespace App\Models;

class Caixa
{
    public int $id = 0;
    public int $usuarioId = 0;
    public string $usuarioNome = '';
    public float $fundoInicial = 0.0;
    public float $valorFechamento = 0.0;
    public string $status = 'aberto';
    public string $observacaoAbertura = '';
    public string $observacaoFechamento = '';
    public string $abertoEm = '';
    public string $fechadoEm = '';

    public static function fromArray(array $row): self
    {
        $caixa = new self();
        $caixa->id = (int) ($row['id'] ?? 0);
        $caixa->usuarioId = (int) ($row['usuario_id'] ?? 0);
        $caixa->usuarioNome = (string) ($row['usuario_nome'] ?? '');
        $caixa->fundoInicial = (float) ($row['fundo_inicial'] ?? 0);
        $caixa->valorFechamento = (float) ($row['valor_fechamento'] ?? 0);
        $caixa->status = (string) ($row['status'] ?? 'aberto');
        $caixa->observacaoAbertura = (string) ($row['observacao_abertura'] ?? '');
        $caixa->observacaoFechamento = (string) ($row['observacao_fechamento'] ?? '');
        $caixa->abertoEm = (string) ($row['aberto_em'] ?? '');
        $caixa->fechadoEm = (string) ($row['fechado_em'] ?? '');
        return $caixa;
    }
}
