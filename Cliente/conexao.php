<?php

$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "Breno3182i";

$conexao = mysqli_connect($servidor,$usuario,$senha,$banco);

if (!$conexao) {
    die("Erro na conexão com o banco de dados");
}

?>
