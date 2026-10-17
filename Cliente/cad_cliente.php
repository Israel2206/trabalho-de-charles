<?php

include "conexao.php";

$nome = $_POST["nome"];
$telefone = $_POST["telefone"];
$endereco = $_POST["endereco"];


$sql_cliente = "INSERT INTO cliente (nome, telefone, endereco) 
                VALUES ('$nome', '$telefone', '$endereco')";

$res_cliente = mysqli_query($conexao, $sql_cliente);


if ($res_cliente) {
    echo "Cliente cadastrado com sucesso!";
} else {
    echo "Erro ao cadastrar: " . mysqli_error($conexao);
}

?>