<?php

namespace App\Models;

class Personalizacao
{
    public int $id = 1;
    public string $paleta = 'azul';
    public string $corPrimaria = '#0d6efd';
    public string $corSucesso = '#198754';
    public bool $modoNoturno = false;
    public int $empresaId = 0;
    public string $logoLogin = '';
    public string $favicon = '';

    public static function fromArray(array $row): self
    {
        $p = new self();
        $p->id = (int) ($row['id'] ?? 1);
        $p->paleta = (string) ($row['paleta'] ?? 'azul');
        $p->corPrimaria = (string) ($row['cor_primaria'] ?? '#0d6efd');
        $p->corSucesso = (string) ($row['cor_sucesso'] ?? '#198754');
        $p->modoNoturno = (bool) ($row['modo_noturno'] ?? false);
        $p->empresaId = (int) ($row['empresa_id'] ?? 0);
        $p->logoLogin = (string) ($row['logo_login'] ?? '');
        $p->favicon = (string) ($row['favicon'] ?? '');
        return $p;
    }
}
