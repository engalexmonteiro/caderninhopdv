<div class="container-fluid py-4">

    <?php if ($erro === 'acesso_negado'): ?>
    <div class="alert alert-warning alert-dismissible fade show">
        <i class="bi bi-shield-exclamation me-2"></i>Acesso restrito a administradores.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h4>
        <a href="/pdv" class="btn btn-success btn-lg">
            <i class="bi bi-cart3 me-2"></i>Abrir PDV
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#0d6efd,#0a58ca)">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Vendas Hoje</div>
                        <div class="stat-value mt-1"><?= money($totalHoje) ?></div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#198754,#146c43)">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Nº de Vendas Hoje</div>
                        <div class="stat-value mt-1"><?= $countHoje ?></div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-receipt"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#6f42c1,#5a32a3)">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Produtos Ativos</div>
                        <div class="stat-value mt-1"><?= $totalProdutos ?></div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
                </div>
            </div>
        </div>
        <?php if (isAdmin()): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#fd7e14,#dc6502)">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Usuários Ativos</div>
                        <div class="stat-value mt-1"><?= $totalUsuarios ?></div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-people"></i></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <span class="fw-bold"><i class="bi bi-receipt me-2 text-primary"></i>Últimas Vendas</span>
            <?php if (isAdmin()): ?>
            <a href="/vendas" class="btn btn-sm btn-outline-primary">Ver todas</a>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th><th>Data / Hora</th><th>Operador</th>
                            <th>Pagamento</th><th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($vendasRecentes)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Nenhuma venda registrada.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($vendasRecentes as $v): ?>
                        <tr>
                            <td><span class="badge bg-secondary">#<?= $v->id ?></span></td>
                            <td><?= date('d/m/Y H:i', strtotime($v->criadoEm)) ?></td>
                            <td><?= e($v->usuarioNome) ?></td>
                            <td><span class="badge bg-info text-dark"><?= \App\Models\Venda::formaLabel($v->formaPagamento) ?></span></td>
                            <td class="text-end fw-bold text-success"><?= money($v->total) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
