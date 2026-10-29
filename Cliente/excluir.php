<?php
include "conexao.php";

// Recebe e sanitiza a tabela e o identificador via GET
$tabela = isset($_GET['tabela']) ? $_GET['tabela'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lista de tabelas permitidas e páginas de retorno (proteção contra SQL Injection)
$tabelas_permitidas = [
    'cliente'   => 'consultar_user.php',
    'categoria' => 'consultar_categoria.php',
    'produto'   => 'consultar_produto.php',
    'pedido'    => 'consultar_pedido.php'
];

// Valida se a tabela pertence à lista autorizada e se o ID é válido (> 0)
if (array_key_exists($tabela, $tabelas_permitidas) && $id > 0) {
    $sql = "DELETE FROM `$tabela` WHERE id = $id";
    mysqli_query($conexao, $sql);

    // Retorna para a listagem correspondente
    header("Location: " . $tabelas_permitidas[$tabela]);
    exit;
} else {
    die("Ação inválida.");
}
?>
