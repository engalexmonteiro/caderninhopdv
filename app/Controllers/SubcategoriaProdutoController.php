<?php

namespace App\Controllers;

use App\Models\SubcategoriaProduto;
use App\Repositories\CategoriaProdutoRepository;
use App\Repositories\SubcategoriaProdutoRepository;
use App\Services\CategoriaProdutoService;
use App\Services\SubcategoriaProdutoService;

class SubcategoriaProdutoController
{
    private SubcategoriaProdutoService $service;
    private CategoriaProdutoService $categoriaService;

    public function __construct()
    {
        $pdo = getDB();
        $categoriaRepo = new CategoriaProdutoRepository($pdo);
        $this->service = new SubcategoriaProdutoService(
            new SubcategoriaProdutoRepository($pdo),
            $categoriaRepo
        );
        $this->categoriaService = new CategoriaProdutoService($categoriaRepo);
    }

    public function index(): void
    {
        requireAdmin();
        render('subcategorias_produto/lista', [
            'pageTitle' => 'Subcategorias de Produtos',
            'subcategorias' => $this->service->listar(),
            'flash' => flash('subcategoria_produto'),
        ]);
    }

    public function create(): void
    {
        requireAdmin();
        render('subcategorias_produto/form', [
            'pageTitle' => 'Nova Subcategoria de Produto',
            'subcategoria' => new SubcategoriaProduto(),
            'categorias' => $this->categoriaService->listarAtivas(),
            'errors' => [],
        ]);
    }

    public function store(): void
    {
        requireAdmin();
        $result = $this->service->salvar($_POST);
        if (!$result['ok']) {
            render('subcategorias_produto/form', [
                'pageTitle' => 'Nova Subcategoria de Produto',
                'subcategoria' => $result['subcategoria'],
                'categorias' => $this->categoriaService->listarAtivas(),
                'errors' => $result['errors'],
            ]);
            return;
        }
        redirect('/produtos/subcategorias', 'subcategoria_produto', 'Subcategoria cadastrada com sucesso.');
    }

    public function edit(string $id): void
    {
        requireAdmin();
        $subcategoria = $this->service->buscarPorId((int) $id);
        if (!$subcategoria) {
            redirect('/produtos/subcategorias');
        }
        render('subcategorias_produto/form', [
            'pageTitle' => 'Editar Subcategoria de Produto',
            'subcategoria' => $subcategoria,
            'categorias' => $this->categoriaService->listarAtivas(),
            'errors' => [],
        ]);
    }

    public function update(string $id): void
    {
        requireAdmin();
        $result = $this->service->salvar($_POST, (int) $id);
        if (!$result['ok']) {
            render('subcategorias_produto/form', [
                'pageTitle' => 'Editar Subcategoria de Produto',
                'subcategoria' => $result['subcategoria'],
                'categorias' => $this->categoriaService->listarAtivas(),
                'errors' => $result['errors'],
            ]);
            return;
        }
        redirect('/produtos/subcategorias', 'subcategoria_produto', 'Subcategoria atualizada com sucesso.');
    }

    public function toggle(string $id): void
    {
        requireAdmin();
        $this->service->toggle((int) $id);
        redirect('/produtos/subcategorias', 'subcategoria_produto', 'Status da subcategoria alterado.');
    }
}
