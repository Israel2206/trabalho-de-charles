<?php
// Inclui a conexão com o banco de dados
include "conexao.php";

$mensagem = "";
$sucesso = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = isset($_POST["nome"]) ? trim($_POST["nome"]) : '';
    $unidade_medida = isset($_POST["unidade_medida"]) ? trim($_POST["unidade_medida"]) : 'gramas';
    $quantidade_atual = isset($_POST["quantidade_atual"]) ? floatval($_POST["quantidade_atual"]) : 0;

    if (!empty($nome)) {
        try {
            // Verifica se já existe um ingrediente com o mesmo nome para evitar duplicatas
            $sql_check = "SELECT id FROM ingrediente WHERE LOWER(nome) = LOWER(?)";
            $stmt_check = mysqli_prepare($conexao, $sql_check);
            mysqli_stmt_bind_param($stmt_check, "s", $nome);
            mysqli_stmt_execute($stmt_check);
            $res_check = mysqli_stmt_get_result($stmt_check);

            if ($res_check && mysqli_num_rows($res_check) > 0) {
                throw new Exception("O ingrediente '$nome' já está cadastrado no sistema! Use a opção de Editar na tabela para alterar a quantidade.");
            }

            // Insere o novo ingrediente
            $sql = "INSERT INTO ingrediente (nome, unidade_medida, quantidade_atual) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conexao, $sql);
            mysqli_stmt_bind_param($stmt, "ssd", $nome, $unidade_medida, $quantidade_atual);

            if (mysqli_stmt_execute($stmt)) {
                $sucesso = true;
                $mensagem = "Ingrediente salvo com sucesso no banco de dados!";
            } else {
                $mensagem = "Erro ao cadastrar ingrediente: " . mysqli_error($conexao);
            }
        } catch (Throwable $e) {
            $mensagem = $e->getMessage();
        }
    } else {
        $mensagem = "O nome do ingrediente é obrigatório!";
    }
} else {
    header("Location: form_cad_ingrediente.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado - Cadastro de Ingrediente</title>
    
    <link rel="stylesheet" href="style/base.css?v=10.0">
    <link rel="stylesheet" href="style/menu.css?v=10.0">
    <link rel="stylesheet" href="style/rodape.css?v=10.0">
    <link rel="stylesheet" href="style/componentes.css?v=10.0">
    <link rel="stylesheet" href="style/resultado.css?v=10.0">
    <link rel="stylesheet" href="style/ingrediente.css?v=10.0">
</head>
<body class="bg-pizzaria">

    <?php include 'Menu1.php'; ?>

    <div class="main-wrapper result-wrapper">
        <div class="table-card-container result-card">
            <?php if ($sucesso): ?>
                <img src="BannerPizzaDaChapada.png" alt="Banner Pizzaria da Chapada" class="result-banner">
                <h2 class="result-title result-title-success">Tudo Pronto!</h2>
                <div class="alert alert-success">
                    <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div class="result-buttons">
                    <a href="consultar_ingrediente.php" class="btn-dominos btn-primary">Visualizar Ingredientes Cadastrados</a>
                    <a href="form_cad_ingrediente.php" class="btn-dominos btn-outline">Cadastrar Outro Ingrediente</a>
                </div>
            <?php else: ?>
                <div class="result-icon">⚠️</div>
                <h2 class="result-title result-title-error">Atenção</h2>
                <div class="alert alert-error">
                    <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div class="result-buttons">
                    <a href="consultar_ingrediente.php" class="btn-dominos btn-primary">Ir para Tabela de Ingredientes</a>
                    <a href="form_cad_ingrediente.php" class="btn-dominos btn-outline">Voltar ao Formulário</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'rodape.php'; ?>

</body>
</html>