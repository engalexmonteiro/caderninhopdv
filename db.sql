-- Sistema PDV - Banco de Dados
CREATE DATABASE IF NOT EXISTS pdv CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pdv;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('admin','usuario') NOT NULL DEFAULT 'usuario',
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE produtos (
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
);

CREATE TABLE vendas (
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
);

CREATE TABLE venda_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venda_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    preco_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venda_id) REFERENCES vendas(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

-- Usuário admin inserido via setup.php (veja instrucoes no README)

-- Produtos de exemplo
INSERT INTO produtos (codigo, nome, descricao, preco_custo, preco_venda, estoque, unidade) VALUES
('001', 'Coca-Cola 350ml', 'Refrigerante Coca-Cola lata 350ml', 2.50, 5.00, 100, 'UN'),
('002', 'Água Mineral 500ml', 'Água mineral sem gás 500ml', 0.80, 2.50, 200, 'UN'),
('003', 'Pão Francês', 'Pão francês unidade', 0.50, 1.00, 50, 'UN'),
('004', 'Café Expresso', 'Café expresso curto', 1.00, 4.00, 999, 'UN'),
('005', 'Salgado Assado', 'Salgado assado variado', 2.00, 5.00, 30, 'UN');
