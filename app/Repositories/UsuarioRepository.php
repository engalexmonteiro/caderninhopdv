<?php

namespace App\Repositories;

use App\Models\Usuario;
use PDO;

class UsuarioRepository
{
    public function __construct(private PDO $pdo) {}

    /** @return Usuario[] */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM usuarios ORDER BY nome ASC');
        return array_map([Usuario::class, 'fromArray'], $stmt->fetchAll());
    }

    public function findById(int $id): ?Usuario
    {
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? Usuario::fromArray($row) : null;
    }

    public function findByEmail(string $email): ?Usuario
    {
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ? Usuario::fromArray($row) : null;
    }

    public function emailExists(string $email, int $exceptId = 0): bool
    {
        $stmt = $this->pdo->prepare('SELECT id FROM usuarios WHERE email = ? AND id != ?');
        $stmt->execute([$email, $exceptId]);
        return (bool) $stmt->fetch();
    }

    public function save(Usuario $u): Usuario
    {
        if ($u->id === 0) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO usuarios (nome,email,senha,perfil,ativo) VALUES (?,?,?,?,?)'
            );
            $stmt->execute([$u->nome, $u->email, $u->senha, $u->perfil, $u->ativo ? 1 : 0]);
            $u->id = (int) $this->pdo->lastInsertId();
        } else {
            $stmt = $this->pdo->prepare(
                'UPDATE usuarios SET nome=?,email=?,perfil=?,ativo=? WHERE id=?'
            );
            $stmt->execute([$u->nome, $u->email, $u->perfil, $u->ativo ? 1 : 0, $u->id]);

            if ($u->senha !== '') {
                $this->pdo->prepare('UPDATE usuarios SET senha=? WHERE id=?')
                          ->execute([$u->senha, $u->id]);
            }
        }
        return $u;
    }

    public function toggle(int $id): void
    {
        $this->pdo->prepare('UPDATE usuarios SET ativo = NOT ativo WHERE id = ?')->execute([$id]);
    }

    public function countAtivos(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM usuarios WHERE ativo = 1')->fetchColumn();
    }
}
