<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold"><i class="bi bi-tags me-2 text-primary"></i>Categorias de Produtos</h4>
        <a href="<?= BASE_URL ?>/produtos/categorias/nova" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nova Categoria
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
                            <th>Nome</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categorias)): ?>
                        <tr><td colspan="3" class="text-center text-muted py-5">Nenhuma categoria cadastrada.</td></tr>
                        <?php else: ?>
                        <?php foreach ($categorias as $categoria): ?>
                        <tr class="<?= !$categoria->ativo ? 'table-secondary text-muted' : '' ?>">
                            <td class="fw-semibold"><?= e($categoria->nome) ?></td>
                            <td class="text-center">
                                <span class="badge <?= $categoria->ativo ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $categoria->ativo ? 'Ativa' : 'Inativa' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>/produtos/categorias/editar/<?= $categoria->id ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/produtos/categorias/toggle/<?= $categoria->id ?>"
                                   class="btn btn-sm btn-outline-<?= $categoria->ativo ? 'warning' : 'success' ?>"
                                   title="<?= $categoria->ativo ? 'Desativar' : 'Ativar' ?>">
                                    <i class="bi bi-toggle-<?= $categoria->ativo ? 'on' : 'off' ?>"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small"><?= count($categorias) ?> categoria(s)</div>
    </div>
</div>
