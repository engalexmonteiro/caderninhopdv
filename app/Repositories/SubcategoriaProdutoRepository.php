<?php

namespace App\Repositories;

use App\Models\SubcategoriaProduto;
use PDO;

class SubcategoriaProdutoRepository
{
    private static bool $schemaChecked = false;

    public function __construct(private PDO $pdo)
    {
        $this->ensureSchema();
    }

    /** @return SubcategoriaProduto[] */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            "SELECT s.*, c.nome AS categoria_nome
             FROM subcategorias_produto s
             JOIN categorias_produto c ON c.id = s.categoria_id
             ORDER BY c.nome ASC, s.nome ASC"
        );
        return array_map([SubcategoriaProduto::class, 'fromArray'], $stmt->fetchAll());
    }

    /** @return SubcategoriaProduto[] */
    public function findAtivas(): array
    {
        $stmt = $this->pdo->query(
            "SELECT s.*, c.nome AS categoria_nome
             FROM subcategorias_produto s
             JOIN categorias_produto c ON c.id = s.categoria_id
             WHERE s.ativo = 1 AND c.ativo = 1
             ORDER BY c.nome ASC, s.nome ASC"
        );
        return array_map([SubcategoriaProduto::class, 'fromArray'], $stmt->fetchAll());
    }

    public function findById(int $id): ?SubcategoriaProduto
    {
        $stmt = $this->pdo->prepare(
            "SELECT s.*, c.nome AS categoria_nome
             FROM subcategorias_produto s
             JOIN categorias_produto c ON c.id = s.categoria_id
             WHERE s.id = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? SubcategoriaProduto::fromArray($row) : null;
    }

    public function nomeExiste(int $categoriaId, string $nome, int $exceptId = 0): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM subcategorias_produto WHERE categoria_id = ? AND nome = ? AND id != ?'
        );
        $stmt->execute([$categoriaId, $nome, $exceptId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function save(SubcategoriaProduto $subcategoria): int
    {
        if ($subcategoria->id > 0) {
            $this->pdo->prepare(
                'UPDATE subcategorias_produto SET categoria_id=?, nome=?, ativo=? WHERE id=?'
            )->execute([
                $subcategoria->categoriaId,
                $subcategoria->nome,
                $subcategoria->ativo ? 1 : 0,
                $subcategoria->id,
            ]);
            return $subcategoria->id;
        }

        $this->pdo->prepare(
            'INSERT INTO subcategorias_produto (categoria_id, nome, ativo) VALUES (?,?,?)'
        )->execute([
            $subcategoria->categoriaId,
            $subcategoria->nome,
            $subcategoria->ativo ? 1 : 0,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function toggle(int $id): void
    {
        $this->pdo->prepare('UPDATE subcategorias_produto SET ativo = NOT ativo WHERE id = ?')->execute([$id]);
    }

    private function ensureSchema(): void
    {
        if (self::$schemaChecked) {
            return;
        }
        self::$schemaChecked = true;

        new CategoriaProdutoRepository($this->pdo);

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS subcategorias_produto (
            id INT AUTO_INCREMENT PRIMARY KEY,
            categoria_id INT NOT NULL,
            nome VARCHAR(150) NOT NULL,
            ativo TINYINT(1) NOT NULL DEFAULT 1,
            criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_subcategoria_categoria_nome (categoria_id, nome),
            FOREIGN KEY (categoria_id) REFERENCES categorias_produto(id)
        )");

        $this->pdo->exec(
            "INSERT IGNORE INTO subcategorias_produto (categoria_id, nome, ativo)
             SELECT c.id, p.subcategoria_produto, 1
             FROM produtos p
             JOIN categorias_produto c ON c.nome = p.categoria_produto
             WHERE p.subcategoria_produto IS NOT NULL
               AND p.subcategoria_produto <> ''
               AND p.categoria_produto IS NOT NULL
               AND p.categoria_produto <> ''"
        );
    }
}
