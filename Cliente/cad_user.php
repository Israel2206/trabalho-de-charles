<?php
include "conexao.php";

// Captura e sanitiza as informações de cadastro do cliente
$nome = isset($_POST["nome"]) ? mysqli_real_escape_string($conexao, trim($_POST["nome"])) : '';
$email = isset($_POST["email"]) ? mysqli_real_escape_string($conexao, trim($_POST["email"])) : '';
$senha = isset($_POST["senha"]) ? $_POST["senha"] : '';
$telefone = isset($_POST["telefone"]) ? mysqli_real_escape_string($conexao, trim($_POST["telefone"])) : '';
$endereco = isset($_POST["endereco"]) ? mysqli_real_escape_string($conexao, trim($_POST["endereco"])) : '';
$datacad = !empty($_POST["datacad"]) ? $_POST["datacad"] : date('Y-m-d H:i:s');
$nivel = isset($_POST["nivel"]) ? intval($_POST["nivel"]) : 1;

$mensagem = "";
$sucesso = false;

// Valida campos obrigatórios e criptografa a senha antes de salvar
if (!empty($nome) && !empty($email) && !empty($senha)) {
    $senha_segura = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO cliente (nome, email, senha, telefone, endereco, datacad, nivel_acesso)
            VALUES ('$nome', '$email', '$senha_segura', '$telefone', '$endereco', '$datacad', '$nivel')";

    if (mysqli_query($conexao, $sql)) {
        $sucesso = true;
        $mensagem = "Cliente cadastrado com sucesso!";
    } else {
        $mensagem = "Erro ao cadastrar cliente: " . mysqli_error($conexao);
    }
} else {
    $mensagem = "Por favor, preencha todos os campos obrigatórios!";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultado do Cadastro - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'Menu1.php'; ?>

    <div class="container">
        <h1>Status do Cadastro</h1>

        <?php if ($sucesso): ?>
            <div class="alert alert-success">
                <?= $mensagem ?>
            </div>
            <a href="consultar_user.php" class="btn">Visualizar Clientes Cadastrados</a>
            <a href="form_cad_user.php" class="btn btn-secondary" style="margin-top: 10px;">Cadastrar Outro Cliente</a>
        <?php else: ?>
            <div class="alert alert-error">
                <?= $mensagem ?>
            </div>
            <a href="form_cad_user.php" class="btn btn-secondary">Voltar ao Formulário</a>
        <?php endif; ?>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>
