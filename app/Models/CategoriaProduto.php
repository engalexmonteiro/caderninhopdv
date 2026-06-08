<?php

namespace App\Models;

class CategoriaProduto
{
    public int $id = 0;
    public string $nome = '';
    public bool $ativo = true;
    public string $criadoEm = '';

    public static function fromArray(array $row): self
    {
        $categoria = new self();
        $categoria->id = (int) ($row['id'] ?? 0);
        $categoria->nome = (string) ($row['nome'] ?? '');
        $categoria->ativo = (bool) ($row['ativo'] ?? true);
        $categoria->criadoEm = (string) ($row['criado_em'] ?? '');
        return $categoria;
    }
}
