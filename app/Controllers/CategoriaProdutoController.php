<?php

namespace App\Controllers;

use App\Models\CategoriaProduto;
use App\Repositories\CategoriaProdutoRepository;
use App\Services\CategoriaProdutoService;

class CategoriaProdutoController
{
    private CategoriaProdutoService $service;

    public function __construct()
    {
        $this->service = new CategoriaProdutoService(new CategoriaProdutoRepository(getDB()));
    }

    public function index(): void
    {
        requireAdmin();
        render('categorias_produto/lista', [
            'pageTitle' => 'Categorias de Produtos',
            'categorias' => $this->service->listar(),
            'flash' => flash('categoria_produto'),
        ]);
    }

    public function create(): void
    {
        requireAdmin();
        render('categorias_produto/form', [
            'pageTitle' => 'Nova Categoria de Produto',
            'categoria' => new CategoriaProduto(),
            'errors' => [],
        ]);
    }

    public function store(): void
    {
        requireAdmin();
        $result = $this->service->salvar($_POST);
        if (!$result['ok']) {
            render('categorias_produto/form', [
                'pageTitle' => 'Nova Categoria de Produto',
                'categoria' => $result['categoria'],
                'errors' => $result['errors'],
            ]);
            return;
        }
        redirect('/produtos/categorias', 'categoria_produto', 'Categoria cadastrada com sucesso.');
    }

    public function edit(string $id): void
    {
        requireAdmin();
        $categoria = $this->service->buscarPorId((int) $id);
        if (!$categoria) {
            redirect('/produtos/categorias');
        }
        render('categorias_produto/form', [
            'pageTitle' => 'Editar Categoria de Produto',
            'categoria' => $categoria,
            'errors' => [],
        ]);
    }

    public function update(string $id): void
    {
        requireAdmin();
        $result = $this->service->salvar($_POST, (int) $id);
        if (!$result['ok']) {
            render('categorias_produto/form', [
                'pageTitle' => 'Editar Categoria de Produto',
                'categoria' => $result['categoria'],
                'errors' => $result['errors'],
            ]);
            return;
        }
        redirect('/produtos/categorias', 'categoria_produto', 'Categoria atualizada com sucesso.');
    }

    public function toggle(string $id): void
    {
        requireAdmin();
        $this->service->toggle((int) $id);
        redirect('/produtos/categorias', 'categoria_produto', 'Status da categoria alterado.');
    }
}
