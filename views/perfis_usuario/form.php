<div class="container py-4" style="max-width:600px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-person-badge me-2 text-primary"></i>
            <?= e($pageTitle) ?>
        </h4>
        <a href="<?= BASE_URL ?>/usuarios/perfis" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Voltar
        </a>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle me-2"></i>
        <ul class="mb-0 mt-1">
            <?php foreach ($errors as $err): ?>
            <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST"
                  action="<?= BASE_URL . ($perfil->id > 0 ? '/usuarios/perfis/editar/' . $perfil->id : '/usuarios/perfis/novo') ?>"
                  novalidate>
                <?= csrfField() ?>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control"
                               value="<?= e($perfil->nome) ?>" required autofocus>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Codigo</label>
                        <input type="text" name="codigo" class="form-control"
                               value="<?= e($perfil->codigo) ?>"
                               placeholder="Gerado automaticamente se ficar vazio">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="ativo" id="ativo"
                                   <?= $perfil->ativo ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="ativo">Perfil ativo</label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="<?= BASE_URL ?>/usuarios/perfis" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Salvar Perfil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
