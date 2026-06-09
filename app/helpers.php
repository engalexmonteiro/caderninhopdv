<?php

function render(string $view, array $data = []): void {
    extract($data, EXTR_SKIP);
    include BASE_PATH . '/views/layout/header.php';
    include BASE_PATH . '/views/' . $view . '.php';
    include BASE_PATH . '/views/layout/footer.php';
}

function jsonResponse(array $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function redirect(string $path, string $flashKey = '', string $flashMsg = ''): never {
    if ($flashKey !== '' && $flashMsg !== '') {
        $_SESSION['flash'][$flashKey] = $flashMsg;
    }
    header('Location: ' . BASE_URL . $path);
    exit;
}

function flash(string $key): string {
    $value = $_SESSION['flash'][$key] ?? '';
    unset($_SESSION['flash'][$key]);
    return $value;
}

function auth(): array {
    return [
        'id'     => $_SESSION['user_id']     ?? null,
        'nome'   => $_SESSION['user_nome']   ?? '',
        'perfil' => $_SESSION['user_perfil'] ?? '',
    ];
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return ($_SESSION['user_perfil'] ?? '') === 'admin';
}

function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string {
    return '<input type="hidden" name="_csrf" value="' . e(csrfToken()) . '">';
}

function verifyCsrf(): void {
    $token = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!hash_equals(csrfToken(), $token)) {
        http_response_code(403);
        exit('Requisição inválida.');
    }
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verifyCsrf();
    }
}

function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        redirect('/dashboard?erro=acesso_negado');
    }
}

function e(mixed $val): string {
    return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
}

function money(float $val): string {
    return 'R$ ' . number_format($val, 2, ',', '.');
}

function formatCnpj(string $cnpj): string {
    $c = preg_replace('/\D/', '', $cnpj);
    if (strlen($c) !== 14) return $cnpj;
    return substr($c, 0, 2) . '.' . substr($c, 2, 3) . '.' . substr($c, 5, 3)
         . '/' . substr($c, 8, 4) . '-' . substr($c, 12, 2);
}

function appContext(): array {
    static $context = null;
    if ($context !== null) {
        return $context;
    }

    try {
        $pdo = getDB();
        $empresaRepo = new App\Repositories\EmpresaRepository($pdo);
        $service = new App\Services\PersonalizacaoService(
            new App\Repositories\PersonalizacaoRepository($pdo),
            $empresaRepo
        );
        $context = $service->contexto();
    } catch (Throwable) {
        $context = [
            'personalizacao' => new App\Models\Personalizacao(),
            'nomeAplicacao' => 'PDV Sistema',
            'paleta' => App\Services\PersonalizacaoService::PALETAS['azul'],
        ];
    }

    return $context;
}

function appName(): string {
    return appContext()['nomeAplicacao'] ?? 'PDV Sistema';
}

function assetUrl(string $path): string {
    return BASE_URL . '/' . ltrim($path, '/');
}
