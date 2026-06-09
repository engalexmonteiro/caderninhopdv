<?php

namespace App\Models;

class Venda
{
    public int    $id             = 0;
    public int    $usuarioId      = 0;
    public string $usuarioNome    = '';
    public int    $empresaId      = 0;
    public int    $caixaId        = 0;
    public string $empresaNome    = '';
    public string $empresaFantasia = '';
    public string $empresaCnpj    = '';
    public string $empresaEmail   = '';
    public string $empresaTelefone = '';
    public string $empresaEndereco = '';
    public string $empresaCidade  = '';
    public string $empresaEstado  = '';
    public string $empresaCep     = '';
    public string $empresaLogomarca = '';
    public float  $total          = 0.0;
    public float  $desconto       = 0.0;
    public string $formaPagamento = 'dinheiro';
    public float  $valorPago      = 0.0;
    public float  $troco          = 0.0;
    public string $status         = 'concluida';
    public string $criadoEm       = '';

    /** @var VendaItem[] */
    public array $itens = [];

    /** @var VendaPagamento[] */
    public array $pagamentos = [];

    public static function fromArray(array $row): self
    {
        $v                 = new self();
        $v->id             = (int)   ($row['id']              ?? 0);
        $v->usuarioId      = (int)   ($row['usuario_id']      ?? 0);
        $v->usuarioNome    = (string)($row['usuario_nome']    ?? '');
        $v->empresaId      = (int)   ($row['empresa_id']      ?? 0);
        $v->caixaId        = (int)   ($row['caixa_id']        ?? 0);
        $v->empresaNome    = (string)($row['empresa_nome']    ?? '');
        $v->empresaFantasia = (string)($row['empresa_fantasia'] ?? '');
        $v->empresaCnpj    = (string)($row['empresa_cnpj']    ?? '');
        $v->empresaEmail   = (string)($row['empresa_email']   ?? '');
        $v->empresaTelefone = (string)($row['empresa_telefone'] ?? '');
        $v->empresaEndereco = (string)($row['empresa_endereco'] ?? '');
        $v->empresaCidade  = (string)($row['empresa_cidade']  ?? '');
        $v->empresaEstado  = (string)($row['empresa_estado']  ?? '');
        $v->empresaCep     = (string)($row['empresa_cep']     ?? '');
        $v->empresaLogomarca = (string)($row['empresa_logomarca'] ?? '');
        $v->total          = (float) ($row['total']           ?? 0);
        $v->desconto       = (float) ($row['desconto']        ?? 0);
        $v->formaPagamento = (string)($row['forma_pagamento'] ?? 'dinheiro');
        $v->valorPago      = (float) ($row['valor_pago']      ?? 0);
        $v->troco          = (float) ($row['troco']           ?? 0);
        $v->status         = (string)($row['status']          ?? 'concluida');
        $v->criadoEm       = (string)($row['criado_em']       ?? '');
        return $v;
    }

    public static function formaLabel(string $forma): string
    {
        if ($forma === 'combinado') {
            return 'Combinado';
        }

        if (!in_array($forma, ['dinheiro', 'cartao_credito', 'cartao_debito', 'pix'], true)) {
            return ucwords(str_replace('_', ' ', $forma));
        }

        return match ($forma) {
            'cartao_credito' => 'Cartão Crédito',
            'cartao_debito'  => 'Cartão Débito',
            'pix'            => 'PIX',
            default          => 'Dinheiro',
        };
    }
}
