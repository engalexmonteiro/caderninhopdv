<div class="container py-4" style="max-width:800px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-<?= $empresa->id > 0 ? 'pencil' : 'plus-circle' ?> me-2 text-primary"></i>
            <?= e($pageTitle) ?>
        </h4>
        <a href="<?= BASE_URL ?>/empresas" class="btn btn-outline-secondary btn-sm">
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
                  enctype="multipart/form-data"
                  action="<?= BASE_URL . ($empresa->id > 0 ? '/empresas/editar/' . $empresa->id : '/empresas/nova') ?>"
                  novalidate>
                <?= csrfField() ?>
                <h6 class="text-primary fw-bold mb-3"><i class="bi bi-info-circle me-1"></i>Dados Principais</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Razão Social <span class="text-danger">*</span></label>
                        <input type="text" name="razao_social" class="form-control"
                               value="<?= e($empresa->razaoSocial) ?>" required autofocus>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">CNPJ <span class="text-danger">*</span></label>
                        <input type="text" name="cnpj" id="cnpj" class="form-control"
                               value="<?= e($empresa->cnpjFormatado()) ?>"
                               placeholder="00.000.000/0000-00" maxlength="18" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Nome Fantasia</label>
                        <input type="text" name="nome_fantasia" class="form-control"
                               value="<?= e($empresa->nomeFantasia) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Telefone</label>
                        <input type="text" name="telefone" id="telefone" class="form-control"
                               value="<?= e($empresa->telefone) ?>"
                               placeholder="(00) 00000-0000" maxlength="15">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">E-mail</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= e($empresa->email) ?>" placeholder="contato@empresa.com.br">
                    </div>
                </div>

                <h6 class="text-primary fw-bold mb-3 mt-4"><i class="bi bi-geo-alt me-1"></i>Endereço</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">CEP</label>
                        <input type="text" name="cep" id="cep" class="form-control"
                               value="<?= e($empresa->cep) ?>" placeholder="00000-000" maxlength="9">
                    </div>
                    <div class="col-md-9">
                        <label class="form-label fw-semibold">Endereço</label>
                        <input type="text" name="endereco" class="form-control"
                               value="<?= e($empresa->endereco) ?>" placeholder="Rua, número, complemento">
                    </div>
                    <div class="col-md-7">
                        <label class="form-label fw-semibold">Cidade</label>
                        <input type="text" name="cidade" class="form-control"
                               value="<?= e($empresa->cidade) ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">UF</label>
                        <select name="estado" class="form-select">
                            <option value="">—</option>
                            <?php
                            $ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA',
                                    'PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
                            foreach ($ufs as $uf):
                            ?>
                            <option value="<?= $uf ?>" <?= $empresa->estado === $uf ? 'selected' : '' ?>><?= $uf ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <h6 class="text-primary fw-bold mb-3 mt-4"><i class="bi bi-image me-1"></i>Logomarca</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Imagem da Logo</label>
                        <input type="file" name="logomarca" class="form-control"
                               accept="image/jpeg,image/png,image/gif,image/webp">
                        <div class="form-text">JPG, PNG, GIF ou WebP. Tamanho máximo: 2MB.</div>
                    </div>
                    <?php if ($empresa->logomarca): ?>
                    <div class="col-md-6 d-flex align-items-center gap-3">
                        <img src="<?= BASE_URL ?>/assets/logos/<?= e($empresa->logomarca) ?>"
                             alt="Logo atual" style="max-height:80px;max-width:150px;object-fit:contain"
                             class="border rounded p-1">
                        <span class="text-muted small">Logo atual<br>(envie nova imagem para substituir)</span>
                    </div>
                    <?php endif; ?>
                </div>

                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="ativo" id="ativo"
                               <?= $empresa->ativo ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="ativo">Empresa ativa</label>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= BASE_URL ?>/empresas" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Salvar Empresa
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Máscara CNPJ
document.getElementById('cnpj').addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '').slice(0, 14);
    if (v.length > 12) v = v.slice(0,2)+'.'+v.slice(2,5)+'.'+v.slice(5,8)+'/'+v.slice(8,12)+'-'+v.slice(12);
    else if (v.length > 8) v = v.slice(0,2)+'.'+v.slice(2,5)+'.'+v.slice(5,8)+'/'+v.slice(8);
    else if (v.length > 5) v = v.slice(0,2)+'.'+v.slice(2,5)+'.'+v.slice(5);
    else if (v.length > 2) v = v.slice(0,2)+'.'+v.slice(2);
    this.value = v;
});

// Máscara Telefone
document.getElementById('telefone').addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '').slice(0, 11);
    if (v.length > 10) v = '('+v.slice(0,2)+') '+v.slice(2,7)+'-'+v.slice(7);
    else if (v.length > 6) v = '('+v.slice(0,2)+') '+v.slice(2,6)+'-'+v.slice(6);
    else if (v.length > 2) v = '('+v.slice(0,2)+') '+v.slice(2);
    else if (v.length > 0) v = '('+v;
    this.value = v;
});

// Máscara CEP
document.getElementById('cep').addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '').slice(0, 8);
    if (v.length > 5) v = v.slice(0,5)+'-'+v.slice(5);
    this.value = v;
});
</script>
