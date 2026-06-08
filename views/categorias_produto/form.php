<div class="container py-4" style="max-width:700px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-<?= $categoria->id > 0 ? 'pencil' : 'plus-circle' ?> me-2 text-primary"></i>
            <?= e($pageTitle) ?>
        </h4>
        <a href="<?= BASE_URL ?>/produtos/categorias" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Voltar
        </a>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST"
                  action="<?= BASE_URL . ($categoria->id > 0 ? '/produtos/categorias/editar/' . $categoria->id : '/produtos/categorias/nova') ?>"
                  novalidate>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="nome" class="form-control" value="<?= e($categoria->nome) ?>" required autofocus>
                </div>
                <div class="form-check form-switch mb-3">
                    <input type="hidden" name="ativo" value="0">
                    <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1" <?= $categoria->ativo ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="ativo">Categoria ativa</label>
                </div>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="<?= BASE_URL ?>/produtos/categorias" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Salvar Categoria
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
