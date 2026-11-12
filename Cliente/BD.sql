-- =======================================================
-- Banco de Dados Oficial: pizzaria_chapada
-- Arquivo de persistência física permanente: BD.sql
-- Última sincronização: 2026-10-20 14:05:57
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

-- Cria a tabela de ingredientes (com o nome único para evitar duplicidade)
CREATE TABLE IF NOT EXISTS ingrediente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    unidade_medida VARCHAR(20) NOT NULL,
    quantidade_atual DECIMAL(10,2) NOT NULL DEFAULT 0.00
);

-- Cria a tabela de ligação da ficha técnica (vincula o produto aos ingredientes da receita)
CREATE TABLE IF NOT EXISTS produto_ingrediente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_produto INT NOT NULL,
    id_ingrediente INT NOT NULL,
    quantidade_necessaria DECIMAL(10,2) NOT NULL
);

-- Dados persistidos na tabela `categoria`
INSERT INTO `categoria` (`id`, `nome`, `descricao`) VALUES ('1', 'Pizzas Salgadas', 'Pizza com sabores Tradicionais') ON DUPLICATE KEY UPDATE id=id;

-- Dados persistidos na tabela `cliente`
INSERT INTO `cliente` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `datacad`, `nivel_acesso`) VALUES ('1', 'Pirarinha Jose Mara', 'mucugegege@gmail.com', '$2y$10$KZwee6gWlzs511v1C6NPdO371EwgxcjSRlHsXQPEGETArR.YkQ21m', '7598896542', 'Rua São goncalo', '2026-10-20 00:00:00', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `cliente` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `datacad`, `nivel_acesso`) VALUES ('2', 'Rayane Beatriz Castro', 'Rayanebeatrizcastro06@gmail.com', '$2y$10$p/NxLmbo8WMUVcVCtba/AOuyWWHlNRchbHnZQlEhXa0Oyxk/8/j9O', '7598843542', 'Rua Jacinto Leite', '2026-10-20 00:00:00', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `cliente` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `datacad`, `nivel_acesso`) VALUES ('3', 'Loiro José Chico Cunha', 'mimizinhodocaixa@gmail.com', '$2y$10$1W/VXPOZTmfe/B5txLG6/ep2FkVX2KlRfclJvKYG28codJXYUStvW', '7598896542', 'Xique-xique Bahia', '2026-10-20 00:00:00', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `cliente` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `datacad`, `nivel_acesso`) VALUES ('4', 'Luna Dente Amassado', 'Luaa32122as@gmail.com', '$2y$10$Cf4anR4uYh7r7egYtjefx.UfPXnc8u5IAQkmdkOKFvIBpcUnfpkw.', '7523645821', 'Pirarucaba', '2026-10-20 00:00:00', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `cliente` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `datacad`, `nivel_acesso`) VALUES ('5', 'Fael Santos Amorim', 'Yeeedsgowe@gmail.com', '$2y$10$lfMJJR/kYft6VPZ2u6lC0.XTjewBZIqUiubpPT/rXgePbnzH0GAYK', '75365412587', 'Rua Lisboa', '2026-10-20 00:00:00', '1') ON DUPLICATE KEY UPDATE id=id;

