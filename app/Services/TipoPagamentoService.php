<?php

namespace App\Services;

use App\Models\TipoPagamento;
use App\Repositories\TipoPagamentoRepository;

class TipoPagamentoService
{
    public function __construct(private TipoPagamentoRepository $repo) {}

    /** @return TipoPagamento[] */
    public function listar(): array
    {
        return $this->repo->findAll();
    }

    /** @return TipoPagamento[] */
    public function listarAtivos(): array
    {
        return $this->repo->findAtivos();
    }

    public function buscarPorId(int $id): ?TipoPagamento
    {
        return $this->repo->findById($id);
    }

    /**
     * @return array{ok: bool, errors: string[], tipo: TipoPagamento}
     */
    public function salvar(array $dados, int $id = 0): array
    {
        $tipo = $id > 0 ? ($this->repo->findById($id) ?? new TipoPagamento()) : new TipoPagamento();
        $tipo->id = $id;
        $tipo->codigo = $this->normalizarCodigo(trim($dados['codigo'] ?? ''));
        $tipo->nome = trim($dados['nome'] ?? '');
        $tipo->ordem = max(0, (int) ($dados['ordem'] ?? 0));
        $tipo->ativo = isset($dados['ativo']) && (string) $dados['ativo'] !== '0';

        $errors = [];
        if ($tipo->codigo === '') {
            $errors[] = 'Código é obrigatório.';
        }
        if ($tipo->nome === '') {
            $errors[] = 'Nome é obrigatório.';
        }
        if ($tipo->codigo !== '' && $this->repo->codigoExiste($tipo->codigo, $id)) {
            $errors[] = 'Já existe um tipo de pagamento com este código.';
        }

        if (!empty($errors)) {
            return ['ok' => false, 'errors' => $errors, 'tipo' => $tipo];
        }

        $tipo->id = $this->repo->save($tipo);
        return ['ok' => true, 'errors' => [], 'tipo' => $tipo];
    }

    public function toggle(int $id): void
    {
        $this->repo->toggle($id);
    }

    private function normalizarCodigo(string $codigo): string
    {
        $codigo = function_exists('iconv') ? iconv('UTF-8', 'ASCII//TRANSLIT', $codigo) : $codigo;
        $codigo = strtolower((string) $codigo);
        return trim((string) preg_replace('/[^a-z0-9]+/', '_', $codigo), '_');
    }
}
