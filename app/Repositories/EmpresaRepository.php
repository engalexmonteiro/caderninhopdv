<?php

namespace App\Repositories;

use App\Models\Empresa;
use PDO;

class EmpresaRepository
{
    public function __construct(private PDO $pdo) {}

    /** @return Empresa[] */
    public function findAll(): array
    {
        return array_map(
            [Empresa::class, 'fromArray'],
            $this->pdo->query("SELECT * FROM empresas ORDER BY razao_social")->fetchAll()
        );
    }

    /** @return Empresa[] */
    public function findAtivas(): array
    {
        return array_map(
            [Empresa::class, 'fromArray'],
            $this->pdo->query("SELECT * FROM empresas WHERE ativo = 1 ORDER BY razao_social")->fetchAll()
        );
    }

    public function findById(int $id): ?Empresa
    {
        $stmt = $this->pdo->prepare("SELECT * FROM empresas WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? Empresa::fromArray($row) : null;
    }

    public function save(Empresa $e): int
    {
        if ($e->id > 0) {
            $this->pdo->prepare(
                "UPDATE empresas
                 SET razao_social=?, nome_fantasia=?, cnpj=?, email=?, telefone=?,
                     endereco=?, cidade=?, estado=?, cep=?, logomarca=?, ativo=?
                 WHERE id=?"
            )->execute([
                $e->razaoSocial, $e->nomeFantasia, $e->cnpj, $e->email, $e->telefone,
                $e->endereco, $e->cidade, $e->estado, $e->cep, $e->logomarca,
                $e->ativo ? 1 : 0, $e->id,
            ]);
            return $e->id;
        }

        $this->pdo->prepare(
            "INSERT INTO empresas (razao_social, nome_fantasia, cnpj, email, telefone,
             endereco, cidade, estado, cep, logomarca, ativo)
             VALUES (?,?,?,?,?,?,?,?,?,?,?)"
        )->execute([
            $e->razaoSocial, $e->nomeFantasia, $e->cnpj, $e->email, $e->telefone,
            $e->endereco, $e->cidade, $e->estado, $e->cep, $e->logomarca,
            $e->ativo ? 1 : 0,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function toggle(int $id): void
    {
        $this->pdo->prepare("UPDATE empresas SET ativo = NOT ativo WHERE id = ?")->execute([$id]);
    }

    public function cnpjExiste(string $cnpj, int $exceptId = 0): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM empresas WHERE cnpj = ? AND id != ?");
        $stmt->execute([$cnpj, $exceptId]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
