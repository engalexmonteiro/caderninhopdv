<?php
/**
 * Script de instalação do PDV.
 * Acesse UMA VEZ pelo navegador: http://localhost/setup.php
 * Depois APAGUE este arquivo.
 */

// Configuração direta (sem require para facilitar execução inicial)
$host = 'localhost';
$user = 'root';
$pass = '';
$name = 'pdv';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Criar banco
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$name`");

    // Tabelas
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL,
        perfil ENUM('admin','usuario') NOT NULL DEFAULT 'usuario',
        ativo TINYINT(1) NOT NULL DEFAULT 1,
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS produtos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        codigo VARCHAR(50) NOT NULL UNIQUE,
        nome VARCHAR(150) NOT NULL,
        descricao TEXT,
        preco_custo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        preco_venda DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        estoque INT NOT NULL DEFAULT 0,
        unidade VARCHAR(10) NOT NULL DEFAULT 'UN',
        ativo TINYINT(1) NOT NULL DEFAULT 1,
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS vendas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        desconto DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        forma_pagamento ENUM('dinheiro','cartao_credito','cartao_debito','pix') NOT NULL DEFAULT 'dinheiro',
        valor_pago DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        troco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        status ENUM('concluida','cancelada') NOT NULL DEFAULT 'concluida',
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS venda_itens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        venda_id INT NOT NULL,
        produto_id INT NOT NULL,
        quantidade INT NOT NULL DEFAULT 1,
        preco_unitario DECIMAL(10,2) NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (venda_id) REFERENCES vendas(id),
        FOREIGN KEY (produto_id) REFERENCES produtos(id)
    )");

    // Admin padrão
    $adminEmail = 'admin@pdv.com';
    $adminSenha = 'admin123';
    $chk = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
    $chk->execute([$adminEmail]);
    if (!$chk->fetch()) {
        $hash = password_hash($adminSenha, PASSWORD_DEFAULT);
        $ins  = $pdo->prepare('INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?,?,?,?)');
        $ins->execute(['Administrador', $adminEmail, $hash, 'admin']);
        $adminCriado = true;
    } else {
        $adminCriado = false;
    }

    // Produtos de exemplo
    $produtos = [
        ['001', 'Coca-Cola 350ml',    'Refrigerante lata 350ml', 2.50, 5.00,  100, 'UN'],
        ['002', 'Água Mineral 500ml', 'Água s/ gás 500ml',       0.80, 2.50,  200, 'UN'],
        ['003', 'Pão Francês',        'Pão francês unidade',      0.50, 1.00,   50, 'UN'],
        ['004', 'Café Expresso',      'Café expresso curto',      1.00, 4.00,  999, 'UN'],
        ['005', 'Salgado Assado',     'Salgado assado variado',   2.00, 5.00,   30, 'UN'],
    ];

    $insProd = $pdo->prepare('INSERT IGNORE INTO produtos (codigo,nome,descricao,preco_custo,preco_venda,estoque,unidade) VALUES (?,?,?,?,?,?,?)');
    foreach ($produtos as $p) {
        $insProd->execute($p);
    }

    $ok = true;
    $erro = '';
} catch (Exception $e) {
    $ok   = false;
    $erro = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Setup — PDV Sistema</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:600px">
    <div class="card shadow">
        <div class="card-header bg-primary text-white fw-bold fs-5">
            <i class="bi bi-gear me-2"></i>PDV Sistema — Instalação
        </div>
        <div class="card-body">
            <?php if ($ok): ?>
            <div class="alert alert-success">
                <h5><i class="bi bi-check-circle me-2"></i>Instalação concluída!</h5>
                <ul class="mb-0 mt-2">
                    <li>Banco de dados <strong>pdv</strong> criado/verificado.</li>
                    <li>Tabelas criadas com sucesso.</li>
                    <?php if ($adminCriado): ?>
                    <li>Usuário admin criado: <strong>admin@pdv.com</strong> / <strong>admin123</strong></li>
                    <?php else: ?>
                    <li>Usuário admin já existia (não alterado).</li>
                    <?php endif; ?>
                    <li>Produtos de exemplo inseridos.</li>
                </ul>
            </div>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Importante:</strong> Apague o arquivo <code>setup.php</code> após a instalação!
            </div>
            <a href="/login.php" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Ir para o Login
            </a>
            <?php else: ?>
            <div class="alert alert-danger">
                <h5><i class="bi bi-x-circle me-2"></i>Erro na instalação</h5>
                <pre class="mb-0 mt-2"><?= htmlspecialchars($erro) ?></pre>
            </div>
            <p>Verifique as credenciais do banco em <code>setup.php</code> e tente novamente.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>
