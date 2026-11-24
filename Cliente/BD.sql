-- =======================================================
-- Banco de Dados Oficial: pizzaria_chapada
-- Arquivo de persistência física permanente: BD.sql
-- Última sincronização: 2026-11-17 08:21:44
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
INSERT INTO `categoria` (`id`, `nome`, `descricao`) VALUES ('1', 'Pizza Salgadas', 'TEM SAL') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `categoria` (`id`, `nome`, `descricao`) VALUES ('2', 'Bebidas', 'WOW') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `categoria` (`id`, `nome`, `descricao`) VALUES ('3', 'Bebidas', '') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `categoria` (`id`, `nome`, `descricao`) VALUES ('4', 'Pizzza tradicional', '') ON DUPLICATE KEY UPDATE id=id;

-- Dados persistidos na tabela `cliente`
INSERT INTO `cliente` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `datacad`, `nivel_acesso`) VALUES ('1', 'aidento', 'pudimlako@gmail.com', '$2y$10$7i4UXoRo1rG6ojl3WvjHZOzPfL3H1CaA8wgRqeyyZHtSBZfCRJv5m', '55998908028', 'Rua Santo Antonio', '2026-10-29 00:00:00', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `cliente` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `datacad`, `nivel_acesso`) VALUES ('2', 'Luartee', 'lakopudim@gmail.com', '$2y$10$OwPU8pXW7g9CofcwX3x23OKLYjfDqnkKwbiTq94vjoFlPb/Yaecae', '756322367', 'Rua Paraiba', '2026-11-17 00:00:00', '1') ON DUPLICATE KEY UPDATE id=id;

-- Dados persistidos na tabela `produto`
INSERT INTO `produto` (`id`, `nome`, `preco`, `estoque`, `id_categoria`) VALUES ('1', 'Pizza de Frango', '40.00', '9', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `produto` (`id`, `nome`, `preco`, `estoque`, `id_categoria`) VALUES ('2', 'Pizza de Queijo', '40.00', '10', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `produto` (`id`, `nome`, `preco`, `estoque`, `id_categoria`) VALUES ('3', 'Pizza de Calabresa', '43.00', '3', '1') ON DUPLICATE KEY UPDATE id=id;

-- Dados persistidos na tabela `pedido`
INSERT INTO `pedido` (`id`, `data_pedido`, `status`, `valor_total`, `id_cliente`) VALUES ('1', '2026-10-29 00:00:00', 'Pendente', '40.00', '1') ON DUPLICATE KEY UPDATE id=id;

-- Dados persistidos na tabela `item_pedido`
INSERT INTO `item_pedido` (`id`, `id_pedido`, `id_produto`, `quantidade`, `preco_unitario`) VALUES ('1', '1', '1', '1', '40.00') ON DUPLICATE KEY UPDATE id=id;

