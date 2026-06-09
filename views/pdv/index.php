<?php
$caixaAberto = $caixa !== null;
$saldoEsperado = $caixaResumo['saldo_esperado'] ?? 0.0;
$categoriasPdv = [];

foreach ($produtos as $produto) {
    $categoria = trim($produto->categoriaProduto) !== '' ? trim($produto->categoriaProduto) : 'Sem categoria';
    $subcategoria = trim($produto->subcategoriaProduto) !== '' ? trim($produto->subcategoriaProduto) : 'Sem subcategoria';

    if (!isset($categoriasPdv[$categoria])) {
        $categoriasPdv[$categoria] = [
            'total' => 0,
            'subcategorias' => [],
        ];
    }

    $categoriasPdv[$categoria]['total']++;
    $categoriasPdv[$categoria]['subcategorias'][$subcategoria] =
        ($categoriasPdv[$categoria]['subcategorias'][$subcategoria] ?? 0) + 1;
}
?>

<div class="container-fluid pt-3 px-3">
    <?php if (!empty($caixaFlash)): ?>
    <div class="alert alert-info alert-dismissible fade show mb-3">
        <i class="bi bi-info-circle me-2"></i><?= e($caixaFlash) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <ul class="nav nav-tabs mb-3" id="pdvTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pdv-venda-tab" data-bs-toggle="tab" data-bs-target="#pdv-venda" type="button" role="tab">
                <i class="bi bi-cart3 me-1"></i>Venda
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pdv-caixa-tab" data-bs-toggle="tab" data-bs-target="#pdv-caixa" type="button" role="tab">
                <i class="bi bi-cash-register me-1"></i>Caixa
            </button>
        </li>
    </ul>

    <div class="tab-content" id="pdvTabsContent">
        <div class="tab-pane fade" id="pdv-caixa" role="tabpanel" aria-labelledby="pdv-caixa-tab">

    <?php if ($caixaAberto): ?>
    <div class="card mb-3">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="small text-muted">Caixa aberto</div>
                    <div class="fw-bold">
                        <i class="bi bi-cash-register text-success me-1"></i>
                        #<?= $caixa->id ?> desde <?= e($caixa->abertoEm) ?>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-3">
                    <div>
                        <div class="small text-muted">Fundo inicial</div>
                        <div class="fw-semibold"><?= money($caixa->fundoInicial) ?></div>
                    </div>
                    <div>
                        <div class="small text-muted">Vendas em dinheiro</div>
                        <div class="fw-semibold"><?= money($caixaResumo['vendas_dinheiro'] ?? 0) ?></div>
                    </div>
                    <div>
                        <div class="small text-muted">Saldo esperado</div>
                        <div class="fw-bold text-success"><?= money($saldoEsperado) ?></div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalSangria">
                        <i class="bi bi-arrow-down-circle me-1"></i>Sangria
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalReforco">
                        <i class="bi bi-arrow-up-circle me-1"></i>Reforço
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalFecharCaixa">
                        <i class="bi bi-lock me-1"></i>Fechar Caixa
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-warning d-flex justify-content-between align-items-center mb-3">
        <div><i class="bi bi-lock me-2"></i>Abra o caixa para iniciar as vendas.</div>
        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalAbrirCaixa">
            <i class="bi bi-unlock me-1"></i>Abrir Caixa
        </button>
    </div>
    <?php endif; ?>

        </div>

        <div class="tab-pane fade show active" id="pdv-venda" role="tabpanel" aria-labelledby="pdv-venda-tab">
<div class="pdv-wrapper <?= $caixaAberto ? '' : 'pdv-locked' ?>">

    <!-- Painel de Produtos -->
    <div class="pdv-products">
        <div class="mb-3">
            <input type="text" id="searchProduto" class="form-control shadow-sm"
                   placeholder="&#xF52A;  Buscar produto por nome ou código..." autofocus>
        </div>
        <?php if (!empty($categoriasPdv)): ?>
        <div class="pdv-filter-section mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="fw-semibold text-muted small text-uppercase">Categorias</div>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearProductFilters">
                    <i class="bi bi-grid-3x3-gap me-1"></i>Todas
                </button>
            </div>
            <div class="row g-2" id="categoryCards">
                <?php foreach ($categoriasPdv as $categoria => $grupo): ?>
                <div class="col-6 col-md-4 col-xl-3">
                    <button type="button" class="card pdv-filter-card w-100 text-start"
                            data-category="<?= e($categoria) ?>">
                        <span class="fw-bold d-block"><?= e($categoria) ?></span>
                        <span class="text-muted small"><?= $grupo['total'] ?> produto(s)</span>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="pdv-filter-section mb-3 d-none" id="subcategorySection">
            <div class="fw-semibold text-muted small text-uppercase mb-2">Subcategorias</div>
            <div class="row g-2" id="subcategoryCards">
                <?php foreach ($categoriasPdv as $categoria => $grupo): ?>
                    <?php foreach ($grupo['subcategorias'] as $subcategoria => $total): ?>
                    <div class="col-6 col-md-4 col-xl-3 subcategory-item"
                         data-category="<?= e($categoria) ?>">
                        <button type="button" class="card pdv-filter-card w-100 text-start"
                                data-category="<?= e($categoria) ?>"
                                data-subcategory="<?= e($subcategoria) ?>">
                            <span class="fw-bold d-block"><?= e($subcategoria) ?></span>
                            <span class="text-muted small"><?= $total ?> produto(s)</span>
                        </button>
                    </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="row g-2" id="productGrid">
            <?php foreach ($produtos as $p): ?>
            <div class="col-6 col-md-4 col-xl-3 product-item"
                 data-nome="<?= strtolower(e($p->nome)) ?>"
                 data-codigo="<?= strtolower(e($p->codigo)) ?>"
                 data-categoria="<?= e(trim($p->categoriaProduto) !== '' ? trim($p->categoriaProduto) : 'Sem categoria') ?>"
                 data-subcategoria="<?= e(trim($p->subcategoriaProduto) !== '' ? trim($p->subcategoriaProduto) : 'Sem subcategoria') ?>">
                <div class="card product-card h-100 shadow-sm"
                     role="button"
                     data-id="<?= $p->id ?>"
                     data-nome="<?= e($p->nome) ?>"
                     data-preco="<?= number_format($p->precoVenda, 2, '.', '') ?>"
                     data-estoque="<?= $p->estoque ?>"
                     data-movimenta-estoque="<?= $p->movimentaEstoque ? '1' : '0' ?>">
                    <div class="card-body p-3">
                        <div class="text-muted small mb-1"><code><?= e($p->codigo) ?></code></div>
                        <div class="fw-semibold mb-2" style="font-size:.95rem;line-height:1.3">
                            <?= e($p->nome) ?>
                        </div>
                        <div class="product-price"><?= money($p->precoVenda) ?></div>
                        <div class="product-stock mt-1">
                            <i class="bi bi-box me-1"></i><?= $p->movimentaEstoque ? 'Estoque: ' . $p->estoque : 'Sem controle de estoque' ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($produtos)): ?>
            <div class="col-12 text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>Nenhum produto disponível.
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Carrinho -->
    <div class="pdv-cart">
        <div class="pdv-cart-header">
            <i class="bi bi-cart3 me-2"></i>Carrinho
            <button class="btn btn-sm btn-outline-light float-end" onclick="clearCart()" title="Limpar carrinho">
                <i class="bi bi-trash3"></i>
            </button>
        </div>

        <?php if (!empty($empresas)): ?>
        <div class="px-3 pt-3 pb-1 border-bottom bg-light">
            <label class="form-label small fw-semibold mb-1">
                <i class="bi bi-building me-1"></i>Empresa / CNPJ
            </label>
            <select id="empresaId" class="form-select form-select-sm">
                <option value="0">— Selecione a empresa —</option>
                <?php foreach ($empresas as $emp): ?>
                <option value="<?= $emp->id ?>">
                    <?= e($emp->nomeExibicao()) ?> — <?= e($emp->cnpjFormatado()) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php else: ?>
        <input type="hidden" id="empresaId" value="0">
        <?php endif; ?>

        <div class="pdv-cart-items" id="cartItems">
            <div id="emptyMsg" class="text-center text-muted py-5">
                <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
                Clique em um produto para adicionar
            </div>
        </div>

        <div class="pdv-cart-footer">
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <label class="form-label small fw-semibold mb-1">Desconto (R$)</label>
                    <input type="number" id="desconto" class="form-control form-control-sm"
                           min="0" step="0.01" value="0" oninput="updateTotal()">
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold mb-1">Forma de Pagamento</label>
                    <select id="formaPagamento" class="form-select form-select-sm" onchange="toggleDinheiro()">
                        <?php foreach ($tiposPagamento ?? [] as $tipo): ?>
                        <option value="<?= e($tipo->codigo) ?>" data-dinheiro="<?= str_starts_with($tipo->codigo, 'dinheiro') ? '1' : '0' ?>"><?= e($tipo->nome) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="text-muted">Pagamento combinado</small>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnTogglePagamentos" onclick="togglePagamentosCombinados()">
                    <i class="bi bi-plus-circle me-1"></i>Combinar
                </button>
            </div>

            <div id="pagamentosCombinados" class="mb-2 d-none">
                <div id="pagamentosRows" class="d-grid gap-2"></div>
                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mt-2" onclick="addPagamentoRow()">
                    <i class="bi bi-plus-lg me-1"></i>Adicionar pagamento
                </button>
                <div class="d-flex justify-content-between mt-2">
                    <small class="text-muted">Pago:</small>
                    <small class="fw-bold" id="pagamentosTotalDisplay">R$ 0,00</small>
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">Restante:</small>
                    <small class="fw-bold text-danger" id="pagamentosRestanteDisplay">R$ 0,00</small>
                </div>
            </div>

            <div id="dinheiroRow" class="mb-2">
                <label class="form-label small fw-semibold mb-1">Valor Pago (R$)</label>
                <input type="number" id="valorPago" class="form-control form-control-sm"
                       min="0" step="0.01" value="0" oninput="updateTroco()">
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted">Troco:</small>
                    <small class="fw-bold text-success" id="trocoDisplay">R$ 0,00</small>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small">Subtotal:</span>
                <span id="subtotalDisplay" class="fw-semibold">R$ 0,00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold fs-5">TOTAL:</span>
                <span class="pdv-total" id="totalDisplay">R$ 0,00</span>
            </div>

            <button class="btn btn-success btn-lg w-100 fw-bold" id="btnFinalizar"
                    onclick="finalizarVenda()" disabled>
                <i class="bi bi-check-circle me-2"></i>Finalizar Venda
            </button>
        </div>
    </div>
</div>

        </div>
    </div>
</div>

<!-- Modal de sucesso -->
<div class="modal fade" id="modalSucesso" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="text-success mb-3" style="font-size:4rem">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h4 class="fw-bold mb-1">Venda Finalizada!</h4>
            <p class="text-muted" id="resumoVenda"></p>
            <a href="#" target="_blank" class="btn btn-outline-primary btn-lg mt-2 w-100" id="btnImprimirRecibo">
                <i class="bi bi-printer me-2"></i>Imprimir recibo
            </a>
            <button class="btn btn-success btn-lg mt-2 w-100" onclick="novaVenda()">
                <i class="bi bi-plus-circle me-2"></i>Nova Venda
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAbrirCaixa" tabindex="-1" <?= $caixaAberto ? '' : 'data-bs-backdrop="static"' ?>>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?= BASE_URL ?>/pdv/caixa/abrir">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-unlock me-2 text-success"></i>Abrir Caixa</h5>
                    <?php if ($caixaAberto): ?><button type="button" class="btn-close" data-bs-dismiss="modal"></button><?php endif; ?>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-semibold">Fundo de caixa</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text">R$</span>
                        <input type="text" name="fundo_inicial" class="form-control" value="0,00" required autofocus>
                    </div>
                    <label class="form-label fw-semibold">Observação</label>
                    <textarea name="observacao" class="form-control" rows="2"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>Abrir Caixa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($caixaAberto): ?>
<div class="modal fade" id="modalSangria" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?= BASE_URL ?>/pdv/caixa/sangria">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-arrow-down-circle me-2 text-danger"></i>Sangria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-semibold">Valor</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text">R$</span>
                        <input type="text" name="valor" class="form-control" required>
                    </div>
                    <label class="form-label fw-semibold">Observação</label>
                    <textarea name="observacao" class="form-control" rows="2"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Registrar Sangria</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalReforco" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?= BASE_URL ?>/pdv/caixa/reforco">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-arrow-up-circle me-2 text-success"></i>Reforço</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-semibold">Valor</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text">R$</span>
                        <input type="text" name="valor" class="form-control" required>
                    </div>
                    <label class="form-label fw-semibold">Observação</label>
                    <textarea name="observacao" class="form-control" rows="2"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Registrar Reforço</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFecharCaixa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?= BASE_URL ?>/pdv/caixa/fechar">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-lock me-2 text-secondary"></i>Fechar Caixa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <div class="small text-muted">Fundo inicial</div>
                            <div class="fw-semibold"><?= money($caixa->fundoInicial) ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="small text-muted">Dinheiro</div>
                            <div class="fw-semibold"><?= money($caixaResumo['vendas_dinheiro'] ?? 0) ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="small text-muted">Reforços</div>
                            <div class="fw-semibold text-success"><?= money($caixaResumo['reforcos'] ?? 0) ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="small text-muted">Sangrias</div>
                            <div class="fw-semibold text-danger"><?= money($caixaResumo['sangrias'] ?? 0) ?></div>
                        </div>
                    </div>
                    <div class="alert alert-light border d-flex justify-content-between align-items-center">
                        <span>Saldo esperado em dinheiro</span>
                        <strong class="text-success"><?= money($saldoEsperado) ?></strong>
                    </div>
                    <?php if (!empty($caixaResumo['vendas_por_forma'])): ?>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm">
                            <thead><tr><th>Forma</th><th class="text-center">Vendas</th><th class="text-end">Total</th></tr></thead>
                            <tbody>
                                <?php foreach ($caixaResumo['vendas_por_forma'] as $row): ?>
                                <tr>
                                    <td><?= e(App\Models\Venda::formaLabel($row['forma_pagamento'])) ?></td>
                                    <td class="text-center"><?= $row['quantidade'] ?></td>
                                    <td class="text-end"><?= money($row['total']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                    <label class="form-label fw-semibold">Valor contado no caixa</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text">R$</span>
                        <input type="text" name="valor_fechamento" class="form-control"
                               value="<?= number_format($saldoEsperado, 2, ',', '.') ?>" required>
                    </div>
                    <label class="form-label fw-semibold">Observação</label>
                    <textarea name="observacao" class="form-control" rows="2"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-secondary">Fechar Caixa</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
const cart = {};
const caixaAberto = <?= $caixaAberto ? 'true' : 'false' ?>;
let selectedCategory = '';
let selectedSubcategory = '';
const lastEmpresaStorageKey = 'pdv_last_empresa_id';
let pagamentosCombinadosAtivo = false;
const paymentOptions = Array.from(document.querySelectorAll('#formaPagamento option')).map(option => ({
    value: option.value,
    label: option.textContent,
    dinheiro: option.dataset.dinheiro === '1',
}));

const fmt = v => 'R$ ' + v.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
const escapeHtml = value => String(value).replace(/[&<>"']/g, char => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
}[char]));

function addToCart(id, nome, preco, estoque, movimentaEstoque) {
    if (!caixaAberto) {
        new bootstrap.Modal(document.getElementById('modalAbrirCaixa')).show();
        return;
    }

    if (cart[id]) {
        if (movimentaEstoque && cart[id].qty >= estoque) {
            alert('Estoque insuficiente! Disponível: ' + estoque);
            return;
        }
        cart[id].qty++;
    } else {
        cart[id] = { nome, preco, estoque, movimentaEstoque, qty: 1 };
    }
    renderCart();
}

function changeQty(id, delta) {
    if (!cart[id]) return;
    if (delta > 0 && cart[id].movimentaEstoque && cart[id].qty >= cart[id].estoque) {
        alert('Estoque insuficiente! Disponível: ' + cart[id].estoque);
        return;
    }
    cart[id].qty += delta;
    if (cart[id].qty <= 0) delete cart[id];
    renderCart();
}

function removeItem(id) { delete cart[id]; renderCart(); }

function clearCart() { Object.keys(cart).forEach(k => delete cart[k]); renderCart(); }

function getSubtotal() {
    return Object.values(cart).reduce((s, i) => s + i.preco * i.qty, 0);
}

function renderCart() {
    const container = document.getElementById('cartItems');
    const ids = Object.keys(cart);

    if (ids.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-cart-x fs-1 d-block mb-2"></i>Clique em um produto para adicionar</div>';
        document.getElementById('btnFinalizar').disabled = true;
        document.getElementById('subtotalDisplay').textContent = 'R$ 0,00';
        document.getElementById('totalDisplay').textContent    = 'R$ 0,00';
        return;
    }

    container.innerHTML = ids.map(id => {
        const item = cart[id];
        const sub  = item.preco * item.qty;
        return `<div class="pdv-cart-item">
            <div class="item-name">${escapeHtml(item.nome)}</div>
            <div class="d-flex align-items-center gap-1">
                <button class="btn btn-sm btn-outline-secondary py-0 px-1" onclick="changeQty(${id},-1)">−</button>
                <span class="fw-bold px-1">${item.qty}</span>
                <button class="btn btn-sm btn-outline-secondary py-0 px-1" onclick="changeQty(${id},1)">+</button>
            </div>
            <div class="text-end" style="min-width:80px">
                <div class="fw-bold text-success">${fmt(sub)}</div>
                <div class="text-muted" style="font-size:.75rem">${fmt(item.preco)}/un</div>
            </div>
            <button class="btn btn-sm btn-outline-danger py-0 px-1" onclick="removeItem(${id})">
                <i class="bi bi-x"></i>
            </button>
        </div>`;
    }).join('');

    document.getElementById('btnFinalizar').disabled = !caixaAberto;
    updateTotal();
}

function updateTotal() {
    const sub  = getSubtotal();
    const desc = Math.min(parseFloat(document.getElementById('desconto').value) || 0, sub);
    const tot  = Math.max(sub - desc, 0);
    document.getElementById('subtotalDisplay').textContent = fmt(sub);
    document.getElementById('totalDisplay').textContent    = fmt(tot);
    updatePagamentosCombinados();
    updateTroco();
}

function updateTroco() {
    const sub  = getSubtotal();
    const desc = Math.min(parseFloat(document.getElementById('desconto').value) || 0, sub);
    const tot  = Math.max(sub - desc, 0);
    const forma = document.getElementById('formaPagamento').value;
    const pago = pagamentosCombinadosAtivo
        ? getPagamentosTotal()
        : (isFormaDinheiro(forma) ? (parseFloat(document.getElementById('valorPago').value) || 0) : tot);
    document.getElementById('trocoDisplay').textContent = fmt(Math.max(pago - tot, 0));
}

function toggleDinheiro() {
    const selected = document.getElementById('formaPagamento').selectedOptions[0];
    document.getElementById('dinheiroRow').style.display =
        !pagamentosCombinadosAtivo && selected?.dataset.dinheiro === '1' ? '' : 'none';
}

function isFormaDinheiro(forma) {
    return paymentOptions.some(option => option.value === forma && option.dinheiro);
}

function getTotalVenda() {
    const sub = getSubtotal();
    const desc = Math.min(parseFloat(document.getElementById('desconto').value) || 0, sub);
    return Math.max(sub - desc, 0);
}

function getPagamentosRows() {
    return Array.from(document.querySelectorAll('.pagamento-row')).map(row => ({
        forma_pagamento: row.querySelector('.pagamento-forma').value,
        valor: parseFloat(row.querySelector('.pagamento-valor').value) || 0,
    })).filter(pagamento => pagamento.forma_pagamento && pagamento.valor > 0);
}

function getPagamentosTotal() {
    return getPagamentosRows().reduce((total, pagamento) => total + pagamento.valor, 0);
}

function addPagamentoRow(forma = '', valor = '') {
    const container = document.getElementById('pagamentosRows');
    const row = document.createElement('div');
    row.className = 'pagamento-row d-flex gap-2';
    const optionsHtml = paymentOptions.map(option =>
        `<option value="${escapeHtml(option.value)}" ${option.value === forma ? 'selected' : ''}>${escapeHtml(option.label)}</option>`
    ).join('');
    row.innerHTML = `
        <select class="form-select form-select-sm pagamento-forma" onchange="updatePagamentosCombinados()">${optionsHtml}</select>
        <input type="number" class="form-control form-control-sm pagamento-valor" min="0" step="0.01" value="${escapeHtml(valor)}" oninput="updatePagamentosCombinados()">
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removePagamentoRow(this)" title="Remover">
            <i class="bi bi-x"></i>
        </button>
    `;
    container.appendChild(row);
    updatePagamentosCombinados();
}

function removePagamentoRow(button) {
    button.closest('.pagamento-row')?.remove();
    updatePagamentosCombinados();
}

function togglePagamentosCombinados() {
    pagamentosCombinadosAtivo = !pagamentosCombinadosAtivo;
    document.getElementById('pagamentosCombinados').classList.toggle('d-none', !pagamentosCombinadosAtivo);
    document.getElementById('btnTogglePagamentos').innerHTML = pagamentosCombinadosAtivo
        ? '<i class="bi bi-x-circle me-1"></i>Usar simples'
        : '<i class="bi bi-plus-circle me-1"></i>Combinar';

    if (pagamentosCombinadosAtivo && document.querySelectorAll('.pagamento-row').length === 0) {
        addPagamentoRow(document.getElementById('formaPagamento').value, getTotalVenda().toFixed(2));
    }

    toggleDinheiro();
    updatePagamentosCombinados();
}

function updatePagamentosCombinados() {
    if (!pagamentosCombinadosAtivo) return;
    const total = getTotalVenda();
    const pago = getPagamentosTotal();
    document.getElementById('pagamentosTotalDisplay').textContent = fmt(pago);
    document.getElementById('pagamentosRestanteDisplay').textContent = fmt(Math.max(total - pago, 0));
    updateTroco();
}

function initEmpresaSelection() {
    const select = document.getElementById('empresaId');
    if (!select || select.tagName !== 'SELECT') return;

    const options = Array.from(select.options).filter(option => option.value !== '0');
    if (options.length === 0) return;

    const savedEmpresaId = localStorage.getItem(lastEmpresaStorageKey);
    const savedOption = savedEmpresaId ? options.find(option => option.value === savedEmpresaId) : null;
    select.value = savedOption ? savedOption.value : options[0].value;
    localStorage.setItem(lastEmpresaStorageKey, select.value);

    select.addEventListener('change', () => {
        if (select.value !== '0') {
            localStorage.setItem(lastEmpresaStorageKey, select.value);
        }
    });
}

function finalizarVenda() {
    if (!caixaAberto) {
        new bootstrap.Modal(document.getElementById('modalAbrirCaixa')).show();
        return;
    }

    const sub  = getSubtotal();
    if (sub === 0) return;

    const desc  = Math.min(parseFloat(document.getElementById('desconto').value) || 0, sub);
    const tot   = Math.max(sub - desc, 0);
    const forma = document.getElementById('formaPagamento').value;
    const pagamentos = pagamentosCombinadosAtivo ? getPagamentosRows() : [];
    const pago  = pagamentosCombinadosAtivo
        ? getPagamentosTotal()
        : (isFormaDinheiro(forma) ? (parseFloat(document.getElementById('valorPago').value) || 0) : tot);

    if (pago < tot) {
        alert('Valor pago insuficiente! Total: ' + fmt(tot));
        return;
    }

    if (pagamentosCombinadosAtivo) {
        const valorDinheiro = pagamentos.reduce((sum, pagamento) =>
            sum + (isFormaDinheiro(pagamento.forma_pagamento) ? pagamento.valor : 0), 0);
        const troco = Math.max(pago - tot, 0);
        if (troco > valorDinheiro) {
            alert('Troco maior que o valor pago em dinheiro.');
            return;
        }
    }

    const itens = Object.entries(cart).map(([id, item]) => ({
        id: parseInt(id), qty: item.qty, preco: item.preco
    }));

    const btn = document.getElementById('btnFinalizar');
    const reciboWindow = window.open('', '_blank', 'width=360,height=640');
    if (reciboWindow) {
        reciboWindow.document.write('<!doctype html><title>Recibo</title><body style="font-family:sans-serif;padding:1rem">Gerando recibo...</body>');
        reciboWindow.document.close();
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';

    const empresaSelect = document.getElementById('empresaId');
    const empresaId = parseInt(empresaSelect.value) || 0;
    if (empresaSelect.tagName === 'SELECT' && empresaId > 0) {
        localStorage.setItem(lastEmpresaStorageKey, String(empresaId));
    }

    fetch('<?= BASE_URL ?>/pdv/finalizar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ itens, desconto: desc, forma_pagamento: forma, valor_pago: pago, total: tot, empresa_id: empresaId, pagamentos })
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            const reciboUrl = '<?= BASE_URL ?>/vendas/recibo/' + data.venda_id;
            if (reciboWindow) {
                reciboWindow.location.href = reciboUrl;
            }
            document.getElementById('btnImprimirRecibo').href = reciboUrl;

            const troco = Math.max(pago - tot, 0);
            document.getElementById('resumoVenda').innerHTML =
                `Venda <strong>#${data.venda_id}</strong> — Total: <strong>${fmt(tot)}</strong>` +
                (troco > 0 ? ` - Troco: <strong>${fmt(troco)}</strong>` : '');
            new bootstrap.Modal(document.getElementById('modalSucesso')).show();
        } else {
            if (reciboWindow) reciboWindow.close();
            alert('Erro: ' + (data.erro || 'Tente novamente.'));
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Finalizar Venda';
        }
    })
    .catch(() => {
        if (reciboWindow) reciboWindow.close();
        alert('Erro de comunicação. Tente novamente.');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Finalizar Venda';
    });
}

function novaVenda() {
    clearCart();
    document.getElementById('desconto').value  = 0;
    document.getElementById('valorPago').value = 0;
    if (document.querySelector('#formaPagamento option[value="dinheiro"]')) {
        document.getElementById('formaPagamento').value = 'dinheiro';
    }
    pagamentosCombinadosAtivo = false;
    document.getElementById('pagamentosRows').innerHTML = '';
    document.getElementById('pagamentosCombinados').classList.add('d-none');
    document.getElementById('btnTogglePagamentos').innerHTML = '<i class="bi bi-plus-circle me-1"></i>Combinar';
    toggleDinheiro();
    document.getElementById('btnFinalizar').innerHTML = '<i class="bi bi-check-circle me-2"></i>Finalizar Venda';
    bootstrap.Modal.getInstance(document.getElementById('modalSucesso')).hide();
    location.reload();
}

function applyProductFilters() {
    const q = document.getElementById('searchProduto').value.toLowerCase().trim();

    document.querySelectorAll('.product-item').forEach(el => {
        const matchesSearch = !q || el.dataset.nome.includes(q) || el.dataset.codigo.includes(q);
        const matchesCategory = !selectedCategory || el.dataset.categoria === selectedCategory;
        const matchesSubcategory = !selectedSubcategory || el.dataset.subcategoria === selectedSubcategory;
        el.style.display = matchesSearch && matchesCategory && matchesSubcategory ? '' : 'none';
    });
}

function renderCategoryFilters() {
    document.querySelectorAll('#categoryCards .pdv-filter-card').forEach(card => {
        card.classList.toggle('active', card.dataset.category === selectedCategory);
    });

    document.querySelectorAll('#subcategoryCards .subcategory-item').forEach(item => {
        item.classList.toggle('d-none', item.dataset.category !== selectedCategory);
    });

    document.querySelectorAll('#subcategoryCards .pdv-filter-card').forEach(card => {
        card.classList.toggle('active', card.dataset.subcategory === selectedSubcategory);
    });

    const subcategorySection = document.getElementById('subcategorySection');
    if (subcategorySection) {
        subcategorySection.classList.toggle('d-none', selectedCategory === '');
    }
}

document.getElementById('searchProduto').addEventListener('input', applyProductFilters);

document.querySelectorAll('#categoryCards .pdv-filter-card').forEach(card => {
    card.addEventListener('click', () => {
        selectedCategory = card.dataset.category;
        selectedSubcategory = '';
        renderCategoryFilters();
        applyProductFilters();
    });
});

document.querySelectorAll('#subcategoryCards .pdv-filter-card').forEach(card => {
    card.addEventListener('click', () => {
        selectedCategory = card.dataset.category;
        selectedSubcategory = card.dataset.subcategory;
        renderCategoryFilters();
        applyProductFilters();
    });
});

const clearProductFilters = document.getElementById('clearProductFilters');
if (clearProductFilters) {
    clearProductFilters.addEventListener('click', () => {
        selectedCategory = '';
        selectedSubcategory = '';
        renderCategoryFilters();
        applyProductFilters();
    });
}

document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', () => {
        addToCart(
            parseInt(card.dataset.id, 10),
            card.dataset.nome,
            parseFloat(card.dataset.preco) || 0,
            parseInt(card.dataset.estoque, 10) || 0,
            card.dataset.movimentaEstoque === '1'
        );
    });
});

if (!caixaAberto) {
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('modalAbrirCaixa')).show();
    });
}

initEmpresaSelection();
</script>
