<?php

namespace App\Models;

class VendaItem
{
    public int    $produtoId     = 0;
    public string $produtoNome   = '';
    public int    $quantidade    = 1;
    public float  $precoUnitario = 0.0;
    public float  $subtotal      = 0.0;
}
