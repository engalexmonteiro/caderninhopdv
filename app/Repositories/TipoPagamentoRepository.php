<?php

namespace App\Repositories;

use App\Models\TipoPagamento;
use PDO;

class TipoPagamentoRepository
{
    private static bool $schemaChecked = false;

    public function __construct(private PDO $pdo)
    {
        $this->ensureSchema();
    }

    /** @return TipoPagamento[] */
    public function findAll(): array
    {
        return array_map(
            [TipoPagamento::class, 'fromArray'],
            $this->pdo->query('SELECT * FROM tipos_pagamento ORDER BY ordem ASC, nome ASC')->fetchAll()
        );
    }

    /** @return TipoPagamento[] */
    public function findAtivos(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM tipos_pagamento WHERE ativo = 1 ORDER BY ordem ASC, nome ASC');
        return array_map([TipoPagamento::class, 'fromArray'], $stmt->fetchAll());
    }

    public function findById(int $id): ?TipoPagamento
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tipos_pagamento WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? TipoPagamento::fromArray($row) : null;
    }

    public function codigoExiste(string $codigo, int $exceptId = 0): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM tipos_pagamento WHERE codigo = ? AND id != ?');
        $stmt->execute([$codigo, $exceptId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function save(TipoPagamento $tipo): int
    {
        if ($tipo->id > 0) {
            $this->pdo->prepare(
                'UPDATE tipos_pagamento SET codigo=?, nome=?, ordem=?, ativo=? WHERE id=?'
            )->execute([
                $tipo->codigo, $tipo->nome, $tipo->ordem, $tipo->ativo ? 1 : 0, $tipo->id,
            ]);
            return $tipo->id;
        }

        $this->pdo->prepare(
            'INSERT INTO tipos_pagamento (codigo, nome, ordem, ativo) VALUES (?,?,?,?)'
        )->execute([
            $tipo->codigo, $tipo->nome, $tipo->ordem, $tipo->ativo ? 1 : 0,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function toggle(int $id): void
    {
        $this->pdo->prepare('UPDATE tipos_pagamento SET ativo = NOT ativo WHERE id = ?')->execute([$id]);
    }

    private function ensureSchema(): void
    {
        if (self::$schemaChecked) {
            return;
        }
        self::$schemaChecked = true;

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS tipos_pagamento (
            id INT AUTO_INCREMENT PRIMARY KEY,
            codigo VARCHAR(50) NOT NULL UNIQUE,
            nome VARCHAR(100) NOT NULL,
            ordem INT NOT NULL DEFAULT 0,
            ativo TINYINT(1) NOT NULL DEFAULT 1,
            criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $defaults = [
            ['dinheiro', 'Dinheiro', 10],
            ['cartao_credito', 'Cartão Crédito', 20],
            ['cartao_debito', 'Cartão Débito', 30],
            ['pix', 'PIX', 40],
        ];
        $stmt = $this->pdo->prepare(
            'INSERT IGNORE INTO tipos_pagamento (codigo, nome, ordem, ativo) VALUES (?,?,?,1)'
        );
        foreach ($defaults as $tipo) {
            $stmt->execute($tipo);
        }
    }
}
