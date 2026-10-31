-- =======================================================
-- Banco de Dados Oficial: pizzaria_chapada
-- Arquivo de persistência física permanente: BD.sql
-- Última sincronização: 2026-10-31 00:01:22
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
    id_cliente INT NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id) ON UPDATE CASCADE
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
INSERT INTO `categoria` (`id`, `nome`, `descricao`) VALUES ('1', 'Pizzas Salgadas', 'Pizzas tradicionais salgadas') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `categoria` (`id`, `nome`, `descricao`) VALUES ('2', 'Bebidas Especiais', 'Sucos naturais e vinhos') ON DUPLICATE KEY UPDATE id=id;

-- Dados persistidos na tabela `cliente`
INSERT INTO `cliente` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `datacad`, `nivel_acesso`) VALUES ('1', 'breno', 'aidento@gmail.com', '$2y$12$Tj8ztXR27p9tD7AMk/3SIu5yVwkxQW/po4IpzjkurUFQGuO2ucmkG', '12345678', '234567', '5222-04-23 00:00:00', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `cliente` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `datacad`, `nivel_acesso`) VALUES ('2', '2345', 'vefiEFVIV@gmail.com', '$2y$12$AXNcET/Nr8DpVZgnp0s4w.RkwLVJqzM9cqD.cAQkPUftKdD7feHgq', '12345676543', 'wertyu', '2026-10-18 00:00:00', '2') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `cliente` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `datacad`, `nivel_acesso`) VALUES ('3', 'Maria Silva', 'maria@gmail.com', '$2y$12$swMWS3tf5LMdRUiuAnDIo.RsBX5n7Pkq7UiGItT1m8s4Pc87HHPsy', '75988888888', 'Rua das Flores', '2026-10-30 00:00:00', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `cliente` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `datacad`, `nivel_acesso`) VALUES ('7', 'rodriguo', 'rofuinho@gmail.com', '$2y$12$ptuwDpAT7VyzGLzjM7/J1efVeNE0kE6PcbhxodyNtN.lGGiFytjLi', '7599123678', 'aidento67', '2026-10-30 00:00:00', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `cliente` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `datacad`, `nivel_acesso`) VALUES ('8', 'teste', '1234567890@gmail.com', '$2y$12$yXe6HWiXMX9akvBq9TFbOOJ3u8hSBf2T7RNkaInITozE1JxPZUVIu', '7654334567', 'weyfvdsjhcbufydshb', '2026-10-31 00:00:00', '1') ON DUPLICATE KEY UPDATE id=id;

-- Dados persistidos na tabela `produto`
INSERT INTO `produto` (`id`, `nome`, `preco`, `estoque`, `id_categoria`) VALUES ('1', 'Pizza Calabresa', '45.50', '8', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `produto` (`id`, `nome`, `preco`, `estoque`, `id_categoria`) VALUES ('2', 'Pizza Quatro Queijos', '49.90', '15', '1') ON DUPLICATE KEY UPDATE id=id;

-- Dados persistidos na tabela `pedido`
INSERT INTO `pedido` (`id`, `data_pedido`, `status`, `valor_total`, `id_cliente`) VALUES ('1', '2026-10-30 23:18:23', 'Pendente', '91.00', '1') ON DUPLICATE KEY UPDATE id=id;

-- Dados persistidos na tabela `item_pedido`
INSERT INTO `item_pedido` (`id`, `id_pedido`, `id_produto`, `quantidade`, `preco_unitario`) VALUES ('1', '1', '1', '2', '45.50') ON DUPLICATE KEY UPDATE id=id;

