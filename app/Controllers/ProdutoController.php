<?php

namespace App\Controllers;

use App\Models\Produto;
use App\Repositories\ProdutoRepository;
use App\Services\ProdutoService;

class ProdutoController
{
    private ProdutoService $service;

    public function __construct()
    {
        $this->service = new ProdutoService(new ProdutoRepository(getDB()));
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
            'errors'    => [],
        ]);
    }

    public function store(): void
    {
        requireAdmin();

        $result = $this->service->salvar($_POST);

        if (!$result['ok']) {
            $produto = new Produto();
            $produto->codigo     = trim($_POST['codigo']   ?? '');
            $produto->nome       = trim($_POST['nome']     ?? '');
            $produto->descricao  = trim($_POST['descricao'] ?? '');
            $produto->precoCusto = (float) str_replace(',', '.', $_POST['preco_custo'] ?? '0');
            $produto->precoVenda = (float) str_replace(',', '.', $_POST['preco_venda'] ?? '0');
            $produto->estoque    = (int)  ($_POST['estoque'] ?? 0);
            $produto->unidade    = trim($_POST['unidade'] ?? 'UN');
            $produto->ativo      = isset($_POST['ativo']);

            render('produtos/form', [
                'pageTitle' => 'Novo Produto',
                'produto'   => $produto,
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
            'errors'    => [],
        ]);
    }

    public function update(string $id): void
    {
        requireAdmin();

        $result = $this->service->salvar($_POST, (int) $id);

        if (!$result['ok']) {
            $produto = $this->service->buscarPorId((int) $id) ?? new Produto();
            $produto->codigo     = trim($_POST['codigo']   ?? '');
            $produto->nome       = trim($_POST['nome']     ?? '');
            $produto->descricao  = trim($_POST['descricao'] ?? '');
            $produto->precoCusto = (float) str_replace(',', '.', $_POST['preco_custo'] ?? '0');
            $produto->precoVenda = (float) str_replace(',', '.', $_POST['preco_venda'] ?? '0');
            $produto->estoque    = (int)  ($_POST['estoque'] ?? 0);
            $produto->unidade    = trim($_POST['unidade'] ?? 'UN');
            $produto->ativo      = isset($_POST['ativo']);

            render('produtos/form', [
                'pageTitle' => 'Editar Produto',
                'produto'   => $produto,
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
}
