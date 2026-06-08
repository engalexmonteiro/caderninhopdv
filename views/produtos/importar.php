<div class="container py-4" style="max-width:760px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-file-earmark-spreadsheet me-2 text-primary"></i>
            Importar Produtos
        </h4>
        <a href="<?= BASE_URL ?>/produtos" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Voltar
        </a>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
            <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if ($result !== null): ?>
    <div class="alert <?= $result['ok'] ? 'alert-success' : 'alert-warning' ?>">
        <div class="fw-semibold mb-1">
            <?= $result['ok'] ? 'Importação concluída.' : 'Importação concluída com pendências.' ?>
        </div>
        <div><?= (int) $result['importados'] ?> produto(s) inserido(s) e <?= (int) $result['atualizados'] ?> atualizado(s).</div>
    </div>

    <?php if (!empty($result['erros'])): ?>
    <div class="card mb-3">
        <div class="card-header fw-semibold">Linhas não importadas</div>
        <div class="card-body">
            <ul class="mb-0">
                <?php foreach (array_slice($result['erros'], 0, 50) as $err): ?>
                <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php if (count($result['erros']) > 50): ?>
            <div class="text-muted small mt-2">Exibindo as primeiras 50 pendências.</div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= BASE_URL ?>/produtos/importar" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Arquivo .xlsx</label>
                    <input type="file" name="arquivo" class="form-control" accept=".xlsx" required>
                </div>
                <div class="text-muted small mb-3">
                    A primeira linha deve conter os cabeçalhos da planilha de produtos. Produtos com o mesmo Código de Barras serão atualizados.
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= BASE_URL ?>/produtos" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i>Importar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
