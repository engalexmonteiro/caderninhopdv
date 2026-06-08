<?php

namespace App\Services;

use App\Models\CategoriaProduto;
use App\Repositories\CategoriaProdutoRepository;

class CategoriaProdutoService
{
    public function __construct(private CategoriaProdutoRepository $repo) {}

    /** @return CategoriaProduto[] */
    public function listar(): array
    {
        return $this->repo->findAll();
    }

    /** @return CategoriaProduto[] */
    public function listarAtivas(): array
    {
        return $this->repo->findAtivas();
    }

    public function buscarPorId(int $id): ?CategoriaProduto
    {
        return $this->repo->findById($id);
    }

    public function salvar(array $dados, int $id = 0): array
    {
        $categoria = $id > 0 ? ($this->repo->findById($id) ?? new CategoriaProduto()) : new CategoriaProduto();
        $categoria->id = $id;
        $categoria->nome = trim($dados['nome'] ?? '');
        $categoria->ativo = isset($dados['ativo']) && (string) $dados['ativo'] !== '0';

        $errors = [];
        if ($categoria->nome === '') {
            $errors[] = 'Nome é obrigatório.';
        } elseif ($this->repo->nomeExiste($categoria->nome, $id)) {
            $errors[] = 'Já existe uma categoria com este nome.';
        }

        if (!empty($errors)) {
            return ['ok' => false, 'errors' => $errors, 'categoria' => $categoria];
        }

        $categoria->id = $this->repo->save($categoria);
        return ['ok' => true, 'errors' => [], 'categoria' => $categoria];
    }

    public function toggle(int $id): void
    {
        $this->repo->toggle($id);
    }
}
