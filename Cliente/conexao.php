<?php
// Configurações de conexão flexíveis (suporta o ambiente local de Breno e o ambiente padrão root/XAMPP)
$servidor = "localhost";
$banco = "pizzaria_chapada";

$credenciais = [
    ["usuario" => "breno", "senha" => "Breno.2008"],
    ["usuario" => "root",  "senha" => ""],
    ["usuario" => "root",  "senha" => "root"],
];

mysqli_report(MYSQLI_REPORT_OFF);

$conexao = false;
$usuario_ativo = "";

foreach ($credenciais as $cred) {
    // Tenta conectar diretamente ao servidor MySQL
    $link = @mysqli_connect($servidor, $cred["usuario"], $cred["senha"]);
    if ($link) {
        $conexao = $link;
        $usuario_ativo = $cred["usuario"];
        break;
    }
}

if (!$conexao) {
    die("Erro ao conectar ao MySQL: Não foi possível autenticar com nenhum dos usuários configurados (breno/root).");
}

mysqli_set_charset($conexao, "utf8mb4");

// 1. Garante que o banco de dados principal exista
@mysqli_query($conexao, "CREATE DATABASE IF NOT EXISTS `$banco` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

// Seleciona o banco (ou fallback Breno3182i se pizzaria_chapada não for acessível)
if (!@mysqli_select_db($conexao, $banco)) {
    $banco_fallback = "Breno3182i";
    @mysqli_query($conexao, "CREATE DATABASE IF NOT EXISTS `$banco_fallback` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    if (@mysqli_select_db($conexao, $banco_fallback)) {
        $banco = $banco_fallback;
    } else {
        die("Erro ao selecionar o banco de dados: " . mysqli_error($conexao));
    }
}

// 2. Garante a criação automática de todas as tabelas caso ainda não existam
$tabelas_sql = [
    "cliente" => "CREATE TABLE IF NOT EXISTS cliente (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE DEFAULT NULL,
        senha VARCHAR(255) DEFAULT NULL,
        telefone VARCHAR(20),
        endereco VARCHAR(255),
        datacad DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        nivel_acesso INT NOT NULL DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "categoria" => "CREATE TABLE IF NOT EXISTS categoria (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nome VARCHAR(100) NOT NULL,
        descricao TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "produto" => "CREATE TABLE IF NOT EXISTS produto (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nome VARCHAR(150) NOT NULL,
        preco DECIMAL(10, 2) NOT NULL,
        estoque INT NOT NULL DEFAULT 0,
        id_categoria INT NOT NULL,
        FOREIGN KEY (id_categoria) REFERENCES categoria(id) ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "pedido" => "CREATE TABLE IF NOT EXISTS pedido (
        id INT PRIMARY KEY AUTO_INCREMENT,
        data_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(30) NOT NULL DEFAULT 'Pendente',
        valor_total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        id_cliente INT NOT NULL,
        FOREIGN KEY (id_cliente) REFERENCES cliente(id) ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "item_pedido" => "CREATE TABLE IF NOT EXISTS item_pedido (
        id INT PRIMARY KEY AUTO_INCREMENT,
        id_pedido INT NOT NULL,
        id_produto INT NOT NULL,
        quantidade INT NOT NULL,
        preco_unitario DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (id_pedido) REFERENCES pedido(id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (id_produto) REFERENCES produto(id) ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

foreach ($tabelas_sql as $sql_tabela) {
    @mysqli_query($conexao, $sql_tabela);
}

// 3. Ajusta tabela cliente para permitir cadastro simples (sem obrigatoriedade de email/senha se a tabela foi criada antes)
@mysqli_query($conexao, "ALTER TABLE cliente MODIFY email VARCHAR(100) NULL, MODIFY senha VARCHAR(255) NULL");

// 4. Se a tabela categoria estiver vazia, adiciona categorias padrão para facilitar cadastros imediatos
$check_cat = @mysqli_query($conexao, "SELECT COUNT(*) as total FROM categoria");
if ($check_cat) {
    $row_cat = mysqli_fetch_assoc($check_cat);
    if ((int)$row_cat['total'] === 0) {
        @mysqli_query($conexao, "INSERT INTO categoria (nome, descricao) VALUES
            ('Pizzas Tradicionais', 'Sabores tradicionais como Mussarela, Calabresa e Marguerita'),
            ('Pizzas Especiais', 'Pizzas especiais da casa com ingredientes premium e bordas recheadas'),
            ('Pizzas Doces', 'Sobremesas saborosas como Chocolate, Romeu e Julieta e Banana nevada'),
            ('Bebidas', 'Refrigerantes, sucos naturais e águas')");
    }
}
?>