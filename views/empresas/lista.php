<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold"><i class="bi bi-building me-2 text-primary"></i>Empresas / CNPJs</h4>
        <a href="<?= BASE_URL ?>/empresas/nova" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nova Empresa
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
                            <th style="width:60px">Logo</th>
                            <th>Razão Social / Nome Fantasia</th>
                            <th>CNPJ</th>
                            <th>Cidade / UF</th>
                            <th>Telefone</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($empresas)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-building fs-1 d-block mb-2"></i>
                                Nenhuma empresa cadastrada.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($empresas as $emp): ?>
                        <tr class="<?= !$emp->ativo ? 'table-secondary text-muted' : '' ?>">
                            <td class="text-center">
                                <?php if ($emp->logomarca): ?>
                                <img src="<?= BASE_URL ?>/assets/logos/<?= e($emp->logomarca) ?>"
                                     alt="Logo" style="max-height:40px;max-width:50px;object-fit:contain">
                                <?php else: ?>
                                <i class="bi bi-building text-muted fs-4"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-semibold"><?= e($emp->razaoSocial) ?></div>
                                <?php if ($emp->nomeFantasia): ?>
                                <div class="small text-muted"><?= e($emp->nomeFantasia) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><code><?= e($emp->cnpjFormatado()) ?></code></td>
                            <td>
                                <?= e($emp->cidade) ?>
                                <?= $emp->estado ? '&nbsp;<span class="badge bg-light text-dark border">' . e($emp->estado) . '</span>' : '' ?>
                            </td>
                            <td><?= e($emp->telefone) ?: '<span class="text-muted">—</span>' ?></td>
                            <td class="text-center">
                                <span class="badge <?= $emp->ativo ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $emp->ativo ? 'Ativa' : 'Inativa' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>/empresas/editar/<?= $emp->id ?>"
                                   class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/empresas/toggle/<?= $emp->id ?>"
                                   class="btn btn-sm btn-outline-<?= $emp->ativo ? 'warning' : 'success' ?>"
                                   title="<?= $emp->ativo ? 'Desativar' : 'Ativar' ?>">
                                    <i class="bi bi-toggle-<?= $emp->ativo ? 'on' : 'off' ?>"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small"><?= count($empresas) ?> empresa(s)</div>
    </div>
</div>
