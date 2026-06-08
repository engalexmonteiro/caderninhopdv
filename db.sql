-- Sistema PDV - Banco de Dados
CREATE DATABASE IF NOT EXISTS pdv CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pdv;

CREATE TABLE IF NOT EXISTS empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    razao_social VARCHAR(200) NOT NULL,
    nome_fantasia VARCHAR(200) NOT NULL DEFAULT '',
    cnpj VARCHAR(14) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL DEFAULT '',
    telefone VARCHAR(20) NOT NULL DEFAULT '',
    endereco VARCHAR(200) NOT NULL DEFAULT '',
    cidade VARCHAR(100) NOT NULL DEFAULT '',
    estado VARCHAR(2) NOT NULL DEFAULT '',
    cep VARCHAR(8) NOT NULL DEFAULT '',
    logomarca VARCHAR(255) NOT NULL DEFAULT '',
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

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
    codigo_barras VARCHAR(50) NOT NULL UNIQUE,
    tipo_produto VARCHAR(50) NOT NULL DEFAULT 'Produto',
    descricao VARCHAR(255) NOT NULL,
    preco_custo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    preco_venda_varejo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    preco_venda_atacado DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    quantidade_minima_atacado INT NOT NULL DEFAULT 0,
    unidade VARCHAR(30) NOT NULL DEFAULT 'Unidade',
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    categoria_produto VARCHAR(150) NOT NULL DEFAULT '',
    subcategoria_produto VARCHAR(150) NOT NULL DEFAULT '',
    movimenta_estoque TINYINT(1) NOT NULL DEFAULT 0,
    estoque_minimo INT NOT NULL DEFAULT 0,
    quantidade_estoque INT NOT NULL DEFAULT 0,
    marca VARCHAR(100) NOT NULL DEFAULT '',
    modelo VARCHAR(100) NOT NULL DEFAULT '',
    codigo_balanca VARCHAR(50) NOT NULL DEFAULT '',
    codigo_interno VARCHAR(50) NOT NULL DEFAULT '',
    tags TEXT NULL,
    tipo VARCHAR(80) NOT NULL DEFAULT '',
    ncm VARCHAR(20) NOT NULL DEFAULT '',
    cfop VARCHAR(20) NOT NULL DEFAULT '',
    origem VARCHAR(80) NOT NULL DEFAULT '',
    cest VARCHAR(20) NOT NULL DEFAULT '',
    categoria_pdv VARCHAR(150) NOT NULL DEFAULT '',
    botao_pdv TINYINT(1) NOT NULL DEFAULT 0,
    categoria_loja_virtual VARCHAR(150) NOT NULL DEFAULT '',
    subcategoria_loja_virtual VARCHAR(150) NOT NULL DEFAULT '',
    nome_loja_virtual VARCHAR(200) NOT NULL DEFAULT '',
    preco_de DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    preco_por DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    altura_cm DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    largura_cm DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    profundidade_cm DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    peso_kg DECIMAL(10,3) NOT NULL DEFAULT 0.000,
    descricao_produto TEXT NULL,
    garantia TEXT NULL,
    itens_inclusos TEXT NULL,
    especificacoes TEXT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    empresa_id INT NULL,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    desconto DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    forma_pagamento ENUM('dinheiro','cartao_credito','cartao_debito','pix') NOT NULL DEFAULT 'dinheiro',
    valor_pago DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    troco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('concluida','cancelada') NOT NULL DEFAULT 'concluida',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (empresa_id) REFERENCES empresas(id)
);

-- Para bancos de dados ja existentes, execute:
-- ALTER TABLE vendas ADD COLUMN empresa_id INT NULL AFTER usuario_id;
-- ALTER TABLE vendas ADD CONSTRAINT fk_venda_empresa FOREIGN KEY (empresa_id) REFERENCES empresas(id);
-- A tabela produtos e migrada automaticamente ao abrir Produtos no sistema.

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

-- Usuario admin inserido via setup.php (veja instrucoes no README)

-- Produtos de exemplo
INSERT INTO produtos
(codigo_barras, tipo_produto, descricao, preco_custo, preco_venda_varejo, unidade, ativo, movimenta_estoque, quantidade_estoque, descricao_produto)
VALUES
('001', 'Produto', 'Coca-Cola 350ml', 2.50, 5.00, 'Unidade', 1, 1, 100, 'Refrigerante Coca-Cola lata 350ml'),
('002', 'Produto', 'Agua Mineral 500ml', 0.80, 2.50, 'Unidade', 1, 1, 200, 'Agua mineral sem gas 500ml'),
('003', 'Produto', 'Pao Frances', 0.50, 1.00, 'Unidade', 1, 1, 50, 'Pao frances unidade'),
('004', 'Produto', 'Cafe Expresso', 1.00, 4.00, 'Unidade', 1, 1, 999, 'Cafe expresso curto'),
('005', 'Produto', 'Salgado Assado', 2.00, 5.00, 'Unidade', 1, 1, 30, 'Salgado assado variado');
