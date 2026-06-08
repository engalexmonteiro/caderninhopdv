<div class="pdv-wrapper">

    <!-- Painel de Produtos -->
    <div class="pdv-products">
        <div class="mb-3">
            <input type="text" id="searchProduto" class="form-control shadow-sm"
                   placeholder="&#xF52A;  Buscar produto por nome ou código..." autofocus>
        </div>
        <div class="row g-2" id="productGrid">
            <?php foreach ($produtos as $p): ?>
            <div class="col-6 col-md-4 col-xl-3 product-item"
                 data-nome="<?= strtolower(e($p->nome)) ?>"
                 data-codigo="<?= strtolower(e($p->codigo)) ?>">
                <div class="card product-card h-100 shadow-sm"
                     onclick="addToCart(<?= $p->id ?>, <?= json_encode($p->nome) ?>, <?= $p->precoVenda ?>, <?= $p->estoque ?>, <?= $p->movimentaEstoque ? 'true' : 'false' ?>)">
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
                        <option value="dinheiro">Dinheiro</option>
                        <option value="cartao_debito">Cartão Débito</option>
                        <option value="cartao_credito">Cartão Crédito</option>
                        <option value="pix">PIX</option>
                    </select>
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

<!-- Modal de sucesso -->
<div class="modal fade" id="modalSucesso" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="text-success mb-3" style="font-size:4rem">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h4 class="fw-bold mb-1">Venda Finalizada!</h4>
            <p class="text-muted" id="resumoVenda"></p>
            <button class="btn btn-success btn-lg mt-2" onclick="novaVenda()">
                <i class="bi bi-plus-circle me-2"></i>Nova Venda
            </button>
        </div>
    </div>
</div>

<script>
const cart = {};

const fmt = v => 'R$ ' + v.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');

function addToCart(id, nome, preco, estoque, movimentaEstoque) {
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
            <div class="item-name">${item.nome}</div>
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

    document.getElementById('btnFinalizar').disabled = false;
    updateTotal();
}

function updateTotal() {
    const sub  = getSubtotal();
    const desc = Math.min(parseFloat(document.getElementById('desconto').value) || 0, sub);
    const tot  = Math.max(sub - desc, 0);
    document.getElementById('subtotalDisplay').textContent = fmt(sub);
    document.getElementById('totalDisplay').textContent    = fmt(tot);
    updateTroco();
}

function updateTroco() {
    const sub  = getSubtotal();
    const desc = Math.min(parseFloat(document.getElementById('desconto').value) || 0, sub);
    const tot  = Math.max(sub - desc, 0);
    const pago = parseFloat(document.getElementById('valorPago').value) || 0;
    document.getElementById('trocoDisplay').textContent = fmt(Math.max(pago - tot, 0));
}

function toggleDinheiro() {
    document.getElementById('dinheiroRow').style.display =
        document.getElementById('formaPagamento').value === 'dinheiro' ? '' : 'none';
}

function finalizarVenda() {
    const sub  = getSubtotal();
    if (sub === 0) return;

    const desc  = Math.min(parseFloat(document.getElementById('desconto').value) || 0, sub);
    const tot   = Math.max(sub - desc, 0);
    const forma = document.getElementById('formaPagamento').value;
    const pago  = forma === 'dinheiro' ? (parseFloat(document.getElementById('valorPago').value) || 0) : tot;

    if (forma === 'dinheiro' && pago < tot) {
        alert('Valor pago insuficiente! Total: ' + fmt(tot));
        return;
    }

    const itens = Object.entries(cart).map(([id, item]) => ({
        id: parseInt(id), qty: item.qty, preco: item.preco
    }));

    const btn = document.getElementById('btnFinalizar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';

    const empresaId = parseInt(document.getElementById('empresaId').value) || 0;

    fetch('<?= BASE_URL ?>/pdv/finalizar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ itens, desconto: desc, forma_pagamento: forma, valor_pago: pago, total: tot, empresa_id: empresaId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            const troco = Math.max(pago - tot, 0);
            document.getElementById('resumoVenda').innerHTML =
                `Venda <strong>#${data.venda_id}</strong> — Total: <strong>${fmt(tot)}</strong>` +
                (forma === 'dinheiro' ? ` — Troco: <strong>${fmt(troco)}</strong>` : '');
            new bootstrap.Modal(document.getElementById('modalSucesso')).show();
        } else {
            alert('Erro: ' + (data.erro || 'Tente novamente.'));
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Finalizar Venda';
        }
    })
    .catch(() => {
        alert('Erro de comunicação. Tente novamente.');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Finalizar Venda';
    });
}

function novaVenda() {
    clearCart();
    document.getElementById('desconto').value  = 0;
    document.getElementById('valorPago').value = 0;
    document.getElementById('formaPagamento').value = 'dinheiro';
    document.getElementById('dinheiroRow').style.display = '';
    document.getElementById('btnFinalizar').innerHTML = '<i class="bi bi-check-circle me-2"></i>Finalizar Venda';
    bootstrap.Modal.getInstance(document.getElementById('modalSucesso')).hide();
    location.reload();
}

document.getElementById('searchProduto').addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.product-item').forEach(el => {
        el.style.display = (!q || el.dataset.nome.includes(q) || el.dataset.codigo.includes(q)) ? '' : 'none';
    });
});
</script>
