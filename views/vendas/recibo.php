<?php
$empresaNome = $venda->empresaFantasia !== '' ? $venda->empresaFantasia : ($venda->empresaNome !== '' ? $venda->empresaNome : 'PDV Sistema');
$empresaRazao = $venda->empresaNome !== '' && $venda->empresaNome !== $empresaNome ? $venda->empresaNome : '';
$empresaDoc = $venda->empresaCnpj !== '' ? formatCnpj($venda->empresaCnpj) : '';
$empresaCidadeUf = trim($venda->empresaCidade . ($venda->empresaEstado !== '' ? ' / ' . $venda->empresaEstado : ''));
$empresaCep = $venda->empresaCep !== '' ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', preg_replace('/\D/', '', $venda->empresaCep)) : '';
$logoPath = $venda->empresaLogomarca !== '' ? BASE_PATH . '/public/assets/logos/' . $venda->empresaLogomarca : '';
$logoUrl = $venda->empresaLogomarca !== '' && is_file($logoPath) ? BASE_URL . '/assets/logos/' . rawurlencode($venda->empresaLogomarca) : '';
$subtotal = array_sum(array_map(fn ($item) => $item->subtotal, $venda->itens));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recibo #<?= $venda->id ?></title>
<style>
@page {
    size: 58mm 200mm;
    margin: 0;
}
* {
    box-sizing: border-box;
}
body {
    margin: 0;
    background: #fff;
    color: #000;
    font-family: "Consolas", "Courier New", monospace;
    font-size: 10px;
    line-height: 1.25;
}
.receipt {
    width: 58mm;
    padding: 3mm;
}
.center { text-align: center; }
.logo {
    display: block;
    max-width: 34mm;
    max-height: 16mm;
    margin: 0 auto 3px;
    object-fit: contain;
}
.company-name {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}
.company-line {
    overflow-wrap: anywhere;
}
.right { text-align: right; }
.bold { font-weight: 700; }
.muted { color: #333; }
.line {
    border-top: 1px dashed #000;
    margin: 5px 0;
}
.row {
    display: flex;
    justify-content: space-between;
    gap: 4px;
}
.item {
    margin-bottom: 5px;
}
.item-name {
    font-weight: 700;
    overflow-wrap: anywhere;
}
.no-print {
    padding: 8px;
    width: 58mm;
}
.btn {
    width: 100%;
    border: 1px solid #000;
    background: #fff;
    padding: 8px;
    font-family: Arial, sans-serif;
    font-size: 12px;
    cursor: pointer;
}
@media print {
    .no-print { display: none; }
    body { width: 58mm; }
}
</style>
</head>
<body>
<div class="receipt">
    <?php if ($logoUrl !== ''): ?>
    <img src="<?= e($logoUrl) ?>" alt="Logomarca" class="logo">
    <?php endif; ?>
    <div class="center company-name"><?= e($empresaNome) ?></div>
    <?php if ($empresaRazao !== ''): ?>
    <div class="center company-line"><?= e($empresaRazao) ?></div>
    <?php endif; ?>
    <?php if ($empresaDoc !== ''): ?>
    <div class="center">CNPJ: <?= e($empresaDoc) ?></div>
    <?php endif; ?>
    <?php if ($venda->empresaEndereco !== ''): ?>
    <div class="center company-line"><?= e($venda->empresaEndereco) ?></div>
    <?php endif; ?>
    <?php if ($empresaCidadeUf !== '' || $empresaCep !== ''): ?>
    <div class="center company-line">
        <?= e($empresaCidadeUf) ?><?= $empresaCidadeUf !== '' && $empresaCep !== '' ? ' - ' : '' ?><?= e($empresaCep) ?>
    </div>
    <?php endif; ?>
    <?php if ($venda->empresaTelefone !== ''): ?>
    <div class="center">Tel: <?= e($venda->empresaTelefone) ?></div>
    <?php endif; ?>
    <?php if ($venda->empresaEmail !== ''): ?>
    <div class="center company-line"><?= e($venda->empresaEmail) ?></div>
    <?php endif; ?>
    <div class="center">RECIBO FISCAL</div>
    <div class="line"></div>

    <div class="row"><span>Venda</span><span>#<?= $venda->id ?></span></div>
    <div class="row"><span>Data</span><span><?= date('d/m/Y H:i', strtotime($venda->criadoEm)) ?></span></div>
    <div class="row"><span>Operador</span><span><?= e($venda->usuarioNome) ?></span></div>
    <?php if ($venda->caixaId > 0): ?>
    <div class="row"><span>Caixa</span><span>#<?= $venda->caixaId ?></span></div>
    <?php endif; ?>

    <div class="line"></div>
    <div class="bold">ITENS</div>
    <?php foreach ($venda->itens as $item): ?>
    <div class="item">
        <div class="item-name"><?= e($item->produtoNome) ?></div>
        <?php if ($item->produtoCodigo !== ''): ?>
        <div class="muted">Cod: <?= e($item->produtoCodigo) ?></div>
        <?php endif; ?>
        <div class="row">
            <span><?= $item->quantidade ?> x <?= money($item->precoUnitario) ?></span>
            <span><?= money($item->subtotal) ?></span>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="line"></div>
    <div class="row"><span>Subtotal</span><span><?= money($subtotal) ?></span></div>
    <div class="row"><span>Desconto</span><span><?= money($venda->desconto) ?></span></div>
    <div class="row bold"><span>TOTAL</span><span><?= money($venda->total) ?></span></div>
    <div class="row"><span>Pagamento</span><span><?= e(App\Models\Venda::formaLabel($venda->formaPagamento)) ?></span></div>
    <?php if ($venda->formaPagamento === 'dinheiro'): ?>
    <div class="row"><span>Valor pago</span><span><?= money($venda->valorPago) ?></span></div>
    <div class="row"><span>Troco</span><span><?= money($venda->troco) ?></span></div>
    <?php endif; ?>

    <div class="line"></div>
    <div class="center">Obrigado pela preferência!</div>
    <div class="center muted">Documento emitido pelo PDV</div>
</div>

<div class="no-print">
    <button class="btn" onclick="window.print()">Imprimir / salvar PDF</button>
</div>

<script>
window.addEventListener('load', () => {
    setTimeout(() => window.print(), 250);
});
</script>
</body>
</html>
