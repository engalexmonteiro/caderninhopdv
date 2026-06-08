<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold"><i class="bi bi-diagram-2 me-2 text-primary"></i>Subcategorias de Produtos</h4>
        <a href="<?= BASE_URL ?>/produtos/subcategorias/nova" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nova Subcategoria
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
                            <th>Categoria</th>
                            <th>Subcategoria</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($subcategorias)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-5">Nenhuma subcategoria cadastrada.</td></tr>
                        <?php else: ?>
                        <?php foreach ($subcategorias as $subcategoria): ?>
                        <tr class="<?= !$subcategoria->ativo ? 'table-secondary text-muted' : '' ?>">
                            <td><?= e($subcategoria->categoriaNome) ?></td>
                            <td class="fw-semibold"><?= e($subcategoria->nome) ?></td>
                            <td class="text-center">
                                <span class="badge <?= $subcategoria->ativo ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $subcategoria->ativo ? 'Ativa' : 'Inativa' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>/produtos/subcategorias/editar/<?= $subcategoria->id ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/produtos/subcategorias/toggle/<?= $subcategoria->id ?>"
                                   class="btn btn-sm btn-outline-<?= $subcategoria->ativo ? 'warning' : 'success' ?>"
                                   title="<?= $subcategoria->ativo ? 'Desativar' : 'Ativar' ?>">
                                    <i class="bi bi-toggle-<?= $subcategoria->ativo ? 'on' : 'off' ?>"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small"><?= count($subcategorias) ?> subcategoria(s)</div>
    </div>
</div>
