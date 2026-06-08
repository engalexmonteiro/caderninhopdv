<?php

namespace App\Services;

use App\Models\SubcategoriaProduto;
use App\Repositories\CategoriaProdutoRepository;
use App\Repositories\SubcategoriaProdutoRepository;

class SubcategoriaProdutoService
{
    public function __construct(
        private SubcategoriaProdutoRepository $repo,
        private CategoriaProdutoRepository $categorias,
    ) {}

    /** @return SubcategoriaProduto[] */
    public function listar(): array
    {
        return $this->repo->findAll();
    }

    /** @return SubcategoriaProduto[] */
    public function listarAtivas(): array
    {
        return $this->repo->findAtivas();
    }

    public function buscarPorId(int $id): ?SubcategoriaProduto
    {
        return $this->repo->findById($id);
    }

    public function salvar(array $dados, int $id = 0): array
    {
        $subcategoria = $id > 0 ? ($this->repo->findById($id) ?? new SubcategoriaProduto()) : new SubcategoriaProduto();
        $subcategoria->id = $id;
        $subcategoria->categoriaId = (int) ($dados['categoria_id'] ?? 0);
        $subcategoria->nome = trim($dados['nome'] ?? '');
        $subcategoria->ativo = isset($dados['ativo']) && (string) $dados['ativo'] !== '0';

        $errors = [];
        if (!$this->categorias->findById($subcategoria->categoriaId)) {
            $errors[] = 'Categoria é obrigatória.';
        }
        if ($subcategoria->nome === '') {
            $errors[] = 'Nome é obrigatório.';
        } elseif ($subcategoria->categoriaId > 0 && $this->repo->nomeExiste($subcategoria->categoriaId, $subcategoria->nome, $id)) {
            $errors[] = 'Já existe uma subcategoria com este nome nesta categoria.';
        }

        if (!empty($errors)) {
            return ['ok' => false, 'errors' => $errors, 'subcategoria' => $subcategoria];
        }

        $subcategoria->id = $this->repo->save($subcategoria);
        return ['ok' => true, 'errors' => [], 'subcategoria' => $subcategoria];
    }

    public function toggle(int $id): void
    {
        $this->repo->toggle($id);
    }
}
