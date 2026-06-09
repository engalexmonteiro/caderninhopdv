<?php

namespace App\Services;

use App\Models\FormaPagamento;
use App\Repositories\FormaPagamentoRepository;
use App\Repositories\TipoPagamentoRepository;

class FormaPagamentoService
{
    public function __construct(
        private FormaPagamentoRepository $repo,
        private TipoPagamentoRepository $tipoRepo,
    ) {}

    /** @return FormaPagamento[] */
    public function listar(): array
    {
        return $this->repo->findAll();
    }

    /** @return FormaPagamento[] */
    public function listarAtivas(): array
    {
        return $this->repo->findAtivas();
    }

    public function buscarPorId(int $id): ?FormaPagamento
    {
        return $this->repo->findById($id);
    }

    /**
     * @return array{ok: bool, errors: string[], forma: FormaPagamento}
     */
    public function salvar(array $dados, int $id = 0): array
    {
        $forma = $id > 0 ? ($this->repo->findById($id) ?? new FormaPagamento()) : new FormaPagamento();
        $forma->id = $id;
        $forma->tipoPagamentoId = (int) ($dados['tipo_pagamento_id'] ?? 0);
        $forma->parcelas = max(1, (int) ($dados['parcelas'] ?? 1));
        $forma->ordem = max(0, (int) ($dados['ordem'] ?? 0));
        $forma->ativo = isset($dados['ativo']) && (string) $dados['ativo'] !== '0';
        $forma->nome = trim($dados['nome'] ?? '');
        $forma->codigo = $this->normalizarCodigo(trim($dados['codigo'] ?? ''));

        $tipo = $this->tipoRepo->findById($forma->tipoPagamentoId);
        if ($tipo && $forma->nome === '') {
            $forma->nome = $this->nomeParcelado($tipo->nome, $forma->parcelas);
        }
        if ($tipo && $forma->codigo === '') {
            $forma->codigo = $this->codigoParcelado($tipo->codigo, $forma->parcelas);
        }

        $errors = [];
        if (!$tipo) {
            $errors[] = 'Tipo de pagamento e obrigatorio.';
        }
        if ($forma->nome === '') {
            $errors[] = 'Nome e obrigatorio.';
        }
        if ($forma->codigo === '') {
            $errors[] = 'Codigo e obrigatorio.';
        } elseif ($this->repo->codigoExiste($forma->codigo, $id)) {
            $errors[] = 'Ja existe uma forma de pagamento com este codigo.';
        }

        if (!empty($errors)) {
            return ['ok' => false, 'errors' => $errors, 'forma' => $forma];
        }

        $forma->id = $this->repo->save($forma);
        return ['ok' => true, 'errors' => [], 'forma' => $forma];
    }

    public function gerarParcelas(array $dados): array
    {
        $tipoId = (int) ($dados['tipo_pagamento_id'] ?? 0);
        $maxParcelas = max(1, (int) ($dados['max_parcelas'] ?? 1));
        $ordemInicial = max(0, (int) ($dados['ordem'] ?? 0));
        $ativo = isset($dados['ativo']) && (string) $dados['ativo'] !== '0';
        $tipo = $this->tipoRepo->findById($tipoId);
        $errors = [];

        if (!$tipo) {
            $errors[] = 'Tipo de pagamento e obrigatorio.';
        }

        if ($maxParcelas < 1) {
            $errors[] = 'Informe a quantidade maxima de parcelas.';
        }

        if (!empty($errors)) {
            $forma = new FormaPagamento();
            $forma->tipoPagamentoId = $tipoId;
            return ['ok' => false, 'errors' => $errors, 'forma' => $forma];
        }

        for ($parcela = 1; $parcela <= $maxParcelas; $parcela++) {
            $codigo = $this->codigoParcelado($tipo->codigo, $parcela);
            $existente = $this->repo->findByCodigo($codigo);
            $forma = $existente ?? new FormaPagamento();
            $forma->tipoPagamentoId = $tipo->id;
            $forma->codigo = $codigo;
            $forma->nome = $this->nomeParcelado($tipo->nome, $parcela);
            $forma->parcelas = $parcela;
            $forma->ordem = $ordemInicial + $parcela;
            $forma->ativo = $ativo;
            $this->repo->save($forma);
        }

        return ['ok' => true, 'errors' => [], 'forma' => new FormaPagamento()];
    }

    public function toggle(int $id): void
    {
        $this->repo->toggle($id);
    }

    private function nomeParcelado(string $nomeTipo, int $parcelas): string
    {
        return $parcelas > 1 ? $nomeTipo . ' ' . $parcelas . 'x' : $nomeTipo . ' 1x';
    }

    private function codigoParcelado(string $codigoTipo, int $parcelas): string
    {
        return $codigoTipo . '_' . $parcelas . 'x';
    }

    private function normalizarCodigo(string $codigo): string
    {
        $codigo = function_exists('iconv') ? iconv('UTF-8', 'ASCII//TRANSLIT', $codigo) : $codigo;
        $codigo = strtolower((string) $codigo);
        return trim((string) preg_replace('/[^a-z0-9]+/', '_', $codigo), '_');
    }
}
