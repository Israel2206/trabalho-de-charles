<?php
include "conexao.php";

$nome = isset($_POST["nome"]) ? mysqli_real_escape_string($conexao, trim($_POST["nome"])) : '';
$descricao = isset($_POST["descricao"]) ? mysqli_real_escape_string($conexao, trim($_POST["descricao"])) : '';

$mensagem = "";
$sucesso = false;

if (!empty($nome)) {
    $sql = "INSERT INTO categoria (nome, descricao) VALUES ('$nome', '$descricao')";

    if (mysqli_query($conexao, $sql)) {
        $sucesso = true;
        $mensagem = "Categoria cadastrada com sucesso!";
    } else {
        $mensagem = "Erro ao cadastrar categoria: " . mysqli_error($conexao);
    }
} else {
    $mensagem = "O nome da categoria é obrigatório!";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultado - Cadastro de Categoria</title>
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
            <a href="consultar_categoria.php" class="btn">Visualizar Categorias</a>
            <a href="form_cad_categoria.php" class="btn btn-secondary" style="margin-top: 10px;">Cadastrar Outra Categoria</a>
        <?php else: ?>
            <div class="alert alert-error">
                <?= $mensagem ?>
            </div>
            <a href="form_cad_categoria.php" class="btn btn-secondary">Voltar ao Formulário</a>
        <?php endif; ?>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>
