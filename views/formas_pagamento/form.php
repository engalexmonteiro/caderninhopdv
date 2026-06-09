<div class="container py-4" style="max-width:760px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-credit-card me-2 text-primary"></i><?= e($pageTitle) ?>
        </h4>
        <a href="<?= BASE_URL ?>/financeiro/formas-pagamento" class="btn btn-outline-secondary btn-sm">
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
                  action="<?= BASE_URL . ($forma->id > 0 ? '/financeiro/formas-pagamento/editar/' . $forma->id : '/financeiro/formas-pagamento/nova') ?>"
                  novalidate>
                <?= csrfField() ?>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Tipo de Pagamento <span class="text-danger">*</span></label>
                        <select name="tipo_pagamento_id" class="form-select" required>
                            <option value="">Selecione</option>
                            <?php foreach ($tipos as $tipo): ?>
                            <option value="<?= $tipo->id ?>" <?= $forma->tipoPagamentoId === $tipo->id ? 'selected' : '' ?>>
                                <?= e($tipo->nome) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Parcelas</label>
                        <input type="number" name="parcelas" class="form-control" min="1" value="<?= max(1, $forma->parcelas) ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Nome</label>
                        <input type="text" name="nome" class="form-control" value="<?= e($forma->nome) ?>"
                               placeholder="Ex.: Cartao Credito 3x">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Ordem</label>
                        <input type="number" name="ordem" class="form-control" min="0" value="<?= $forma->ordem ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Codigo</label>
                        <input type="text" name="codigo" class="form-control" value="<?= e($forma->codigo) ?>"
                               placeholder="Gerado automaticamente se ficar vazio">
                    </div>
                    <?php if ($forma->id === 0): ?>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Gerar de 1x ate</label>
                        <input type="number" name="max_parcelas" class="form-control" min="1" value="1">
                    </div>
                    <?php endif; ?>
                    <div class="col-md-6 d-flex align-items-end pb-2">
                        <div class="form-check form-switch">
                            <input type="hidden" name="ativo" value="0">
                            <input class="form-check-input" type="checkbox" name="ativo" id="ativo"
                                   value="1" <?= $forma->ativo ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="ativo">Forma ativa</label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="<?= BASE_URL ?>/financeiro/formas-pagamento" class="btn btn-outline-secondary">Cancelar</a>
                    <?php if ($forma->id === 0): ?>
                    <button type="submit" name="gerar_parcelas" value="1" class="btn btn-outline-primary">
                        <i class="bi bi-layers me-1"></i>Gerar Parcelas
                    </button>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Salvar Forma
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
