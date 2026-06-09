<?php

namespace App\Repositories;

use App\Models\FormaPagamento;
use PDO;

class FormaPagamentoRepository
{
    private static bool $schemaChecked = false;

    public function __construct(private PDO $pdo)
    {
        $this->ensureSchema();
    }

    /** @return FormaPagamento[] */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            "SELECT f.*, t.codigo AS tipo_codigo, t.nome AS tipo_nome
             FROM formas_pagamento f
             JOIN tipos_pagamento t ON t.id = f.tipo_pagamento_id
             ORDER BY t.ordem ASC, f.ordem ASC, f.parcelas ASC, f.nome ASC"
        );
        return array_map([FormaPagamento::class, 'fromArray'], $stmt->fetchAll());
    }

    /** @return FormaPagamento[] */
    public function findAtivas(): array
    {
        $stmt = $this->pdo->query(
            "SELECT f.*, t.codigo AS tipo_codigo, t.nome AS tipo_nome
             FROM formas_pagamento f
             JOIN tipos_pagamento t ON t.id = f.tipo_pagamento_id
             WHERE f.ativo = 1 AND t.ativo = 1
             ORDER BY t.ordem ASC, f.ordem ASC, f.parcelas ASC, f.nome ASC"
        );
        return array_map([FormaPagamento::class, 'fromArray'], $stmt->fetchAll());
    }

    public function findById(int $id): ?FormaPagamento
    {
        $stmt = $this->pdo->prepare(
            "SELECT f.*, t.codigo AS tipo_codigo, t.nome AS tipo_nome
             FROM formas_pagamento f
             JOIN tipos_pagamento t ON t.id = f.tipo_pagamento_id
             WHERE f.id = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? FormaPagamento::fromArray($row) : null;
    }

    public function findByCodigo(string $codigo): ?FormaPagamento
    {
        $stmt = $this->pdo->prepare('SELECT * FROM formas_pagamento WHERE codigo = ? LIMIT 1');
        $stmt->execute([$codigo]);
        $row = $stmt->fetch();
        return $row ? FormaPagamento::fromArray($row) : null;
    }

    public function codigoExiste(string $codigo, int $exceptId = 0): bool
    {
        $stmt = $this->pdo->prepare('SELECT id FROM formas_pagamento WHERE codigo = ? AND id != ?');
        $stmt->execute([$codigo, $exceptId]);
        return (bool) $stmt->fetch();
    }

    public function save(FormaPagamento $forma): int
    {
        if ($forma->id > 0) {
            $this->pdo->prepare(
                'UPDATE formas_pagamento SET tipo_pagamento_id=?, codigo=?, nome=?, parcelas=?, ordem=?, ativo=? WHERE id=?'
            )->execute([
                $forma->tipoPagamentoId, $forma->codigo, $forma->nome, $forma->parcelas,
                $forma->ordem, $forma->ativo ? 1 : 0, $forma->id,
            ]);
            return $forma->id;
        }

        $this->pdo->prepare(
            'INSERT INTO formas_pagamento (tipo_pagamento_id, codigo, nome, parcelas, ordem, ativo) VALUES (?,?,?,?,?,?)'
        )->execute([
            $forma->tipoPagamentoId, $forma->codigo, $forma->nome, $forma->parcelas,
            $forma->ordem, $forma->ativo ? 1 : 0,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function toggle(int $id): void
    {
        $this->pdo->prepare('UPDATE formas_pagamento SET ativo = NOT ativo WHERE id = ?')->execute([$id]);
    }

    private function ensureSchema(): void
    {
        if (self::$schemaChecked) {
            return;
        }
        self::$schemaChecked = true;

        new TipoPagamentoRepository($this->pdo);

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS formas_pagamento (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tipo_pagamento_id INT NOT NULL,
            codigo VARCHAR(80) NOT NULL UNIQUE,
            nome VARCHAR(150) NOT NULL,
            parcelas INT NOT NULL DEFAULT 1,
            ordem INT NOT NULL DEFAULT 0,
            ativo TINYINT(1) NOT NULL DEFAULT 1,
            criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tipo_pagamento_id) REFERENCES tipos_pagamento(id)
        )");

        $this->pdo->exec(
            "INSERT IGNORE INTO formas_pagamento (tipo_pagamento_id, codigo, nome, parcelas, ordem, ativo)
             SELECT id, codigo, nome, 1, ordem, ativo FROM tipos_pagamento"
        );
    }
}
