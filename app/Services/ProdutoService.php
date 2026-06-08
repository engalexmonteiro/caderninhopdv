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
        [$produto, $errors] = $this->prepararProduto($dados, $id);

        if (!empty($errors)) {
            return ['ok' => false, 'errors' => $errors, 'produto' => null];
        }

        $produto = $this->repo->save($produto);

        return ['ok' => true, 'errors' => [], 'produto' => $produto];
    }

    /**
     * @return array{ok: bool, importados: int, atualizados: int, erros: string[]}
     */
    public function importarXlsx(string $arquivo): array
    {
        try {
            $rows = XlsxReader::readRows($arquivo);
        } catch (\Throwable $e) {
            return ['ok' => false, 'importados' => 0, 'atualizados' => 0, 'erros' => [$e->getMessage()]];
        }

        if (count($rows) < 2) {
            return ['ok' => false, 'importados' => 0, 'atualizados' => 0, 'erros' => ['A planilha não possui linhas para importar.']];
        }

        $headers = array_map(fn ($h) => $this->normalizarCabecalho((string) $h), array_shift($rows));
        $importados = 0;
        $atualizados = 0;
        $erros = [];

        foreach ($rows as $line => $row) {
            $dados = [];
            foreach ($headers as $i => $header) {
                if ($header !== '') {
                    $dados[$header] = trim((string) ($row[$i] ?? ''));
                }
            }

            if (implode('', $dados) === '') {
                continue;
            }

            $codigo = $dados['codigo_barras'] ?? '';
            $existente = $codigo !== '' ? $this->repo->findByCodigoBarras($codigo) : null;
            $id = $existente?->id ?? 0;
            $result = $this->salvar($dados, $id);

            if (!$result['ok']) {
                $erros[] = 'Linha ' . ($line + 2) . ': ' . implode(' ', $result['errors']);
                continue;
            }

            $id > 0 ? $atualizados++ : $importados++;
        }

        return [
            'ok' => empty($erros),
            'importados' => $importados,
            'atualizados' => $atualizados,
            'erros' => $erros,
        ];
    }

    public function toggle(int $id): void
    {
        $this->repo->toggle($id);
    }

    public function excluir(int $id): void
    {
        $this->repo->delete($id);
    }

    /**
     * @return array{0: Produto, 1: string[]}
     */
    public function prepararProduto(array $dados, int $id = 0): array
    {
        $produto = $id > 0 ? ($this->repo->findById($id) ?? new Produto()) : new Produto();
        $produto->id = $id;

        $produto->codigoBarras = $this->texto($dados, 'codigo_barras', 'codigo');
        $produto->tipoProduto = $this->texto($dados, 'tipo_produto') ?: 'Produto';
        $produto->descricao = $this->texto($dados, 'descricao', 'nome');
        $produto->precoCusto = $this->decimal($dados, 'preco_custo');
        $produto->precoVendaVarejo = $this->decimal($dados, 'preco_venda_varejo', 'preco_venda');
        $produto->precoVendaAtacado = $this->decimal($dados, 'preco_venda_atacado');
        $produto->quantidadeMinimaAtacado = $this->inteiro($dados, 'quantidade_minima_atacado');
        $produto->unidade = $this->texto($dados, 'unidade') ?: 'Unidade';
        $produto->ativo = $this->booleano($dados, 'ativo', true);
        $produto->categoriaProduto = $this->texto($dados, 'categoria_produto');
        $produto->subcategoriaProduto = $this->texto($dados, 'subcategoria_produto');
        $produto->movimentaEstoque = $this->booleano($dados, 'movimenta_estoque', false);
        $produto->estoqueMinimo = $this->inteiro($dados, 'estoque_minimo');
        $produto->quantidadeEstoque = $this->inteiro($dados, 'quantidade_estoque', 'estoque');
        $produto->marca = $this->texto($dados, 'marca');
        $produto->modelo = $this->texto($dados, 'modelo');
        $produto->codigoBalanca = $this->texto($dados, 'codigo_balanca');
        $produto->codigoInterno = $this->texto($dados, 'codigo_interno');
        $produto->tags = $this->texto($dados, 'tags');
        $produto->tipo = $this->texto($dados, 'tipo');
        $produto->ncm = $this->texto($dados, 'ncm');
        $produto->cfop = $this->texto($dados, 'cfop');
        $produto->origem = $this->texto($dados, 'origem');
        $produto->cest = $this->texto($dados, 'cest');
        $produto->categoriaPdv = $this->texto($dados, 'categoria_pdv');
        $produto->botaoPdv = $this->booleano($dados, 'botao_pdv', false);
        $produto->categoriaLojaVirtual = $this->texto($dados, 'categoria_loja_virtual');
        $produto->subcategoriaLojaVirtual = $this->texto($dados, 'subcategoria_loja_virtual');
        $produto->nomeLojaVirtual = $this->texto($dados, 'nome_loja_virtual');
        $produto->precoDe = $this->decimal($dados, 'preco_de');
        $produto->precoPor = $this->decimal($dados, 'preco_por');
        $produto->alturaCm = $this->decimal($dados, 'altura_cm');
        $produto->larguraCm = $this->decimal($dados, 'largura_cm');
        $produto->profundidadeCm = $this->decimal($dados, 'profundidade_cm');
        $produto->pesoKg = $this->decimal($dados, 'peso_kg');
        $produto->descricaoProduto = $this->texto($dados, 'descricao_produto');
        $produto->garantia = $this->texto($dados, 'garantia');
        $produto->itensInclusos = $this->texto($dados, 'itens_inclusos');
        $produto->especificacoes = $this->texto($dados, 'especificacoes');

        $errors = [];
        if ($produto->codigoBarras === '') $errors[] = 'Código de barras é obrigatório.';
        if ($produto->descricao === '') $errors[] = 'Descrição é obrigatória.';
        if ($produto->precoVendaVarejo <= 0) $errors[] = 'Preço venda varejo deve ser maior que zero.';
        if ($produto->quantidadeEstoque < 0) $errors[] = 'Quantidade em estoque não pode ser negativa.';
        if ($produto->estoqueMinimo < 0) $errors[] = 'Estoque mínimo não pode ser negativo.';

        if (empty($errors) && $this->repo->codigoBarrasExists($produto->codigoBarras, $id)) {
            $errors[] = 'Já existe um produto com este código de barras.';
        }

        $produto->codigo = $produto->codigoBarras;
        $produto->nome = $produto->descricao;
        $produto->precoVenda = $produto->precoVendaVarejo;
        $produto->estoque = $produto->quantidadeEstoque;

        return [$produto, $errors];
    }

    private function normalizarCabecalho(string $header): string
    {
        $header = function_exists('mb_strtolower')
            ? mb_strtolower(trim($header), 'UTF-8')
            : strtolower(trim($header));
        $header = str_replace(['(', ')'], '', $header);
        $map = [
            'código de barras' => 'codigo_barras',
            'tipo de produto' => 'tipo_produto',
            'descrição' => 'descricao',
            'preço de custo' => 'preco_custo',
            'preço venda varejo' => 'preco_venda_varejo',
            'preço venda atacado' => 'preco_venda_atacado',
            'quantidade mínima atacado' => 'quantidade_minima_atacado',
            'unidade' => 'unidade',
            'ativo' => 'ativo',
            'categoria do produto' => 'categoria_produto',
            'subcategoria do produto' => 'subcategoria_produto',
            'movimenta estoque' => 'movimenta_estoque',
            'estoque mínimo' => 'estoque_minimo',
            'quantidade em estoque' => 'quantidade_estoque',
            'marca' => 'marca',
            'modelo' => 'modelo',
            'código balança' => 'codigo_balanca',
            'código interno' => 'codigo_interno',
            'tags' => 'tags',
            'tipo' => 'tipo',
            'ncm' => 'ncm',
            'cfop' => 'cfop',
            'origem' => 'origem',
            'cest' => 'cest',
            'categoria pdv' => 'categoria_pdv',
            'botão pdv' => 'botao_pdv',
            'categoria na loja virtual' => 'categoria_loja_virtual',
            'subcategoria na loja virtual' => 'subcategoria_loja_virtual',
            'nome na loja virtual' => 'nome_loja_virtual',
            'preço de' => 'preco_de',
            'preço por' => 'preco_por',
            'altura cm' => 'altura_cm',
            'largura cm' => 'largura_cm',
            'profundidade cm' => 'profundidade_cm',
            'peso kg' => 'peso_kg',
            'descrição do produto' => 'descricao_produto',
            'garantia' => 'garantia',
            'itens inclusos' => 'itens_inclusos',
            'especificações' => 'especificacoes',
        ];

        $ascii = function_exists('iconv') ? iconv('UTF-8', 'ASCII//TRANSLIT', $header) : $header;
        return $map[$header] ?? (preg_replace('/[^a-z0-9]+/', '_', (string) $ascii) ?: '');
    }

    private function texto(array $dados, string ...$keys): string
    {
        foreach ($keys as $key) {
            if (isset($dados[$key])) {
                return trim((string) $dados[$key]);
            }
        }
        return '';
    }

    private function decimal(array $dados, string ...$keys): float
    {
        $value = $this->texto($dados, ...$keys);
        $value = str_replace(['R$', ' '], '', $value);
        if (str_contains($value, ',') && str_contains($value, '.')) {
            $value = str_replace('.', '', $value);
        }
        return (float) str_replace(',', '.', $value);
    }

    private function inteiro(array $dados, string ...$keys): int
    {
        return (int) round($this->decimal($dados, ...$keys));
    }

    private function booleano(array $dados, string $key, bool $default): bool
    {
        if (!array_key_exists($key, $dados)) {
            return $default;
        }
        $raw = trim((string) $dados[$key]);
        $value = function_exists('mb_strtolower') ? mb_strtolower($raw, 'UTF-8') : strtolower($raw);
        if ($value === '') {
            return false;
        }
        return in_array($value, ['1', 'sim', 's', 'true', 'on', 'ativo'], true);
    }
}
