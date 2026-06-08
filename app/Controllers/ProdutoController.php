<?php

namespace App\Controllers;

use App\Models\Produto;
use App\Repositories\CategoriaProdutoRepository;
use App\Repositories\ProdutoRepository;
use App\Repositories\SubcategoriaProdutoRepository;
use App\Services\CategoriaProdutoService;
use App\Services\ProdutoService;
use App\Services\SubcategoriaProdutoService;

class ProdutoController
{
    private ProdutoService $service;
    private CategoriaProdutoService $categoriaService;
    private SubcategoriaProdutoService $subcategoriaService;

    public function __construct()
    {
        $pdo = getDB();
        $categoriaRepo = new CategoriaProdutoRepository($pdo);
        $this->service = new ProdutoService(new ProdutoRepository($pdo));
        $this->categoriaService = new CategoriaProdutoService($categoriaRepo);
        $this->subcategoriaService = new SubcategoriaProdutoService(
            new SubcategoriaProdutoRepository($pdo),
            $categoriaRepo
        );
    }

    public function index(): void
    {
        requireLogin();

        $busca    = trim($_GET['q'] ?? '');
        $produtos = $this->service->listar($busca !== '' ? $busca : null);
        $flash    = flash('produto');

        render('produtos/lista', [
            'pageTitle' => 'Produtos',
            'produtos'  => $produtos,
            'busca'     => $busca,
            'flash'     => $flash,
        ]);
    }

    public function create(): void
    {
        requireAdmin();
        render('produtos/form', [
            'pageTitle' => 'Novo Produto',
            'produto'   => new Produto(),
            'categorias' => $this->categoriaService->listarAtivas(),
            'subcategorias' => $this->subcategoriaService->listarAtivas(),
            'errors'    => [],
        ]);
    }

    public function store(): void
    {
        requireAdmin();

        $result = $this->service->salvar($_POST);

        if (!$result['ok']) {
            [$produto] = $this->service->prepararProduto($_POST);

            render('produtos/form', [
                'pageTitle' => 'Novo Produto',
                'produto'   => $produto,
                'categorias' => $this->categoriaService->listarAtivas(),
                'subcategorias' => $this->subcategoriaService->listarAtivas(),
                'errors'    => $result['errors'],
            ]);
            return;
        }

        redirect('/produtos', 'produto', 'Produto cadastrado com sucesso.');
    }

    public function edit(string $id): void
    {
        requireAdmin();

        $produto = $this->service->buscarPorId((int) $id);
        if (!$produto) {
            redirect('/produtos');
        }

        render('produtos/form', [
            'pageTitle' => 'Editar Produto',
            'produto'   => $produto,
            'categorias' => $this->categoriaService->listarAtivas(),
            'subcategorias' => $this->subcategoriaService->listarAtivas(),
            'errors'    => [],
        ]);
    }

    public function update(string $id): void
    {
        requireAdmin();

        $result = $this->service->salvar($_POST, (int) $id);

        if (!$result['ok']) {
            [$produto] = $this->service->prepararProduto($_POST, (int) $id);

            render('produtos/form', [
                'pageTitle' => 'Editar Produto',
                'produto'   => $produto,
                'categorias' => $this->categoriaService->listarAtivas(),
                'subcategorias' => $this->subcategoriaService->listarAtivas(),
                'errors'    => $result['errors'],
            ]);
            return;
        }

        redirect('/produtos', 'produto', 'Produto atualizado com sucesso.');
    }

    public function toggle(string $id): void
    {
        requireAdmin();
        $this->service->toggle((int) $id);
        redirect('/produtos', 'produto', 'Status do produto alterado.');
    }

    public function destroy(string $id): void
    {
        requireAdmin();
        $this->service->excluir((int) $id);
        redirect('/produtos', 'produto', 'Produto excluído com sucesso.');
    }
    public function importForm(): void
    {
        requireAdmin();

        render('produtos/importar', [
            'pageTitle' => 'Importar Produtos',
            'result' => null,
            'errors' => [],
        ]);
    }

    public function import(): void
    {
        requireAdmin();

        $file = $_FILES['arquivo'] ?? null;
        $errors = [];

        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $errors[] = 'Selecione um arquivo .xlsx válido.';
        } elseif (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'xlsx') {
            $errors[] = 'O arquivo precisa estar no formato .xlsx.';
        }

        if (!empty($errors)) {
            render('produtos/importar', [
                'pageTitle' => 'Importar Produtos',
                'result' => null,
                'errors' => $errors,
            ]);
            return;
        }

        $result = $this->service->importarXlsx($file['tmp_name']);

        render('produtos/importar', [
            'pageTitle' => 'Importar Produtos',
            'result' => $result,
            'errors' => [],
        ]);
    }
}
