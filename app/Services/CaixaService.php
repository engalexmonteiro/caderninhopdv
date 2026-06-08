<?php

namespace App\Services;

use App\Models\Caixa;
use App\Repositories\CaixaRepository;

class CaixaService
{
    public function __construct(private CaixaRepository $repo) {}

    public function caixaAberto(int $usuarioId): ?Caixa
    {
        return $this->repo->findAbertoByUsuario($usuarioId);
    }

    public function resumo(Caixa $caixa): array
    {
        return $this->repo->resumo($caixa);
    }

    public function operacoes(Caixa $caixa): array
    {
        return $this->repo->operacoes($caixa->id);
    }

    /**
     * @return array{ok: bool, errors: string[]}
     */
    public function abrir(int $usuarioId, array $dados): array
    {
        if ($this->caixaAberto($usuarioId)) {
            return ['ok' => false, 'errors' => ['Já existe um caixa aberto para este usuário.']];
        }

        $fundoInicial = $this->decimal($dados['fundo_inicial'] ?? '0');
        $observacao = trim($dados['observacao'] ?? '');

        if ($fundoInicial < 0) {
            return ['ok' => false, 'errors' => ['Fundo de caixa não pode ser negativo.']];
        }

        $this->repo->abrir($usuarioId, $fundoInicial, $observacao);
        return ['ok' => true, 'errors' => []];
    }

    /**
     * @return array{ok: bool, errors: string[]}
     */
    public function operacao(int $usuarioId, string $tipo, array $dados): array
    {
        $caixa = $this->caixaAberto($usuarioId);
        if (!$caixa) {
            return ['ok' => false, 'errors' => ['Abra o caixa antes de registrar operações.']];
        }

        if (!in_array($tipo, ['sangria', 'reforco'], true)) {
            return ['ok' => false, 'errors' => ['Tipo de operação inválido.']];
        }

        $valor = $this->decimal($dados['valor'] ?? '0');
        $observacao = trim($dados['observacao'] ?? '');

        if ($valor <= 0) {
            return ['ok' => false, 'errors' => ['Informe um valor maior que zero.']];
        }

        $this->repo->registrarOperacao($caixa->id, $usuarioId, $tipo, $valor, $observacao);
        return ['ok' => true, 'errors' => []];
    }

    /**
     * @return array{ok: bool, errors: string[]}
     */
    public function fechar(int $usuarioId, array $dados): array
    {
        $caixa = $this->caixaAberto($usuarioId);
        if (!$caixa) {
            return ['ok' => false, 'errors' => ['Não há caixa aberto para fechar.']];
        }

        $valorFechamento = $this->decimal($dados['valor_fechamento'] ?? '0');
        $observacao = trim($dados['observacao'] ?? '');

        if ($valorFechamento < 0) {
            return ['ok' => false, 'errors' => ['Valor de fechamento não pode ser negativo.']];
        }

        $this->repo->fechar($caixa->id, $valorFechamento, $observacao);
        return ['ok' => true, 'errors' => []];
    }

    private function decimal(string|int|float $value): float
    {
        $value = str_replace(['R$', ' '], '', (string) $value);
        if (str_contains($value, ',') && str_contains($value, '.')) {
            $value = str_replace('.', '', $value);
        }
        return (float) str_replace(',', '.', $value);
    }
}
