<?php
include "conexao.php";

$tabela = isset($_GET['tabela']) ? $_GET['tabela'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$tabelas_permitidas = [
    'cliente'   => 'consultar_user.php',
    'categoria' => 'consultar_categoria.php',
    'produto'   => 'consultar_produto.php',
    'pedido'    => 'consultar_pedido.php'
];

if (array_key_exists($tabela, $tabelas_permitidas) && $id > 0) {
    // Monta a query segura para exclusão
    $sql = "DELETE FROM `$tabela` WHERE id = $id";
    mysqli_query($conexao, $sql);

    // Redireciona para a respectiva página de consulta
    header("Location: " . $tabelas_permitidas[$tabela]);
    exit;
} else {
    die("Ação inválida.");
}
?>
