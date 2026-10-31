<?php
include "conexao.php";

// Captura os dados da nova categoria
$nome = isset($_POST["nome"]) ? trim($_POST["nome"]) : '';
$descricao = isset($_POST["descricao"]) ? trim($_POST["descricao"]) : '';

$mensagem = "";
$sucesso = false;

// Valida o preenchimento do nome obrigatório antes do INSERT
if (!empty($nome)) {
    try {
        $stmt = mysqli_prepare($conexao, "INSERT INTO categoria (nome, descricao) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $nome, $descricao);

        if (mysqli_stmt_execute($stmt)) {
            $sucesso = true;
            $mensagem = "Categoria salva com sucesso no banco de dados!";
            sincronizar_bd_sql($conexao);
        } else {
            $mensagem = "Erro ao cadastrar categoria: " . mysqli_error($conexao);
        }
    } catch (Throwable $e) {
        $mensagem = "Erro ao salvar no banco: " . $e->getMessage();
    }
} else {
    $mensagem = "O nome da categoria é obrigatório!";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado - Cadastro de Categoria</title>
    <link rel="stylesheet" href="style/base.css?v=10.0">
    <link rel="stylesheet" href="style/menu.css?v=10.0">
    <link rel="stylesheet" href="style/rodape.css?v=10.0">
    <link rel="stylesheet" href="style/componentes.css?v=10.0">
    <link rel="stylesheet" href="style/resultado.css?v=10.0">
</head>
<body class="bg-pizzaria">
    <?php include 'Menu1.php'; ?>

    <div class="main-wrapper result-wrapper">
        <div class="table-card-container result-card">
            
            <?php if ($sucesso): ?>
                <img src="BannerPizzaDaChapada.png" alt="Banner Pizzaria da Chapada" class="result-banner">
                <h2 class="result-title result-title-success">Tudo Pronto!</h2>
                
                <div class="alert alert-success">
                    <?= $mensagem ?>
                </div>

                <div class="result-buttons">
                    <a href="consultar_categoria.php" class="btn-dominos btn-primary">Visualizar Categorias</a>
                    <a href="form_cad_categoria.php" class="btn-dominos btn-outline">Cadastrar Outra Categoria</a>
                </div>
            <?php else: ?>
                <div class="result-icon">⚠️</div>
                <h2 class="result-title result-title-error">Atenção</h2>
                
                <div class="alert alert-error">
                    <?= $mensagem ?>
                </div>

                <div class="result-buttons">
                    <a href="form_cad_categoria.php" class="btn-dominos btn-primary">Voltar ao Formulário</a>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>