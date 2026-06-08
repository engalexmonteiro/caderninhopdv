<?php

namespace App\Models;

class Empresa
{
    public int    $id           = 0;
    public string $razaoSocial  = '';
    public string $nomeFantasia = '';
    public string $cnpj         = '';
    public string $email        = '';
    public string $telefone     = '';
    public string $endereco     = '';
    public string $cidade       = '';
    public string $estado       = '';
    public string $cep          = '';
    public string $logomarca    = '';
    public bool   $ativo        = true;
    public string $criadoEm     = '';

    public static function fromArray(array $row): self
    {
        $e               = new self();
        $e->id           = (int)   ($row['id']            ?? 0);
        $e->razaoSocial  = (string)($row['razao_social']  ?? '');
        $e->nomeFantasia = (string)($row['nome_fantasia'] ?? '');
        $e->cnpj         = (string)($row['cnpj']          ?? '');
        $e->email        = (string)($row['email']         ?? '');
        $e->telefone     = (string)($row['telefone']      ?? '');
        $e->endereco     = (string)($row['endereco']      ?? '');
        $e->cidade       = (string)($row['cidade']        ?? '');
        $e->estado       = (string)($row['estado']        ?? '');
        $e->cep          = (string)($row['cep']           ?? '');
        $e->logomarca    = (string)($row['logomarca']     ?? '');
        $e->ativo        = (bool)  ($row['ativo']         ?? true);
        $e->criadoEm     = (string)($row['criado_em']     ?? '');
        return $e;
    }

    public function cnpjFormatado(): string
    {
        $c = preg_replace('/\D/', '', $this->cnpj);
        if (strlen($c) !== 14) return $this->cnpj;
        return substr($c, 0, 2) . '.' . substr($c, 2, 3) . '.' . substr($c, 5, 3)
             . '/' . substr($c, 8, 4) . '-' . substr($c, 12, 2);
    }

    public function nomeExibicao(): string
    {
        return $this->nomeFantasia !== '' ? $this->nomeFantasia : $this->razaoSocial;
    }
}
