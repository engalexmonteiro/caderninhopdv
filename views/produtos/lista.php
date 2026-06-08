<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2 text-primary"></i>Produtos</h4>
        <?php if (isAdmin()): ?>
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>/produtos/importar" class="btn btn-outline-primary">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>Importar XLSX
            </a>
            <a href="<?= BASE_URL ?>/produtos/novo" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Novo Produto
            </a>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($flash !== ''): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i><?= e($flash) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="<?= BASE_URL ?>/produtos" class="d-flex gap-2">
                <input type="text" name="q" value="<?= e($busca) ?>"
                       class="form-control" placeholder="Buscar por descrição, código de barras ou categoria...">
                <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                <?php if ($busca !== ''): ?>
                <a href="<?= BASE_URL ?>/produtos" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Código de Barras</th>
                            <th>Descrição</th>
                            <th>Categoria</th>
                            <th>Un.</th>
                            <th class="text-end">Custo</th>
                            <th class="text-end">Varejo</th>
                            <th class="text-center">Estoque</th>
                            <th class="text-center">Mov.</th>
                            <th class="text-center">Status</th>
                            <?php if (isAdmin()): ?><th class="text-center">Ações</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($produtos)): ?>
                        <tr><td colspan="<?= isAdmin() ? 10 : 9 ?>" class="text-center text-muted py-4">Nenhum produto encontrado.</td></tr>
                        <?php else: ?>
                        <?php foreach ($produtos as $p): ?>
                        <tr class="<?= !$p->ativo ? 'table-secondary text-muted' : '' ?>">
                            <td><code><?= e($p->codigoBarras) ?></code></td>
                            <td class="fw-semibold"><?= e($p->descricao) ?></td>
                            <td><?= e($p->categoriaProduto) ?></td>
                            <td><?= e($p->unidade) ?></td>
                            <td class="text-end"><?= money($p->precoCusto) ?></td>
                            <td class="text-end fw-bold text-success"><?= money($p->precoVendaVarejo) ?></td>
                            <td class="text-center">
                                <span class="badge <?= $p->quantidadeEstoque > 5 ? 'bg-success' : ($p->quantidadeEstoque > 0 ? 'bg-warning text-dark' : 'bg-danger') ?>">
                                    <?= $p->quantidadeEstoque ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $p->movimentaEstoque ? 'bg-info text-dark' : 'bg-light text-muted' ?>">
                                    <?= $p->movimentaEstoque ? 'Sim' : 'Não' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $p->ativo ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $p->ativo ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                            <?php if (isAdmin()): ?>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>/produtos/editar/<?= $p->id ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/produtos/toggle/<?= $p->id ?>"
                                   class="btn btn-sm btn-outline-<?= $p->ativo ? 'warning' : 'success' ?> me-1"
                                   title="<?= $p->ativo ? 'Desativar' : 'Ativar' ?>">
                                    <i class="bi bi-toggle-<?= $p->ativo ? 'on' : 'off' ?>"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/produtos/excluir/<?= $p->id ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Excluir este produto?')" title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small"><?= count($produtos) ?> produto(s)</div>
    </div>
</div>
