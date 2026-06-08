<div class="container py-4" style="max-width:700px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-<?= $subcategoria->id > 0 ? 'pencil' : 'plus-circle' ?> me-2 text-primary"></i>
            <?= e($pageTitle) ?>
        </h4>
        <a href="<?= BASE_URL ?>/produtos/subcategorias" class="btn btn-outline-secondary btn-sm">
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
                  action="<?= BASE_URL . ($subcategoria->id > 0 ? '/produtos/subcategorias/editar/' . $subcategoria->id : '/produtos/subcategorias/nova') ?>"
                  novalidate>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Categoria <span class="text-danger">*</span></label>
                    <select name="categoria_id" class="form-select" required autofocus>
                        <option value="">Selecione...</option>
                        <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= $categoria->id ?>" <?= $subcategoria->categoriaId === $categoria->id ? 'selected' : '' ?>>
                            <?= e($categoria->nome) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="nome" class="form-control" value="<?= e($subcategoria->nome) ?>" required>
                </div>
                <div class="form-check form-switch mb-3">
                    <input type="hidden" name="ativo" value="0">
                    <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1" <?= $subcategoria->ativo ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="ativo">Subcategoria ativa</label>
                </div>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="<?= BASE_URL ?>/produtos/subcategorias" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Salvar Subcategoria
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
