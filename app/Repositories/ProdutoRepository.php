<?php

namespace App\Repositories;

use App\Models\Produto;
use PDO;

class ProdutoRepository
{
    private const COLUMNS = [
        'codigo_barras', 'tipo_produto', 'descricao', 'preco_custo', 'preco_venda_varejo',
        'preco_venda_atacado', 'quantidade_minima_atacado', 'unidade', 'ativo',
        'categoria_produto', 'subcategoria_produto', 'movimenta_estoque', 'estoque_minimo',
        'quantidade_estoque', 'marca', 'modelo', 'codigo_balanca', 'codigo_interno',
        'tags', 'tipo', 'ncm', 'cfop', 'origem', 'cest', 'categoria_pdv', 'botao_pdv',
        'categoria_loja_virtual', 'subcategoria_loja_virtual', 'nome_loja_virtual',
        'preco_de', 'preco_por', 'altura_cm', 'largura_cm', 'profundidade_cm', 'peso_kg',
        'descricao_produto', 'garantia', 'itens_inclusos', 'especificacoes',
    ];

    private static bool $schemaChecked = false;

    public function __construct(private PDO $pdo)
    {
        $this->ensureSchema();
    }

    /** @return Produto[] */
    public function findAll(?string $busca = null): array
    {
        $sql    = 'SELECT * FROM produtos';
        $params = [];

        if ($busca !== null && $busca !== '') {
            $sql .= ' WHERE (descricao LIKE ? OR codigo_barras LIKE ? OR categoria_produto LIKE ?)';
            $params = ["%$busca%", "%$busca%", "%$busca%"];
        }

        $sql .= ' ORDER BY descricao ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map([Produto::class, 'fromArray'], $stmt->fetchAll());
    }

    /** @return Produto[] */
    public function findAtivosComEstoque(): array
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM produtos
             WHERE ativo = 1 AND (movimenta_estoque = 0 OR quantidade_estoque > 0)
             ORDER BY descricao ASC'
        );
        return array_map([Produto::class, 'fromArray'], $stmt->fetchAll());
    }

    public function findById(int $id): ?Produto
    {
        $stmt = $this->pdo->prepare('SELECT * FROM produtos WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? Produto::fromArray($row) : null;
    }

    /** @return Produto[] ids => Produto */
    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare(
            "SELECT * FROM produtos WHERE id IN ($placeholders) AND ativo = 1"
        );
        $stmt->execute(array_values($ids));

        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $p = Produto::fromArray($row);
            $result[$p->id] = $p;
        }
        return $result;
    }

    public function findByCodigoBarras(string $codigoBarras): ?Produto
    {
        $stmt = $this->pdo->prepare('SELECT * FROM produtos WHERE codigo_barras = ?');
        $stmt->execute([$codigoBarras]);
        $row = $stmt->fetch();
        return $row ? Produto::fromArray($row) : null;
    }

    public function codigoExists(string $codigo, int $exceptId = 0): bool
    {
        return $this->codigoBarrasExists($codigo, $exceptId);
    }

    public function codigoBarrasExists(string $codigoBarras, int $exceptId = 0): bool
    {
        $stmt = $this->pdo->prepare('SELECT id FROM produtos WHERE codigo_barras = ? AND id != ?');
        $stmt->execute([$codigoBarras, $exceptId]);
        return (bool) $stmt->fetch();
    }

    public function save(Produto $p): Produto
    {
        $values = $this->values($p);

        if ($p->id === 0) {
            $columns = implode(',', self::COLUMNS);
            $placeholders = implode(',', array_fill(0, count(self::COLUMNS), '?'));
            $stmt = $this->pdo->prepare(
                "INSERT INTO produtos ($columns) VALUES ($placeholders)"
            );
            $stmt->execute($values);
            $p->id = (int) $this->pdo->lastInsertId();
        } else {
            $sets = implode(',', array_map(fn (string $col): string => "$col=?", self::COLUMNS));
            $stmt = $this->pdo->prepare(
                "UPDATE produtos SET $sets WHERE id=?"
            );
            $stmt->execute([...$values, $p->id]);
        }
        return $p;
    }

    public function toggle(int $id): void
    {
        $this->pdo->prepare('UPDATE produtos SET ativo = NOT ativo WHERE id = ?')->execute([$id]);
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM produtos WHERE id = ?')->execute([$id]);
    }

    public function countAtivos(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM produtos WHERE ativo = 1')->fetchColumn();
    }

    public function decrementarEstoque(int $id, int $qty): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE produtos
             SET quantidade_estoque = quantidade_estoque - ?
             WHERE id = ? AND quantidade_estoque >= ?'
        );
        $stmt->execute([$qty, $id, $qty]);
        return $stmt->rowCount() > 0;
    }

    private function values(Produto $p): array
    {
        return [
            $p->codigoBarras, $p->tipoProduto, $p->descricao, $p->precoCusto,
            $p->precoVendaVarejo, $p->precoVendaAtacado, $p->quantidadeMinimaAtacado,
            $p->unidade, $p->ativo ? 1 : 0, $p->categoriaProduto, $p->subcategoriaProduto,
            $p->movimentaEstoque ? 1 : 0, $p->estoqueMinimo, $p->quantidadeEstoque,
            $p->marca, $p->modelo, $p->codigoBalanca, $p->codigoInterno, $p->tags,
            $p->tipo, $p->ncm, $p->cfop, $p->origem, $p->cest, $p->categoriaPdv,
            $p->botaoPdv ? 1 : 0, $p->categoriaLojaVirtual, $p->subcategoriaLojaVirtual,
            $p->nomeLojaVirtual, $p->precoDe, $p->precoPor, $p->alturaCm, $p->larguraCm,
            $p->profundidadeCm, $p->pesoKg, $p->descricaoProduto, $p->garantia,
            $p->itensInclusos, $p->especificacoes,
        ];
    }

    private function ensureSchema(): void
    {
        if (self::$schemaChecked) {
            return;
        }
        self::$schemaChecked = true;

        $existing = [];
        foreach ($this->pdo->query('SHOW COLUMNS FROM produtos')->fetchAll() as $column) {
            $existing[$column['Field']] = true;
        }

        $definitions = [
            'codigo_barras' => "VARCHAR(50) NOT NULL DEFAULT ''",
            'tipo_produto' => "VARCHAR(50) NOT NULL DEFAULT 'Produto'",
            'preco_venda_varejo' => 'DECIMAL(10,2) NOT NULL DEFAULT 0.00',
            'preco_venda_atacado' => 'DECIMAL(10,2) NOT NULL DEFAULT 0.00',
            'quantidade_minima_atacado' => 'INT NOT NULL DEFAULT 0',
            'categoria_produto' => "VARCHAR(150) NOT NULL DEFAULT ''",
            'subcategoria_produto' => "VARCHAR(150) NOT NULL DEFAULT ''",
            'movimenta_estoque' => 'TINYINT(1) NOT NULL DEFAULT 0',
            'estoque_minimo' => 'INT NOT NULL DEFAULT 0',
            'quantidade_estoque' => 'INT NOT NULL DEFAULT 0',
            'marca' => "VARCHAR(100) NOT NULL DEFAULT ''",
            'modelo' => "VARCHAR(100) NOT NULL DEFAULT ''",
            'codigo_balanca' => "VARCHAR(50) NOT NULL DEFAULT ''",
            'codigo_interno' => "VARCHAR(50) NOT NULL DEFAULT ''",
            'tags' => 'TEXT NULL',
            'tipo' => "VARCHAR(80) NOT NULL DEFAULT ''",
            'ncm' => "VARCHAR(20) NOT NULL DEFAULT ''",
            'cfop' => "VARCHAR(20) NOT NULL DEFAULT ''",
            'origem' => "VARCHAR(80) NOT NULL DEFAULT ''",
            'cest' => "VARCHAR(20) NOT NULL DEFAULT ''",
            'categoria_pdv' => "VARCHAR(150) NOT NULL DEFAULT ''",
            'botao_pdv' => 'TINYINT(1) NOT NULL DEFAULT 0',
            'categoria_loja_virtual' => "VARCHAR(150) NOT NULL DEFAULT ''",
            'subcategoria_loja_virtual' => "VARCHAR(150) NOT NULL DEFAULT ''",
            'nome_loja_virtual' => "VARCHAR(200) NOT NULL DEFAULT ''",
            'preco_de' => 'DECIMAL(10,2) NOT NULL DEFAULT 0.00',
            'preco_por' => 'DECIMAL(10,2) NOT NULL DEFAULT 0.00',
            'altura_cm' => 'DECIMAL(10,2) NOT NULL DEFAULT 0.00',
            'largura_cm' => 'DECIMAL(10,2) NOT NULL DEFAULT 0.00',
            'profundidade_cm' => 'DECIMAL(10,2) NOT NULL DEFAULT 0.00',
            'peso_kg' => 'DECIMAL(10,3) NOT NULL DEFAULT 0.000',
            'descricao_produto' => 'TEXT NULL',
            'garantia' => 'TEXT NULL',
            'itens_inclusos' => 'TEXT NULL',
            'especificacoes' => 'TEXT NULL',
        ];

        foreach ($definitions as $column => $definition) {
            if (!isset($existing[$column])) {
                $this->pdo->exec("ALTER TABLE produtos ADD COLUMN $column $definition");
            }
        }

        if (isset($existing['codigo'])) {
            $this->pdo->exec("UPDATE produtos SET codigo_barras = codigo WHERE codigo_barras = ''");
        }
        if (isset($existing['preco_venda'])) {
            $this->pdo->exec('UPDATE produtos SET preco_venda_varejo = preco_venda WHERE preco_venda_varejo = 0');
        }
        if (isset($existing['estoque'])) {
            $this->pdo->exec('UPDATE produtos SET quantidade_estoque = estoque WHERE quantidade_estoque = 0');
        }
        if (isset($existing['nome'])) {
            $this->pdo->exec("UPDATE produtos SET descricao_produto = descricao WHERE descricao_produto IS NULL OR descricao_produto = ''");
            $this->pdo->exec("UPDATE produtos SET descricao = nome WHERE nome != ''");
        }
    }
}
