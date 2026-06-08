<?php

namespace App\Repositories;

use App\Models\CategoriaProduto;
use PDO;

class CategoriaProdutoRepository
{
    private static bool $schemaChecked = false;

    public function __construct(private PDO $pdo)
    {
        $this->ensureSchema();
    }

    /** @return CategoriaProduto[] */
    public function findAll(): array
    {
        return array_map(
            [CategoriaProduto::class, 'fromArray'],
            $this->pdo->query('SELECT * FROM categorias_produto ORDER BY nome ASC')->fetchAll()
        );
    }

    /** @return CategoriaProduto[] */
    public function findAtivas(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM categorias_produto WHERE ativo = 1 ORDER BY nome ASC');
        return array_map([CategoriaProduto::class, 'fromArray'], $stmt->fetchAll());
    }

    public function findById(int $id): ?CategoriaProduto
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categorias_produto WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? CategoriaProduto::fromArray($row) : null;
    }

    public function findByNome(string $nome): ?CategoriaProduto
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categorias_produto WHERE nome = ?');
        $stmt->execute([$nome]);
        $row = $stmt->fetch();
        return $row ? CategoriaProduto::fromArray($row) : null;
    }

    public function nomeExiste(string $nome, int $exceptId = 0): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM categorias_produto WHERE nome = ? AND id != ?');
        $stmt->execute([$nome, $exceptId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function save(CategoriaProduto $categoria): int
    {
        if ($categoria->id > 0) {
            $this->pdo->prepare(
                'UPDATE categorias_produto SET nome=?, ativo=? WHERE id=?'
            )->execute([$categoria->nome, $categoria->ativo ? 1 : 0, $categoria->id]);
            return $categoria->id;
        }

        $this->pdo->prepare(
            'INSERT INTO categorias_produto (nome, ativo) VALUES (?,?)'
        )->execute([$categoria->nome, $categoria->ativo ? 1 : 0]);
        return (int) $this->pdo->lastInsertId();
    }

    public function toggle(int $id): void
    {
        $this->pdo->prepare('UPDATE categorias_produto SET ativo = NOT ativo WHERE id = ?')->execute([$id]);
    }

    private function ensureSchema(): void
    {
        if (self::$schemaChecked) {
            return;
        }
        self::$schemaChecked = true;

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS categorias_produto (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(150) NOT NULL UNIQUE,
            ativo TINYINT(1) NOT NULL DEFAULT 1,
            criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $this->pdo->exec(
            "INSERT IGNORE INTO categorias_produto (nome, ativo)
             SELECT DISTINCT categoria_produto, 1
             FROM produtos
             WHERE categoria_produto IS NOT NULL AND categoria_produto <> ''"
        );
    }
}
