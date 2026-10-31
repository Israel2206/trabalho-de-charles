<?php
include "conexao.php";

// Captura e converte formatos de preço e estoque
$nome = isset($_POST["nome"]) ? trim($_POST["nome"]) : '';
$preco = isset($_POST["preco"]) ? floatval(str_replace(',', '.', $_POST["preco"])) : 0;
$estoque = isset($_POST["estoque"]) ? intval($_POST["estoque"]) : 0;
$id_categoria = isset($_POST["id_categoria"]) ? intval($_POST["id_categoria"]) : 0;

$mensagem = "";
$sucesso = false;

// Valida campos obrigatórios antes da inserção
if (!empty($nome) && $preco > 0 && $id_categoria > 0) {
    try {
        $stmt = mysqli_prepare($conexao, "INSERT INTO produto (nome, preco, estoque, id_categoria) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sdii", $nome, $preco, $estoque, $id_categoria);

        if (mysqli_stmt_execute($stmt)) {
            $sucesso = true;
            $mensagem = "Produto salvo com sucesso no banco de dados!";
            sincronizar_bd_sql($conexao);
        } else {
            $mensagem = "Erro ao cadastrar produto: " . mysqli_error($conexao);
        }
    } catch (Throwable $e) {
        $mensagem = "Erro ao salvar no banco: " . $e->getMessage();
    }
} else {
    $mensagem = "Preencha todos os campos obrigatórios corretamente!";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado - Cadastro de Produto</title>
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
                    <a href="consultar_produto.php" class="btn-dominos btn-primary">Visualizar Produtos</a>
                    <a href="form_cad_produto.php" class="btn-dominos btn-outline">Cadastrar Outro Produto</a>
                </div>
            <?php else: ?>
                <div class="result-icon">⚠️</div>
                <h2 class="result-title result-title-error">Atenção</h2>
                
                <div class="alert alert-error">
                    <?= $mensagem ?>
                </div>

                <div class="result-buttons">
                    <a href="form_cad_produto.php" class="btn-dominos btn-primary">Voltar ao Formulário</a>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <?php include 'rodape.php'; ?>
    
</body>
</html>

