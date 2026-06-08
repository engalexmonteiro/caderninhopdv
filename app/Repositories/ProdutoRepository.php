<?php

namespace App\Repositories;

use App\Models\Produto;
use PDO;

class ProdutoRepository
{
    public function __construct(private PDO $pdo) {}

    /** @return Produto[] */
    public function findAll(?string $busca = null): array
    {
        $sql    = 'SELECT * FROM produtos';
        $params = [];

        if ($busca !== null && $busca !== '') {
            $sql   .= ' WHERE (nome LIKE ? OR codigo LIKE ?)';
            $params = ["%$busca%", "%$busca%"];
        }

        $sql .= ' ORDER BY nome ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map([Produto::class, 'fromArray'], $stmt->fetchAll());
    }

    /** @return Produto[] */
    public function findAtivosComEstoque(): array
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM produtos WHERE ativo = 1 AND estoque > 0 ORDER BY nome ASC'
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

    public function codigoExists(string $codigo, int $exceptId = 0): bool
    {
        $stmt = $this->pdo->prepare('SELECT id FROM produtos WHERE codigo = ? AND id != ?');
        $stmt->execute([$codigo, $exceptId]);
        return (bool) $stmt->fetch();
    }

    public function save(Produto $p): Produto
    {
        if ($p->id === 0) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO produtos (codigo,nome,descricao,preco_custo,preco_venda,estoque,unidade,ativo)
                 VALUES (?,?,?,?,?,?,?,?)'
            );
            $stmt->execute([
                $p->codigo, $p->nome, $p->descricao,
                $p->precoCusto, $p->precoVenda, $p->estoque, $p->unidade, $p->ativo ? 1 : 0,
            ]);
            $p->id = (int) $this->pdo->lastInsertId();
        } else {
            $stmt = $this->pdo->prepare(
                'UPDATE produtos SET codigo=?,nome=?,descricao=?,preco_custo=?,preco_venda=?,estoque=?,unidade=?,ativo=?
                 WHERE id=?'
            );
            $stmt->execute([
                $p->codigo, $p->nome, $p->descricao,
                $p->precoCusto, $p->precoVenda, $p->estoque, $p->unidade, $p->ativo ? 1 : 0,
                $p->id,
            ]);
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
            'UPDATE produtos SET estoque = estoque - ? WHERE id = ? AND estoque >= ?'
        );
        $stmt->execute([$qty, $id, $qty]);
        return $stmt->rowCount() > 0;
    }
}
