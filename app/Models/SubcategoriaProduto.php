<?php

namespace App\Models;

class SubcategoriaProduto
{
    public int $id = 0;
    public int $categoriaId = 0;
    public string $categoriaNome = '';
    public string $nome = '';
    public bool $ativo = true;
    public string $criadoEm = '';

    public static function fromArray(array $row): self
    {
        $subcategoria = new self();
        $subcategoria->id = (int) ($row['id'] ?? 0);
        $subcategoria->categoriaId = (int) ($row['categoria_id'] ?? 0);
        $subcategoria->categoriaNome = (string) ($row['categoria_nome'] ?? '');
        $subcategoria->nome = (string) ($row['nome'] ?? '');
        $subcategoria->ativo = (bool) ($row['ativo'] ?? true);
        $subcategoria->criadoEm = (string) ($row['criado_em'] ?? '');
        return $subcategoria;
    }
}
