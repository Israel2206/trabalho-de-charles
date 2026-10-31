<?php
// Configurações flexíveis de conexão com o banco de dados
$servidor = "localhost";
$banco = "pizzaria_chapada";

$credenciais = [
    ["usuario" => "breno", "senha" => "Breno.2008"],
    ["usuario" => "root",  "senha" => ""],
    ["usuario" => "root",  "senha" => "root"],
];

mysqli_report(MYSQLI_REPORT_OFF);

$conexao = false;
foreach ($credenciais as $cred) {
    $link = @mysqli_connect($servidor, $cred["usuario"], $cred["senha"]);
    if ($link) {
        $conexao = $link;
        break;
    }
}

if (!$conexao) {
    die("Erro ao conectar ao servidor MySQL: Não foi possível autenticar.");
}

mysqli_set_charset($conexao, "utf8mb4");

// Garante a existência do banco oficial
@mysqli_query($conexao, "CREATE DATABASE IF NOT EXISTS `$banco` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

if (!@mysqli_select_db($conexao, $banco)) {
    $banco_fallback = "Breno3182i";
    @mysqli_query($conexao, "CREATE DATABASE IF NOT EXISTS `$banco_fallback` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    if (@mysqli_select_db($conexao, $banco_fallback)) {
        $banco = $banco_fallback;
    } else {
        die("Erro ao selecionar o banco de dados: " . mysqli_error($conexao));
    }
}

/**
 * Função para sincronizar todo o estado do banco diretamente no arquivo BD.sql
 * Esta função garante que todas as inserções, edições e exclusões sejam
 * persistidas fisicamente no arquivo BD.sql.
 */
function sincronizar_bd_sql($conexao) {
    $caminho_bd_sql = __DIR__ . "/BD.sql";

    $sql = "-- =======================================================\n";
    $sql .= "-- Banco de Dados Oficial: pizzaria_chapada\n";
    $sql .= "-- Arquivo de persistência física permanente: BD.sql\n";
    $sql .= "-- Última sincronização: " . date("Y-m-d H:i:s") . "\n";
    $sql .= "-- =======================================================\n\n";
    $sql .= "CREATE DATABASE IF NOT EXISTS pizzaria_chapada;\n";
    $sql .= "USE pizzaria_chapada;\n\n";

    $tabelas = [
        "categoria" => "CREATE TABLE IF NOT EXISTS categoria (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "cliente" => "CREATE TABLE IF NOT EXISTS cliente (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE DEFAULT NULL,
    senha VARCHAR(255) DEFAULT NULL,
    telefone VARCHAR(20),
    endereco VARCHAR(255),
    datacad DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    nivel_acesso INT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "produto" => "CREATE TABLE IF NOT EXISTS produto (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    estoque INT NOT NULL DEFAULT 0,
    id_categoria INT NOT NULL,
    FOREIGN KEY (id_categoria) REFERENCES categoria(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "pedido" => "CREATE TABLE IF NOT EXISTS pedido (
    id INT PRIMARY KEY AUTO_INCREMENT,
    data_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(30) NOT NULL DEFAULT 'Pendente',
    valor_total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    id_cliente INT NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "item_pedido" => "CREATE TABLE IF NOT EXISTS item_pedido (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_pedido INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedido(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_produto) REFERENCES produto(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ];

    foreach ($tabelas as $tab => $schema) {
        $sql .= "-- Estrutura da tabela `$tab`\n$schema\n\n";
    }

    foreach (array_keys($tabelas) as $tab) {
        $res = @mysqli_query($conexao, "SELECT * FROM `$tab` ORDER BY id ASC");
        if ($res && mysqli_num_rows($res) > 0) {
            $sql .= "-- Dados persistidos na tabela `$tab`\n";
            while ($row = mysqli_fetch_assoc($res)) {
                $campos = array_keys($row);
                $valores = [];
                foreach ($row as $v) {
                    if ($v === null) {
                        $valores[] = "NULL";
                    } else {
                        $valores[] = "'" . mysqli_real_escape_string($conexao, $v) . "'";
                    }
                }
                $sql .= "INSERT INTO `$tab` (`" . implode("`, `", $campos) . "`) VALUES (" . implode(", ", $valores) . ") ON DUPLICATE KEY UPDATE id=id;\n";
            }
            $sql .= "\n";
        }
    }

    @file_put_contents($caminho_bd_sql, $sql);
}

/**
 * Carrega a estrutura e os dados do arquivo BD.sql se o banco estiver vazio ou incompleto
 */
function inicializar_a_partir_de_bd_sql($conexao) {
    $caminho_bd_sql = __DIR__ . "/BD.sql";
    if (file_exists($caminho_bd_sql)) {
        // Verifica se a tabela categoria existe
        $res = @mysqli_query($conexao, "SHOW TABLES LIKE 'categoria'");
        if (!$res || mysqli_num_rows($res) === 0) {
            $conteudo = file_get_contents($caminho_bd_sql);
            if (!empty(trim($conteudo))) {
                if (mysqli_multi_query($conexao, $conteudo)) {
                    do {
                        if ($r = mysqli_store_result($conexao)) {
                            mysqli_free_result($r);
                        }
                    } while (mysqli_more_results($conexao) && mysqli_next_result($conexao));
                }
            }
        }
    }
}

inicializar_a_partir_de_bd_sql($conexao);
?>