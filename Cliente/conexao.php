<?php

$servidor = "localhost";
$usuario = "breno";
$senha = "Breno.2008";
$banco = "pizzaria_chapada";

// Tenta conectar ao banco pizzaria_chapada; se não existir, tenta Breno3182i
$conexao = @mysqli_connect($servidor, $usuario, $senha, $banco);
if (!$conexao) {
    $banco = "Breno3182i";
    $conexao = @mysqli_connect($servidor, $usuario, $senha, $banco);
}

if (!$conexao) {
    die("Erro na conexão com o banco de dados: " . mysqli_connect_error());
}

mysqli_set_charset($conexao, "utf8mb4");
?>
