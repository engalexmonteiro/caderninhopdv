<div class="container py-4" style="max-width:900px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold"><i class="bi bi-palette me-2 text-primary"></i>Personalizacao</h4>
    </div>

    <?php if ($flash !== ''): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i><?= e($flash) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

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

    <form method="POST" action="<?= BASE_URL ?>/personalizacao" enctype="multipart/form-data" novalidate>
        <?= csrfField() ?>
        <div class="card mb-3">
            <div class="card-header bg-white">
                <i class="bi bi-brush me-2 text-primary"></i>Paleta de cores
            </div>
            <div class="card-body">
                <input type="hidden" name="paleta" id="paleta" value="<?= e($personalizacao->paleta) ?>">
                <div class="row g-3">
                    <?php foreach ($paletas as $codigo => $paleta): ?>
                    <div class="col-md-6 col-xl-4">
                        <button type="button"
                                class="palette-option w-100 <?= $personalizacao->paleta === $codigo ? 'active' : '' ?>"
                                data-palette="<?= e($codigo) ?>"
                                data-primary="<?= e($paleta['primary']) ?>"
                                data-success="<?= e($paleta['success']) ?>">
                            <span class="palette-card">
                                <span class="fw-bold d-block mb-2"><?= e($paleta['nome']) ?></span>
                                <span class="d-flex gap-2">
                                    <span class="palette-swatch" style="background:<?= e($paleta['primary']) ?>"></span>
                                    <span class="palette-swatch" style="background:<?= e($paleta['success']) ?>"></span>
                                </span>
                            </span>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Cor principal</label>
                        <div class="input-group color-picker-group">
                            <input type="color" name="cor_primaria" id="corPrimaria" class="form-control form-control-color"
                                   value="<?= e($personalizacao->corPrimaria) ?>">
                            <input type="text" id="corPrimariaText" class="form-control"
                                   value="<?= e($personalizacao->corPrimaria) ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Cor de destaque</label>
                        <div class="input-group color-picker-group">
                            <input type="color" name="cor_sucesso" id="corSucesso" class="form-control form-control-color"
                                   value="<?= e($personalizacao->corSucesso) ?>">
                            <input type="text" id="corSucessoText" class="form-control"
                                   value="<?= e($personalizacao->corSucesso) ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-white">
                <i class="bi bi-building me-2 text-primary"></i>Nome da aplicacao
            </div>
            <div class="card-body">
                <label class="form-label fw-semibold">Empresa exibida no sistema</label>
                <select name="empresa_id" class="form-select">
                    <option value="0">PDV Sistema</option>
                    <?php foreach ($empresas as $empresa): ?>
                    <option value="<?= $empresa->id ?>" <?= $personalizacao->empresaId === $empresa->id ? 'selected' : '' ?>>
                        <?= e($empresa->nomeExibicao()) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-white">
                <i class="bi bi-moon-stars me-2 text-primary"></i>Aparencia
            </div>
            <div class="card-body">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="modo_noturno" id="modoNoturno"
                           <?= $personalizacao->modoNoturno ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="modoNoturno">Ativar modo noturno</label>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-white">
                <i class="bi bi-image me-2 text-primary"></i>Logo e favicon
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Logo da tela de login</label>
                        <input type="file" name="logo_login" class="form-control" accept="image/*">
                        <?php if ($personalizacao->logoLogin !== ''): ?>
                        <div class="mt-3 p-3 border rounded text-center bg-light">
                            <img src="<?= assetUrl($personalizacao->logoLogin) ?>" alt="Logo atual" class="personalizacao-preview-logo">
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Favicon</label>
                        <input type="file" name="favicon" class="form-control" accept="image/*,.ico">
                        <?php if ($personalizacao->favicon !== ''): ?>
                        <div class="mt-3 p-3 border rounded bg-light">
                            <img src="<?= assetUrl($personalizacao->favicon) ?>" alt="Favicon atual" class="personalizacao-preview-favicon">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Salvar Personalizacao
            </button>
        </div>
    </form>
</div>

<script>
const paletaInput = document.getElementById('paleta');
const corPrimaria = document.getElementById('corPrimaria');
const corPrimariaText = document.getElementById('corPrimariaText');
const corSucesso = document.getElementById('corSucesso');
const corSucessoText = document.getElementById('corSucessoText');
const hexPattern = /^#[0-9a-fA-F]{6}$/;

function syncColor(colorInput, textInput, value, markCustom = true) {
    if (!hexPattern.test(value)) return;
    const normalized = value.toLowerCase();
    colorInput.value = normalized;
    textInput.value = normalized;
    if (markCustom) {
        paletaInput.value = 'personalizada';
        document.querySelectorAll('.palette-option').forEach(btn => btn.classList.remove('active'));
    }
}

document.querySelectorAll('.palette-option').forEach(btn => {
    btn.addEventListener('click', () => {
        paletaInput.value = btn.dataset.palette;
        syncColor(corPrimaria, corPrimariaText, btn.dataset.primary, false);
        syncColor(corSucesso, corSucessoText, btn.dataset.success, false);
        document.querySelectorAll('.palette-option').forEach(item => item.classList.remove('active'));
        btn.classList.add('active');
    });
});

corPrimaria.addEventListener('input', () => syncColor(corPrimaria, corPrimariaText, corPrimaria.value));
corSucesso.addEventListener('input', () => syncColor(corSucesso, corSucessoText, corSucesso.value));
corPrimariaText.addEventListener('input', () => syncColor(corPrimaria, corPrimariaText, corPrimariaText.value));
corSucessoText.addEventListener('input', () => syncColor(corSucesso, corSucessoText, corSucessoText.value));
</script>
