<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2 text-primary"></i>Perfis de Usuario</h4>
        <a href="<?= BASE_URL ?>/usuarios/perfis/novo" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Novo Perfil
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
                            <th>Codigo</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($perfis)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-5">Nenhum perfil cadastrado.</td></tr>
                        <?php else: ?>
                        <?php foreach ($perfis as $perfil): ?>
                        <tr class="<?= !$perfil->ativo ? 'table-secondary text-muted' : '' ?>">
                            <td class="fw-semibold"><?= e($perfil->nome) ?></td>
                            <td><code><?= e($perfil->codigo) ?></code></td>
                            <td class="text-center">
                                <span class="badge <?= $perfil->ativo ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $perfil->ativo ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>/usuarios/perfis/editar/<?= $perfil->id ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/usuarios/perfis/toggle/<?= $perfil->id ?>"
                                   class="btn btn-sm btn-outline-<?= $perfil->ativo ? 'warning' : 'success' ?>"
                                   title="<?= $perfil->ativo ? 'Desativar' : 'Ativar' ?>">
                                    <i class="bi bi-toggle-<?= $perfil->ativo ? 'on' : 'off' ?>"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small"><?= count($perfis) ?> perfil(is)</div>
    </div>
</div>
