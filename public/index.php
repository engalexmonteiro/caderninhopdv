<?php
define('BASE_PATH', dirname(__DIR__));

session_start();

spl_autoload_register(function (string $class): void {
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', str_replace('App\\', '', $class)) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

require BASE_PATH . '/config/db.php';
require BASE_PATH . '/app/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
define('BASE_URL', $base);
if ($base && str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base));
}
$uri    = rtrim($uri, '/') ?: '/';

$routes = [
    'GET /'                      => ['Controllers\\AuthController',      'showLogin'],
    'GET /login'                 => ['Controllers\\AuthController',      'showLogin'],
    'POST /login'                => ['Controllers\\AuthController',      'login'],
    'GET /logout'                => ['Controllers\\AuthController',      'logout'],
    'GET /dashboard'             => ['Controllers\\DashboardController', 'index'],
    'GET /produtos'              => ['Controllers\\ProdutoController',   'index'],
    'GET /produtos/novo'         => ['Controllers\\ProdutoController',   'create'],
    'POST /produtos/novo'        => ['Controllers\\ProdutoController',   'store'],
    'GET /produtos/importar'     => ['Controllers\\ProdutoController',   'importForm'],
    'POST /produtos/importar'    => ['Controllers\\ProdutoController',   'import'],
    'GET /produtos/editar/{id}'  => ['Controllers\\ProdutoController',   'edit'],
    'POST /produtos/editar/{id}' => ['Controllers\\ProdutoController',   'update'],
    'GET /produtos/toggle/{id}'  => ['Controllers\\ProdutoController',   'toggle'],
    'GET /produtos/excluir/{id}' => ['Controllers\\ProdutoController',   'destroy'],
    'GET /produtos/categorias'              => ['Controllers\\CategoriaProdutoController', 'index'],
    'GET /produtos/categorias/nova'         => ['Controllers\\CategoriaProdutoController', 'create'],
    'POST /produtos/categorias/nova'        => ['Controllers\\CategoriaProdutoController', 'store'],
    'GET /produtos/categorias/editar/{id}'  => ['Controllers\\CategoriaProdutoController', 'edit'],
    'POST /produtos/categorias/editar/{id}' => ['Controllers\\CategoriaProdutoController', 'update'],
    'GET /produtos/categorias/toggle/{id}'  => ['Controllers\\CategoriaProdutoController', 'toggle'],
    'GET /produtos/subcategorias'              => ['Controllers\\SubcategoriaProdutoController', 'index'],
    'GET /produtos/subcategorias/nova'         => ['Controllers\\SubcategoriaProdutoController', 'create'],
    'POST /produtos/subcategorias/nova'        => ['Controllers\\SubcategoriaProdutoController', 'store'],
    'GET /produtos/subcategorias/editar/{id}'  => ['Controllers\\SubcategoriaProdutoController', 'edit'],
    'POST /produtos/subcategorias/editar/{id}' => ['Controllers\\SubcategoriaProdutoController', 'update'],
    'GET /produtos/subcategorias/toggle/{id}'  => ['Controllers\\SubcategoriaProdutoController', 'toggle'],
    'GET /usuarios'              => ['Controllers\\UsuarioController',   'index'],
    'GET /usuarios/novo'         => ['Controllers\\UsuarioController',   'create'],
    'POST /usuarios/novo'        => ['Controllers\\UsuarioController',   'store'],
    'GET /usuarios/editar/{id}'  => ['Controllers\\UsuarioController',   'edit'],
    'POST /usuarios/editar/{id}' => ['Controllers\\UsuarioController',   'update'],
    'GET /usuarios/toggle/{id}'  => ['Controllers\\UsuarioController',   'toggle'],
    'GET /usuarios/perfis'              => ['Controllers\\PerfilUsuarioController', 'index'],
    'GET /usuarios/perfis/novo'         => ['Controllers\\PerfilUsuarioController', 'create'],
    'POST /usuarios/perfis/novo'        => ['Controllers\\PerfilUsuarioController', 'store'],
    'GET /usuarios/perfis/editar/{id}'  => ['Controllers\\PerfilUsuarioController', 'edit'],
    'POST /usuarios/perfis/editar/{id}' => ['Controllers\\PerfilUsuarioController', 'update'],
    'GET /usuarios/perfis/toggle/{id}'  => ['Controllers\\PerfilUsuarioController', 'toggle'],
    'GET /pdv'                        => ['Controllers\\PdvController',       'index'],
    'POST /pdv/finalizar'             => ['Controllers\\PdvController',       'finalizar'],
    'POST /pdv/caixa/abrir'           => ['Controllers\\PdvController',       'abrirCaixa'],
    'POST /pdv/caixa/sangria'         => ['Controllers\\PdvController',       'sangria'],
    'POST /pdv/caixa/reforco'         => ['Controllers\\PdvController',       'reforco'],
    'POST /pdv/caixa/fechar'          => ['Controllers\\PdvController',       'fecharCaixa'],
    'GET /vendas'                     => ['Controllers\\VendaController',     'index'],
    'GET /vendas/recibo/{id}'         => ['Controllers\\VendaController',     'recibo'],
    'GET /empresas'                   => ['Controllers\\EmpresaController',   'index'],
    'GET /empresas/nova'              => ['Controllers\\EmpresaController',   'create'],
    'POST /empresas/nova'             => ['Controllers\\EmpresaController',   'store'],
    'GET /empresas/editar/{id}'       => ['Controllers\\EmpresaController',   'edit'],
    'POST /empresas/editar/{id}'      => ['Controllers\\EmpresaController',   'update'],
    'GET /empresas/toggle/{id}'       => ['Controllers\\EmpresaController',   'toggle'],
    'GET /personalizacao'             => ['Controllers\\PersonalizacaoController', 'edit'],
    'POST /personalizacao'            => ['Controllers\\PersonalizacaoController', 'update'],
    'GET /financeiro/tipos-pagamento'              => ['Controllers\\TipoPagamentoController', 'index'],
    'GET /financeiro/tipos-pagamento/novo'         => ['Controllers\\TipoPagamentoController', 'create'],
    'POST /financeiro/tipos-pagamento/novo'        => ['Controllers\\TipoPagamentoController', 'store'],
    'GET /financeiro/tipos-pagamento/editar/{id}'  => ['Controllers\\TipoPagamentoController', 'edit'],
    'POST /financeiro/tipos-pagamento/editar/{id}' => ['Controllers\\TipoPagamentoController', 'update'],
    'GET /financeiro/tipos-pagamento/toggle/{id}'  => ['Controllers\\TipoPagamentoController', 'toggle'],
    'GET /financeiro/formas-pagamento'              => ['Controllers\\FormaPagamentoController', 'index'],
    'GET /financeiro/formas-pagamento/nova'         => ['Controllers\\FormaPagamentoController', 'create'],
    'POST /financeiro/formas-pagamento/nova'        => ['Controllers\\FormaPagamentoController', 'store'],
    'GET /financeiro/formas-pagamento/editar/{id}'  => ['Controllers\\FormaPagamentoController', 'edit'],
    'POST /financeiro/formas-pagamento/editar/{id}' => ['Controllers\\FormaPagamentoController', 'update'],
    'GET /financeiro/formas-pagamento/toggle/{id}'  => ['Controllers\\FormaPagamentoController', 'toggle'],
];

$matched = false;

foreach ($routes as $route => $handler) {
    [$routeMethod, $routePath] = explode(' ', $route, 2);

    if ($routeMethod !== $method) {
        continue;
    }

    $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $routePath);
    $pattern = '#^' . $pattern . '$#';

    if (preg_match($pattern, $uri, $matches)) {
        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        [$controllerClass, $action] = $handler;
        $fullClass  = 'App\\' . $controllerClass;
        $controller = new $fullClass();
        $controller->$action(...array_values($params));
        $matched = true;
        break;
    }
}

if (!$matched) {
    http_response_code(404);
    echo '<div style="font-family:sans-serif;padding:3rem;text-align:center">
            <h2>404 — Página não encontrada</h2>
            <a href="/dashboard">Voltar ao início</a>
          </div>';
}
