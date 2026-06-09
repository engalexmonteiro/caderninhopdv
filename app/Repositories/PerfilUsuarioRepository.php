<?php

namespace App\Repositories;

use App\Models\PerfilUsuario;
use PDO;

class PerfilUsuarioRepository
{
    private static bool $schemaChecked = false;

    public function __construct(private PDO $pdo)
    {
        $this->ensureSchema();
    }

    /** @return PerfilUsuario[] */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM perfis_usuario ORDER BY nome ASC');
        return array_map([PerfilUsuario::class, 'fromArray'], $stmt->fetchAll());
    }

    /** @return PerfilUsuario[] */
    public function findAtivos(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM perfis_usuario WHERE ativo = 1 ORDER BY nome ASC');
        return array_map([PerfilUsuario::class, 'fromArray'], $stmt->fetchAll());
    }

    public function findById(int $id): ?PerfilUsuario
    {
        $stmt = $this->pdo->prepare('SELECT * FROM perfis_usuario WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? PerfilUsuario::fromArray($row) : null;
    }

    public function findByCodigo(string $codigo): ?PerfilUsuario
    {
        $stmt = $this->pdo->prepare('SELECT * FROM perfis_usuario WHERE codigo = ? LIMIT 1');
        $stmt->execute([$codigo]);
        $row = $stmt->fetch();
        return $row ? PerfilUsuario::fromArray($row) : null;
    }

    public function codigoExists(string $codigo, int $exceptId = 0): bool
    {
        $stmt = $this->pdo->prepare('SELECT id FROM perfis_usuario WHERE codigo = ? AND id != ?');
        $stmt->execute([$codigo, $exceptId]);
        return (bool) $stmt->fetch();
    }

    public function save(PerfilUsuario $perfil): PerfilUsuario
    {
        if ($perfil->id === 0) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO perfis_usuario (codigo,nome,ativo) VALUES (?,?,?)'
            );
            $stmt->execute([$perfil->codigo, $perfil->nome, $perfil->ativo ? 1 : 0]);
            $perfil->id = (int) $this->pdo->lastInsertId();
            return $perfil;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE perfis_usuario SET codigo=?,nome=?,ativo=? WHERE id=?'
        );
        $stmt->execute([$perfil->codigo, $perfil->nome, $perfil->ativo ? 1 : 0, $perfil->id]);
        return $perfil;
    }

    public function toggle(int $id): void
    {
        $this->pdo->prepare('UPDATE perfis_usuario SET ativo = NOT ativo WHERE id = ?')->execute([$id]);
    }

    private function ensureSchema(): void
    {
        if (self::$schemaChecked) {
            return;
        }
        self::$schemaChecked = true;

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS perfis_usuario (
            id INT AUTO_INCREMENT PRIMARY KEY,
            codigo VARCHAR(50) NOT NULL UNIQUE,
            nome VARCHAR(120) NOT NULL,
            ativo TINYINT(1) NOT NULL DEFAULT 1,
            criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $this->pdo->exec("ALTER TABLE usuarios MODIFY perfil VARCHAR(50) NOT NULL DEFAULT 'usuario'");

        $this->pdo->exec(
            "INSERT IGNORE INTO perfis_usuario (codigo, nome, ativo) VALUES
             ('admin', 'Administrador', 1),
             ('usuario', 'Usuario', 1)"
        );
    }
}
