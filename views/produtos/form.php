<div class="container py-4" style="max-width:700px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-<?= $produto->id > 0 ? 'pencil' : 'plus-circle' ?> me-2 text-primary"></i>
            <?= e($pageTitle) ?>
        </h4>
        <a href="/produtos" class="btn btn-outline-secondary btn-sm">
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
                  action="<?= $produto->id > 0 ? '/produtos/editar/' . $produto->id : '/produtos/novo' ?>"
                  novalidate>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Código <span class="text-danger">*</span></label>
                        <input type="text" name="codigo" class="form-control"
                               value="<?= e($produto->codigo) ?>" required autofocus>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control"
                               value="<?= e($produto->nome) ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="2"><?= e($produto->descricao) ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Preço de Custo</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" name="preco_custo" class="form-control"
                                   value="<?= number_format($produto->precoCusto, 2, ',', '') ?>"
                                   placeholder="0,00">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Preço de Venda <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" name="preco_venda" class="form-control"
                                   value="<?= number_format($produto->precoVenda, 2, ',', '') ?>"
                                   placeholder="0,00" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Estoque</label>
                        <input type="number" name="estoque" class="form-control"
                               min="0" value="<?= $produto->estoque ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Unidade</label>
                        <select name="unidade" class="form-select">
                            <?php foreach (['UN','KG','LT','CX','PC','MT'] as $un): ?>
                            <option value="<?= $un ?>" <?= $produto->unidade === $un ? 'selected' : '' ?>><?= $un ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="ativo" id="ativo"
                                   <?= $produto->ativo ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="ativo">Produto ativo</label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="/produtos" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Salvar Produto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
