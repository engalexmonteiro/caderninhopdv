<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-credit-card me-2 text-primary"></i>Formas de Pagamento
        </h4>
        <a href="<?= BASE_URL ?>/financeiro/formas-pagamento/nova" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nova Forma
        </a>
    </div>

    <?php if ($flash !== ''): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i><?= e($flash) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Forma</th>
                            <th>Codigo</th>
                            <th class="text-center">Parcelas</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($formas)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-5">Nenhuma forma de pagamento cadastrada.</td></tr>
                        <?php else: ?>
                        <?php foreach ($formas as $forma): ?>
                        <tr class="<?= !$forma->ativo ? 'table-secondary text-muted' : '' ?>">
                            <td><?= e($forma->tipoNome) ?></td>
                            <td class="fw-semibold"><?= e($forma->nome) ?></td>
                            <td><code><?= e($forma->codigo) ?></code></td>
                            <td class="text-center"><?= $forma->parcelas ?>x</td>
                            <td class="text-center">
                                <span class="badge <?= $forma->ativo ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $forma->ativo ? 'Ativa' : 'Inativa' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>/financeiro/formas-pagamento/editar/<?= $forma->id ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/financeiro/formas-pagamento/toggle/<?= $forma->id ?>"
                                   class="btn btn-sm btn-outline-<?= $forma->ativo ? 'warning' : 'success' ?>"
                                   title="<?= $forma->ativo ? 'Desativar' : 'Ativar' ?>">
                                    <i class="bi bi-toggle-<?= $forma->ativo ? 'on' : 'off' ?>"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small"><?= count($formas) ?> forma(s)</div>
    </div>
</div>
