<div class="container-fluid py-4">
    <h4 class="mb-4 fw-bold"><i class="bi bi-receipt me-2 text-primary"></i>Relatório de Vendas</h4>

    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="/vendas" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">De</label>
                    <input type="date" name="de" class="form-control" value="<?= e($dataInicio) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Até</label>
                    <input type="date" name="ate" class="form-control" value="<?= e($dataFim) ?>">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="bi bi-funnel me-1"></i>Filtrar
                    </button>
                </div>
                <div class="col-md-4 text-end">
                    <div class="fw-bold fs-5 text-success"><?= money($totalPeriodo) ?></div>
                    <div class="text-muted small"><?= count($vendas) ?> venda(s) no período</div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th><th>Data / Hora</th><th>Operador</th>
                            <th class="text-center">Itens</th><th>Pagamento</th>
                            <th class="text-end">Desconto</th><th class="text-end">Total</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($vendas)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Nenhuma venda no período.</td></tr>
                        <?php else: ?>
                        <?php foreach ($vendas as $v): ?>
                        <tr>
                            <td><span class="badge bg-secondary">#<?= $v->id ?></span></td>
                            <td><?= date('d/m/Y H:i', strtotime($v->criadoEm)) ?></td>
                            <td><?= e($v->usuarioNome) ?></td>
                            <td class="text-center"><?= $v->qtd_itens ?? '—' ?></td>
                            <td><span class="badge bg-info text-dark"><?= \App\Models\Venda::formaLabel($v->formaPagamento) ?></span></td>
                            <td class="text-end text-danger">
                                <?= $v->desconto > 0 ? '- ' . money($v->desconto) : '—' ?>
                            </td>
                            <td class="text-end fw-bold <?= $v->status === 'concluida' ? 'text-success' : 'text-danger' ?>">
                                <?= money($v->total) ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $v->status === 'concluida' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= ucfirst($v->status) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($vendas)): ?>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="6" class="text-end">Total do Período:</td>
                            <td class="text-end text-success"><?= money($totalPeriodo) ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>
