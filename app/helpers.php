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

function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect('/login');
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
