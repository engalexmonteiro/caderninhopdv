<div class="container py-4" style="max-width:700px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-<?= $tipo->id > 0 ? 'pencil' : 'plus-circle' ?> me-2 text-primary"></i>
            <?= e($pageTitle) ?>
        </h4>
        <a href="<?= BASE_URL ?>/financeiro/tipos-pagamento" class="btn btn-outline-secondary btn-sm">
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
                  action="<?= BASE_URL . ($tipo->id > 0 ? '/financeiro/tipos-pagamento/editar/' . $tipo->id : '/financeiro/tipos-pagamento/novo') ?>"
                  novalidate>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control"
                               value="<?= e($tipo->nome) ?>" required autofocus>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Ordem</label>
                        <input type="number" name="ordem" class="form-control"
                               min="0" value="<?= $tipo->ordem ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Código <span class="text-danger">*</span></label>
                        <input type="text" name="codigo" class="form-control"
                               value="<?= e($tipo->codigo) ?>" required>
                        <div class="form-text">Use um código simples, como <code>dinheiro</code>, <code>pix</code> ou <code>cartao_credito</code>.</div>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input type="hidden" name="ativo" value="0">
                            <input class="form-check-input" type="checkbox" name="ativo" id="ativo"
                                   value="1" <?= $tipo->ativo ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="ativo">Tipo ativo</label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="<?= BASE_URL ?>/financeiro/tipos-pagamento" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Salvar Tipo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
