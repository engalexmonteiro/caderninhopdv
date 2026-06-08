<?php

namespace App\Models;

class Produto
{
    public int $id = 0;
    public string $codigoBarras = '';
    public string $tipoProduto = 'Produto';
    public string $descricao = '';
    public float $precoCusto = 0.0;
    public float $precoVendaVarejo = 0.0;
    public float $precoVendaAtacado = 0.0;
    public int $quantidadeMinimaAtacado = 0;
    public string $unidade = 'Unidade';
    public bool $ativo = true;
    public string $categoriaProduto = '';
    public string $subcategoriaProduto = '';
    public bool $movimentaEstoque = false;
    public int $estoqueMinimo = 0;
    public int $quantidadeEstoque = 0;
    public string $marca = '';
    public string $modelo = '';
    public string $codigoBalanca = '';
    public string $codigoInterno = '';
    public string $tags = '';
    public string $tipo = '';
    public string $ncm = '';
    public string $cfop = '';
    public string $origem = '';
    public string $cest = '';
    public string $categoriaPdv = '';
    public bool $botaoPdv = false;
    public string $categoriaLojaVirtual = '';
    public string $subcategoriaLojaVirtual = '';
    public string $nomeLojaVirtual = '';
    public float $precoDe = 0.0;
    public float $precoPor = 0.0;
    public float $alturaCm = 0.0;
    public float $larguraCm = 0.0;
    public float $profundidadeCm = 0.0;
    public float $pesoKg = 0.0;
    public string $descricaoProduto = '';
    public string $garantia = '';
    public string $itensInclusos = '';
    public string $especificacoes = '';

    /** Aliases mantidos para PDV/vendas existentes. */
    public string $codigo = '';
    public string $nome = '';
    public float $precoVenda = 0.0;
    public int $estoque = 0;

    public static function fromArray(array $row): self
    {
        $p = new self();
        $p->id = (int) ($row['id'] ?? 0);
        $p->codigoBarras = (string) ($row['codigo_barras'] ?? $row['codigo'] ?? '');
        $p->tipoProduto = (string) ($row['tipo_produto'] ?? 'Produto');
        $p->descricao = (string) ($row['descricao'] ?? $row['nome'] ?? '');
        $p->precoCusto = (float) ($row['preco_custo'] ?? 0);
        $p->precoVendaVarejo = (float) ($row['preco_venda_varejo'] ?? $row['preco_venda'] ?? 0);
        $p->precoVendaAtacado = (float) ($row['preco_venda_atacado'] ?? 0);
        $p->quantidadeMinimaAtacado = (int) ($row['quantidade_minima_atacado'] ?? 0);
        $p->unidade = (string) ($row['unidade'] ?? 'Unidade');
        $p->ativo = (bool) ($row['ativo'] ?? true);
        $p->categoriaProduto = (string) ($row['categoria_produto'] ?? '');
        $p->subcategoriaProduto = (string) ($row['subcategoria_produto'] ?? '');
        $p->movimentaEstoque = (bool) ($row['movimenta_estoque'] ?? false);
        $p->estoqueMinimo = (int) ($row['estoque_minimo'] ?? 0);
        $p->quantidadeEstoque = (int) ($row['quantidade_estoque'] ?? $row['estoque'] ?? 0);
        $p->marca = (string) ($row['marca'] ?? '');
        $p->modelo = (string) ($row['modelo'] ?? '');
        $p->codigoBalanca = (string) ($row['codigo_balanca'] ?? '');
        $p->codigoInterno = (string) ($row['codigo_interno'] ?? '');
        $p->tags = (string) ($row['tags'] ?? '');
        $p->tipo = (string) ($row['tipo'] ?? '');
        $p->ncm = (string) ($row['ncm'] ?? '');
        $p->cfop = (string) ($row['cfop'] ?? '');
        $p->origem = (string) ($row['origem'] ?? '');
        $p->cest = (string) ($row['cest'] ?? '');
        $p->categoriaPdv = (string) ($row['categoria_pdv'] ?? '');
        $p->botaoPdv = (bool) ($row['botao_pdv'] ?? false);
        $p->categoriaLojaVirtual = (string) ($row['categoria_loja_virtual'] ?? '');
        $p->subcategoriaLojaVirtual = (string) ($row['subcategoria_loja_virtual'] ?? '');
        $p->nomeLojaVirtual = (string) ($row['nome_loja_virtual'] ?? '');
        $p->precoDe = (float) ($row['preco_de'] ?? 0);
        $p->precoPor = (float) ($row['preco_por'] ?? 0);
        $p->alturaCm = (float) ($row['altura_cm'] ?? 0);
        $p->larguraCm = (float) ($row['largura_cm'] ?? 0);
        $p->profundidadeCm = (float) ($row['profundidade_cm'] ?? 0);
        $p->pesoKg = (float) ($row['peso_kg'] ?? 0);
        $p->descricaoProduto = (string) ($row['descricao_produto'] ?? '');
        $p->garantia = (string) ($row['garantia'] ?? '');
        $p->itensInclusos = (string) ($row['itens_inclusos'] ?? '');
        $p->especificacoes = (string) ($row['especificacoes'] ?? '');

        $p->codigo = $p->codigoBarras;
        $p->nome = $p->descricao;
        $p->precoVenda = $p->precoVendaVarejo;
        $p->estoque = $p->quantidadeEstoque;
        return $p;
    }
}
