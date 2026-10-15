<?php

include "conexao.php";

$nome = $_POST["nome"];
$email = $_POST["email"];
$senha = $_POST["senha"];
$datacad = $_POST["datacad"];
$nivel = $_POST["nivel"];
$senha_segura = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuario (nome, email, senha, datacad, nivel_acesso)
        VALUES ('$nome', '$email', '$senha_segura', '$datacad', '$nivel')";


$resultado = mysqli_query($conexao, $sql);

if ($resultado) {
    echo "Usuário cadastrado com sucesso!";
} else {
    echo "Erro ao cadastrar usuário.";
}

?>
