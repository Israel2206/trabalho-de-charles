<?php
include "conexao.php";

$nome = isset($_POST["nome"]) ? mysqli_real_escape_string($conexao, trim($_POST["nome"])) : '';
$preco = isset($_POST["preco"]) ? floatval(str_replace(',', '.', $_POST["preco"])) : 0;
$estoque = isset($_POST["estoque"]) ? intval($_POST["estoque"]) : 0;
$id_categoria = isset($_POST["id_categoria"]) ? intval($_POST["id_categoria"]) : 0;

$mensagem = "";
$sucesso = false;

if (!empty($nome) && $preco > 0 && $id_categoria > 0) {
    $sql = "INSERT INTO produto (nome, preco, estoque, id_categoria) 
            VALUES ('$nome', '$preco', '$estoque', '$id_categoria')";

    if (mysqli_query($conexao, $sql)) {
        $sucesso = true;
        $mensagem = "Produto cadastrado com sucesso!";
    } else {
        $mensagem = "Erro ao cadastrar produto: " . mysqli_error($conexao);
    }
} else {
    $mensagem = "Preencha todos os campos obrigatórios corretamente!";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultado - Cadastro de Produto</title>
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
            <a href="consultar_produto.php" class="btn">Visualizar Produtos</a>
            <a href="form_cad_produto.php" class="btn btn-secondary" style="margin-top: 10px;">Cadastrar Outro Produto</a>
        <?php else: ?>
            <div class="alert alert-error">
                <?= $mensagem ?>
            </div>
            <a href="form_cad_produto.php" class="btn btn-secondary">Voltar ao Formulário</a>
        <?php endif; ?>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>
