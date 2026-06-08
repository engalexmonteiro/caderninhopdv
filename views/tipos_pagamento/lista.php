<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-credit-card-2-front me-2 text-primary"></i>Tipos de Pagamento
        </h4>
        <a href="<?= BASE_URL ?>/financeiro/tipos-pagamento/novo" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Novo Tipo
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
                            <th style="width:90px">Ordem</th>
                            <th>Nome</th>
                            <th>Código</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tipos)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-credit-card fs-1 d-block mb-2"></i>
                                Nenhum tipo de pagamento cadastrado.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($tipos as $tipo): ?>
                        <tr class="<?= !$tipo->ativo ? 'table-secondary text-muted' : '' ?>">
                            <td><?= $tipo->ordem ?></td>
                            <td class="fw-semibold"><?= e($tipo->nome) ?></td>
                            <td><code><?= e($tipo->codigo) ?></code></td>
                            <td class="text-center">
                                <span class="badge <?= $tipo->ativo ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $tipo->ativo ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>/financeiro/tipos-pagamento/editar/<?= $tipo->id ?>"
                                   class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/financeiro/tipos-pagamento/toggle/<?= $tipo->id ?>"
                                   class="btn btn-sm btn-outline-<?= $tipo->ativo ? 'warning' : 'success' ?>"
                                   title="<?= $tipo->ativo ? 'Desativar' : 'Ativar' ?>">
                                    <i class="bi bi-toggle-<?= $tipo->ativo ? 'on' : 'off' ?>"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small"><?= count($tipos) ?> tipo(s)</div>
    </div>
</div>
