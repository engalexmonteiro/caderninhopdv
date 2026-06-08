<?php

namespace App\Services;

use App\Models\Produto;
use App\Repositories\ProdutoRepository;

class ProdutoService
{
    public function __construct(private ProdutoRepository $repo) {}

    /** @return Produto[] */
    public function listar(?string $busca = null): array
    {
        return $this->repo->findAll($busca);
    }

    /** @return Produto[] */
    public function listarParaPdv(): array
    {
        return $this->repo->findAtivosComEstoque();
    }

    public function buscarPorId(int $id): ?Produto
    {
        return $this->repo->findById($id);
    }

    /**
     * Valida e salva um produto.
     * @return array{ok: bool, errors: string[], produto: ?Produto}
     */
    public function salvar(array $dados, int $id = 0): array
    {
        $errors = [];

        $codigo     = trim($dados['codigo']      ?? '');
        $nome       = trim($dados['nome']        ?? '');
        $descricao  = trim($dados['descricao']   ?? '');
        $precoCusto = (float) str_replace(',', '.', $dados['preco_custo'] ?? '0');
        $precoVenda = (float) str_replace(',', '.', $dados['preco_venda'] ?? '0');
        $estoque    = (int)  ($dados['estoque']  ?? 0);
        $unidade    = trim($dados['unidade']     ?? 'UN');
        $ativo      = isset($dados['ativo']);

        if ($codigo === '') $errors[] = 'Código é obrigatório.';
        if ($nome   === '') $errors[] = 'Nome é obrigatório.';
        if ($precoVenda <= 0) $errors[] = 'Preço de venda deve ser maior que zero.';
        if ($estoque < 0)     $errors[] = 'Estoque não pode ser negativo.';

        if (empty($errors) && $this->repo->codigoExists($codigo, $id)) {
            $errors[] = 'Já existe um produto com este código.';
        }

        if (!empty($errors)) {
            return ['ok' => false, 'errors' => $errors, 'produto' => null];
        }

        $produto             = $id > 0 ? ($this->repo->findById($id) ?? new Produto()) : new Produto();
        $produto->id         = $id;
        $produto->codigo     = $codigo;
        $produto->nome       = $nome;
        $produto->descricao  = $descricao;
        $produto->precoCusto = $precoCusto;
        $produto->precoVenda = $precoVenda;
        $produto->estoque    = $estoque;
        $produto->unidade    = $unidade;
        $produto->ativo      = $ativo;

        $produto = $this->repo->save($produto);

        return ['ok' => true, 'errors' => [], 'produto' => $produto];
    }

    public function toggle(int $id): void
    {
        $this->repo->toggle($id);
    }

    public function excluir(int $id): void
    {
        $this->repo->delete($id);
    }
}
