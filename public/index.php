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
    'GET /produtos/editar/{id}'  => ['Controllers\\ProdutoController',   'edit'],
    'POST /produtos/editar/{id}' => ['Controllers\\ProdutoController',   'update'],
    'GET /produtos/toggle/{id}'  => ['Controllers\\ProdutoController',   'toggle'],
    'GET /produtos/excluir/{id}' => ['Controllers\\ProdutoController',   'destroy'],
    'GET /usuarios'              => ['Controllers\\UsuarioController',   'index'],
    'GET /usuarios/novo'         => ['Controllers\\UsuarioController',   'create'],
    'POST /usuarios/novo'        => ['Controllers\\UsuarioController',   'store'],
    'GET /usuarios/editar/{id}'  => ['Controllers\\UsuarioController',   'edit'],
    'POST /usuarios/editar/{id}' => ['Controllers\\UsuarioController',   'update'],
    'GET /usuarios/toggle/{id}'  => ['Controllers\\UsuarioController',   'toggle'],
    'GET /pdv'                   => ['Controllers\\PdvController',       'index'],
    'POST /pdv/finalizar'        => ['Controllers\\PdvController',       'finalizar'],
    'GET /vendas'                => ['Controllers\\VendaController',     'index'],
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
