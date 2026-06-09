<!DOCTYPE html>
<?php
$__appContext = appContext();
$__paleta = $__appContext['paleta'];
$__personalizacao = $__appContext['personalizacao'];
$__appName = $__appContext['nomeAplicacao'];
?>
<html lang="pt-BR" data-bs-theme="<?= $__personalizacao->modoNoturno ? 'dark' : 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? $__appName) ?> - <?= e($__appName) ?></title>
    <?php if ($__personalizacao->favicon !== ''): ?>
    <link rel="icon" href="<?= assetUrl($__personalizacao->favicon) ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
    <style>
        :root {
            --bs-primary: <?= e($__paleta['primary']) ?>;
            --bs-primary-rgb: <?= e($__paleta['primary_rgb']) ?>;
            --bs-success: <?= e($__paleta['success']) ?>;
            --app-login-bg: <?= e($__paleta['login_bg']) ?>;
        }
    </style>
</head>
<body class="<?= $__personalizacao->modoNoturno ? 'app-dark-mode' : '' ?>">

<?php if (isLoggedIn()): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/dashboard">
            <i class="bi bi-shop-window me-2"></i><?= e($__appName) ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <?php
                $fullUri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') ?: '/';
                $uri = BASE_URL && str_starts_with($fullUri, BASE_URL) ? substr($fullUri, strlen(BASE_URL)) : $fullUri;
                $uri = $uri ?: '/';

                $mainItems = [
                    '/dashboard' => ['bi-speedometer2', 'Dashboard'],
                    '/pdv' => ['bi-cart3', 'PDV'],
                ];
                if (isAdmin()) {
                    $mainItems['/vendas'] = ['bi-receipt', 'Vendas'];
                }

                foreach ($mainItems as $href => [$icon, $label]):
                    $active = str_starts_with($uri, $href) ? 'active' : '';
                ?>
                <li class="nav-item">
                    <a class="nav-link <?= $active ?>" href="<?= BASE_URL . $href ?>">
                        <i class="bi <?= $icon ?> me-1"></i><?= $label ?>
                    </a>
                </li>
                <?php endforeach; ?>

                <?php
                $cadastroItems = ['/produtos' => ['bi-box-seam', 'Produtos']];
                $financeiroItems = [];
                $produtoConfigItems = [
                    '/produtos/categorias' => ['bi-tags', 'Categoria'],
                    '/produtos/subcategorias' => ['bi-diagram-2', 'Subcategoria'],
                ];
                $usuarioConfigItems = [
                    '/usuarios/perfis' => ['bi-person-badge', 'Perfil de Usuario'],
                ];
                $tipoPagamentoConfigItems = [
                    '/financeiro/formas-pagamento' => ['bi-credit-card', 'Forma de Pagamento'],
                ];
                if (isAdmin()) {
                    $cadastroItems['/usuarios'] = ['bi-people', 'Usuarios'];
                    $cadastroItems['/empresas'] = ['bi-building', 'Empresas'];
                    $financeiroItems['/financeiro/tipos-pagamento'] = ['bi-credit-card-2-front', 'Tipos de Pagamento'];
                }
                $cadastroActive = false;
                foreach (array_merge(array_keys($cadastroItems), array_keys($financeiroItems), array_keys($tipoPagamentoConfigItems), ['/personalizacao']) as $href) {
                    if (str_starts_with($uri, $href)) { $cadastroActive = true; break; }
                }
                ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= $cadastroActive ? 'active' : '' ?>"
                       href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-gear me-1"></i>Configuracoes
                    </a>
                    <ul class="dropdown-menu">
                        <li><h6 class="dropdown-header">
                            <i class="bi bi-card-list me-1"></i>Cadastro
                        </h6></li>
                        <?php foreach ($cadastroItems as $href => [$icon, $label]):
                            $active = str_starts_with($uri, $href) ? 'active' : '';
                        ?>
                        <?php if ($href === '/produtos' || $href === '/usuarios'): ?>
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle <?= $active ?>" href="<?= BASE_URL . $href ?>">
                                <i class="bi <?= $icon ?> me-2"></i><?= $label ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php foreach (($href === '/produtos' ? $produtoConfigItems : $usuarioConfigItems) as $subHref => [$subIcon, $subLabel]):
                                    $subActive = str_starts_with($uri, $subHref) ? 'active' : '';
                                ?>
                                <li>
                                    <a class="dropdown-item <?= $subActive ?>" href="<?= BASE_URL . $subHref ?>">
                                        <i class="bi <?= $subIcon ?> me-2"></i><?= $subLabel ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <?php else: ?>
                        <li>
                            <a class="dropdown-item <?= $active ?>" href="<?= BASE_URL . $href ?>">
                                <i class="bi <?= $icon ?> me-2"></i><?= $label ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if (!empty($financeiroItems)): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">
                            <i class="bi bi-cash-coin me-1"></i>Financeiro
                        </h6></li>
                        <?php foreach ($financeiroItems as $href => [$icon, $label]):
                            $active = str_starts_with($uri, $href) || ($href === '/financeiro/tipos-pagamento' && str_starts_with($uri, '/financeiro/formas-pagamento')) ? 'active' : '';
                        ?>
                        <?php if ($href === '/financeiro/tipos-pagamento'): ?>
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle <?= $active ?>" href="<?= BASE_URL . $href ?>">
                                <i class="bi <?= $icon ?> me-2"></i><?= $label ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php foreach ($tipoPagamentoConfigItems as $subHref => [$subIcon, $subLabel]):
                                    $subActive = str_starts_with($uri, $subHref) ? 'active' : '';
                                ?>
                                <li>
                                    <a class="dropdown-item <?= $subActive ?>" href="<?= BASE_URL . $subHref ?>">
                                        <i class="bi <?= $subIcon ?> me-2"></i><?= $subLabel ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <?php else: ?>
                        <li>
                            <a class="dropdown-item <?= $active ?>" href="<?= BASE_URL . $href ?>">
                                <i class="bi <?= $icon ?> me-2"></i><?= $label ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (isAdmin()): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item <?= str_starts_with($uri, '/personalizacao') ? 'active' : '' ?>" href="<?= BASE_URL ?>/personalizacao">
                                <i class="bi bi-palette me-2"></i>Personalizacao
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= e(auth()['nome']) ?>
                        <span class="badge bg-<?= isAdmin() ? 'warning text-dark' : 'light text-dark' ?> ms-1">
                            <?= isAdmin() ? 'Admin' : 'Usuario' ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="<?= BASE_URL ?>/logout">
                                <i class="bi bi-box-arrow-right me-2"></i>Sair
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>
