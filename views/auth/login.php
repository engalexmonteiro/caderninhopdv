<div class="login-wrapper">
    <div class="login-card card shadow-lg">
        <div class="text-center mb-4">
            <?php if (($__personalizacao->logoLogin ?? '') !== ''): ?>
            <img src="<?= assetUrl($__personalizacao->logoLogin) ?>" alt="<?= e($__appName) ?>" class="login-logo-img mb-2">
            <?php else: ?>
            <div class="login-logo"><i class="bi bi-shop-window"></i></div>
            <?php endif; ?>
            <h3 class="fw-bold mt-2 text-primary"><?= e($__appName) ?></h3>
            <p class="text-muted small">Ponto de Venda</p>
        </div>

        <?php if ($erro !== ''): ?>
        <div class="alert alert-danger py-2">
            <i class="bi bi-exclamation-circle me-2"></i><?= e($erro) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/login" novalidate>
            <?= csrfField() ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control form-control-lg"
                           placeholder="seu@email.com"
                           value="<?= e($_POST['email'] ?? '') ?>"
                           autofocus required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Senha</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="senha" class="form-control form-control-lg"
                           placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
            </button>
        </form>

    </div>
</div>
