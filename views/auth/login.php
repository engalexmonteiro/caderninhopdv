<div class="login-wrapper">
    <div class="login-card card shadow-lg">
        <div class="text-center mb-4">
            <div class="login-logo"><i class="bi bi-shop-window"></i></div>
            <h3 class="fw-bold mt-2 text-primary">PDV Sistema</h3>
            <p class="text-muted small">Ponto de Venda</p>
        </div>

        <?php if ($erro !== ''): ?>
        <div class="alert alert-danger py-2">
            <i class="bi bi-exclamation-circle me-2"></i><?= e($erro) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/login" novalidate>
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

        <p class="text-center text-muted small mt-4 mb-0">
            Acesso padrão: <strong>admin@pdv.com</strong> / <strong>admin123</strong>
        </p>
    </div>
</div>
