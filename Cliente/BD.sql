-- =======================================================
-- Banco de Dados Oficial: pizzaria_chapada
-- Arquivo de persistência física permanente: BD.sql
-- Última sincronização: 2026-11-25 03:43:34
-- =======================================================

CREATE DATABASE IF NOT EXISTS pizzaria_chapada;
USE pizzaria_chapada;

-- Estrutura da tabela `categoria`
CREATE TABLE IF NOT EXISTS categoria (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Estrutura da tabela `cliente`
CREATE TABLE IF NOT EXISTS cliente (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE DEFAULT NULL,
    senha VARCHAR(255) DEFAULT NULL,
    telefone VARCHAR(20),
    endereco VARCHAR(255),
    datacad DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    nivel_acesso INT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Estrutura da tabela `produto`
CREATE TABLE IF NOT EXISTS produto (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    estoque INT NOT NULL DEFAULT 0,
    id_categoria INT NOT NULL,
    FOREIGN KEY (id_categoria) REFERENCES categoria(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Estrutura da tabela `pedido`
CREATE TABLE IF NOT EXISTS pedido (
    id INT PRIMARY KEY AUTO_INCREMENT,
    data_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(30) NOT NULL DEFAULT 'Pendente',
    valor_total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    forma_pagamento VARCHAR(50) NOT NULL DEFAULT 'Pix',
    id_cliente INT NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Estrutura da tabela `ingrediente`
CREATE TABLE IF NOT EXISTS ingrediente (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    unidade_medida VARCHAR(30) NOT NULL DEFAULT 'gramas',
    quantidade_atual DECIMAL(10, 2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Estrutura da tabela `produto_ingrediente`
CREATE TABLE IF NOT EXISTS produto_ingrediente (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_produto INT NOT NULL,
    id_ingrediente INT NOT NULL,
    quantidade_necessaria DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_produto) REFERENCES produto(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_ingrediente) REFERENCES ingrediente(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Estrutura da tabela `item_pedido`
CREATE TABLE IF NOT EXISTS item_pedido (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_pedido INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedido(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_produto) REFERENCES produto(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dados persistidos na tabela `categoria`
INSERT INTO `categoria` (`id`, `nome`, `descricao`) VALUES ('3', 'Bebidas', '') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `categoria` (`id`, `nome`, `descricao`) VALUES ('4', 'Pizza tradicional', '') ON DUPLICATE KEY UPDATE id=id;

-- Dados persistidos na tabela `cliente`
INSERT INTO `cliente` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `datacad`, `nivel_acesso`) VALUES ('5', 'Lucas Araujo Ambrozio', 'lucas@gmail.com', '$2y$10$VCPw0z4WUDt3Nz/2DZw/F.TO5p/cJ2W7.Z0n.Fnprf0YSLBMowZUS', '55998908028', 'Rua Santo Antonio', '2026-11-25 00:00:00', '2') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `cliente` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `datacad`, `nivel_acesso`) VALUES ('6', 'Mama Cita Junior', 'mamacita@gmail.com', '$2y$10$GWef6prQcsIjAWABKZNAluoCzl/sXr3ZMAa6trOGvZ6/dKEXXo4Y6', '55998908028', 'Rua Santo Antonio', '2026-11-25 00:01:06', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `cliente` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `datacad`, `nivel_acesso`) VALUES ('7', 'Mauricio Junior Vieira', 'jojo@gmail.com', '$2y$10$oULDmyf4mkI4ew.PSoDA6eb3wrG4X.AQbbCRhWmk2egZNbdsgg/kC', '55998908028', 'Rua Santo Antonio', '2026-11-25 00:35:22', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `cliente` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `datacad`, `nivel_acesso`) VALUES ('8', 'adm', 'adm@pizzariadachapada.com', '$2y$12$6jfXXtxwZUzOSDIndLhwsepulbVtIX0vhGAoHiy/glAcciLok2NqW', '', '', '2026-11-25 20:15:00', '2') ON DUPLICATE KEY UPDATE id=id;

-- Dados persistidos na tabela `produto`
INSERT INTO `produto` (`id`, `nome`, `preco`, `estoque`, `id_categoria`) VALUES ('6', 'pizza de calabresa', '43.00', '6', '4') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `produto` (`id`, `nome`, `preco`, `estoque`, `id_categoria`) VALUES ('7', 'Pizza de Frango', '40.00', '6', '4') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `produto` (`id`, `nome`, `preco`, `estoque`, `id_categoria`) VALUES ('8', 'Coca Cola 2L', '11.00', '122', '3') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `produto` (`id`, `nome`, `preco`, `estoque`, `id_categoria`) VALUES ('9', 'Pepsi 2L', '9.00', '55', '3') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `produto` (`id`, `nome`, `preco`, `estoque`, `id_categoria`) VALUES ('10', 'Guaraná 2L', '10.00', '100', '3') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `produto` (`id`, `nome`, `preco`, `estoque`, `id_categoria`) VALUES ('11', 'Portuguesa', '35.00', '43', '4') ON DUPLICATE KEY UPDATE id=id;

-- Dados persistidos na tabela `pedido`
INSERT INTO `pedido` (`id`, `data_pedido`, `status`, `valor_total`, `id_cliente`) VALUES ('7', '2026-11-25 00:00:00', 'Entregue', '163.00', '6') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `pedido` (`id`, `data_pedido`, `status`, `valor_total`, `id_cliente`) VALUES ('8', '2026-11-25 00:00:00', 'Entregue', '46.00', '7') ON DUPLICATE KEY UPDATE id=id;

-- Dados persistidos na tabela `item_pedido`
INSERT INTO `item_pedido` (`id`, `id_pedido`, `id_produto`, `quantidade`, `preco_unitario`) VALUES ('8', '7', '7', '3', '40.00') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `item_pedido` (`id`, `id_pedido`, `id_produto`, `quantidade`, `preco_unitario`) VALUES ('9', '8', '11', '1', '35.00') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `item_pedido` (`id`, `id_pedido`, `id_produto`, `quantidade`, `preco_unitario`) VALUES ('10', '8', '8', '1', '11.00') ON DUPLICATE KEY UPDATE id=id;

