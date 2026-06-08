<?php

namespace App\Models;

class Usuario
{
    public int    $id     = 0;
    public string $nome   = '';
    public string $email  = '';
    public string $senha  = '';
    public string $perfil = 'usuario'; // 'admin' | 'usuario'
    public bool   $ativo  = true;

    public static function fromArray(array $row): self
    {
        $u         = new self();
        $u->id     = (int)   ($row['id']     ?? 0);
        $u->nome   = (string)($row['nome']   ?? '');
        $u->email  = (string)($row['email']  ?? '');
        $u->senha  = (string)($row['senha']  ?? '');
        $u->perfil = (string)($row['perfil'] ?? 'usuario');
        $u->ativo  = (bool)  ($row['ativo']  ?? true);
        return $u;
    }

    public function isAdmin(): bool
    {
        return $this->perfil === 'admin';
    }
}
