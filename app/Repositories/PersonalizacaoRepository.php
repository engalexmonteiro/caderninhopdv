<?php

namespace App\Repositories;

use App\Models\Personalizacao;
use PDO;

class PersonalizacaoRepository
{
    private static bool $schemaChecked = false;

    public function __construct(private PDO $pdo)
    {
        $this->ensureSchema();
    }

    public function get(): Personalizacao
    {
        $stmt = $this->pdo->query('SELECT * FROM personalizacao WHERE id = 1 LIMIT 1');
        $row = $stmt->fetch();
        return $row ? Personalizacao::fromArray($row) : new Personalizacao();
    }

    public function save(Personalizacao $p): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE personalizacao SET paleta=?, cor_primaria=?, cor_sucesso=?, modo_noturno=?, empresa_id=?, logo_login=?, favicon=? WHERE id=1'
        );
        $stmt->execute([$p->paleta, $p->corPrimaria, $p->corSucesso, $p->modoNoturno ? 1 : 0, $p->empresaId ?: null, $p->logoLogin, $p->favicon]);
    }

    private function ensureSchema(): void
    {
        if (self::$schemaChecked) {
            return;
        }
        self::$schemaChecked = true;

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS personalizacao (
            id INT PRIMARY KEY,
            paleta VARCHAR(30) NOT NULL DEFAULT 'azul',
            cor_primaria VARCHAR(7) NOT NULL DEFAULT '#0d6efd',
            cor_sucesso VARCHAR(7) NOT NULL DEFAULT '#198754',
            modo_noturno TINYINT(1) NOT NULL DEFAULT 0,
            empresa_id INT NULL,
            logo_login VARCHAR(255) NOT NULL DEFAULT '',
            favicon VARCHAR(255) NOT NULL DEFAULT '',
            atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");

        $existing = [];
        foreach ($this->pdo->query('SHOW COLUMNS FROM personalizacao')->fetchAll() as $column) {
            $existing[$column['Field']] = true;
        }

        if (!isset($existing['cor_primaria'])) {
            $this->pdo->exec("ALTER TABLE personalizacao ADD COLUMN cor_primaria VARCHAR(7) NOT NULL DEFAULT '#0d6efd' AFTER paleta");
        }
        if (!isset($existing['cor_sucesso'])) {
            $this->pdo->exec("ALTER TABLE personalizacao ADD COLUMN cor_sucesso VARCHAR(7) NOT NULL DEFAULT '#198754' AFTER cor_primaria");
        }
        if (!isset($existing['modo_noturno'])) {
            $this->pdo->exec("ALTER TABLE personalizacao ADD COLUMN modo_noturno TINYINT(1) NOT NULL DEFAULT 0 AFTER cor_sucesso");
        }

        $this->pdo->exec(
            "INSERT IGNORE INTO personalizacao (id, paleta, cor_primaria, cor_sucesso, modo_noturno, empresa_id, logo_login, favicon)
             VALUES (1, 'azul', '#0d6efd', '#198754', 0, NULL, '', '')"
        );
    }
}
