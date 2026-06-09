<?php

namespace App\Models;

class PerfilUsuario
{
    public int $id = 0;
    public string $codigo = '';
    public string $nome = '';
    public bool $ativo = true;
    public string $criadoEm = '';

    public static function fromArray(array $row): self
    {
        $perfil = new self();
        $perfil->id = (int) ($row['id'] ?? 0);
        $perfil->codigo = (string) ($row['codigo'] ?? '');
        $perfil->nome = (string) ($row['nome'] ?? '');
        $perfil->ativo = (bool) ($row['ativo'] ?? true);
        $perfil->criadoEm = (string) ($row['criado_em'] ?? '');
        return $perfil;
    }
}
