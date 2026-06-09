<div class="container-fluid py-4">
    <?php
    $perfisPorCodigo = [];
    foreach ($perfis ?? [] as $perfil) {
        $perfisPorCodigo[$perfil->codigo] = $perfil->nome;
    }
    ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold"><i class="bi bi-people me-2 text-primary"></i>Usuarios</h4>
        <a href="<?= BASE_URL ?>/usuarios/novo" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Novo Usuario
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
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th><th>Nome</th><th>E-mail</th>
                            <th class="text-center">Perfil</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                        <tr class="<?= !$u->ativo ? 'table-secondary text-muted' : '' ?>">
                            <td><?= $u->id ?></td>
                            <td class="fw-semibold"><?= e($u->nome) ?></td>
                            <td><?= e($u->email) ?></td>
                            <td class="text-center">
                                <span class="badge <?= $u->perfil === 'admin' ? 'badge-admin' : 'badge-usuario' ?>">
                                    <i class="bi bi-<?= $u->perfil === 'admin' ? 'shield-check' : 'person' ?> me-1"></i>
                                    <?= e($perfisPorCodigo[$u->perfil] ?? $u->perfil) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $u->ativo ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $u->ativo ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>/usuarios/editar/<?= $u->id ?>"
                                   class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if ($u->id !== auth()['id']): ?>
                                <a href="<?= BASE_URL ?>/usuarios/toggle/<?= $u->id ?>"
                                   class="btn btn-sm btn-outline-<?= $u->ativo ? 'warning' : 'success' ?>"
                                   title="<?= $u->ativo ? 'Desativar' : 'Ativar' ?>">
                                    <i class="bi bi-toggle-<?= $u->ativo ? 'on' : 'off' ?>"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small"><?= count($usuarios) ?> usuario(s)</div>
    </div>
</div>
