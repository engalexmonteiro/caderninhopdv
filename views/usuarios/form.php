<div class="container py-4" style="max-width:600px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-person-<?= $usuario->id > 0 ? 'gear' : 'plus' ?> me-2 text-primary"></i>
            <?= e($pageTitle) ?>
        </h4>
        <a href="<?= BASE_URL ?>/usuarios" class="btn btn-outline-secondary btn-sm">
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
                  action="<?= BASE_URL . ($usuario->id > 0 ? '/usuarios/editar/' . $usuario->id : '/usuarios/novo') ?>"
                  novalidate>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control"
                               value="<?= e($usuario->nome) ?>" required autofocus>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">E-mail <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control"
                               value="<?= e($usuario->email) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Senha <?= $usuario->id > 0 ? '<span class="text-muted small">(vazio = manter)</span>' : '<span class="text-danger">*</span>' ?>
                        </label>
                        <input type="password" name="senha" class="form-control"
                               placeholder="Mínimo 6 caracteres"
                               <?= $usuario->id === 0 ? 'required' : '' ?>>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Confirmar Senha</label>
                        <input type="password" name="confirmacao" class="form-control"
                               placeholder="Repita a senha">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Perfil</label>
                        <select name="perfil" class="form-select">
                            <option value="usuario" <?= $usuario->perfil === 'usuario' ? 'selected' : '' ?>>Usuário</option>
                            <option value="admin"   <?= $usuario->perfil === 'admin'   ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end pb-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="ativo" id="ativo"
                                   <?= $usuario->ativo ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="ativo">Usuário ativo</label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="<?= BASE_URL ?>/usuarios" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Salvar Usuário
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
