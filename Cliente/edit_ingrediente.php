<?php
// Inclui a conexão com o banco de dados
include "conexao.php";

$mensagem = "";
$sucesso = false;
$id = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
    $nome = isset($_POST["nome"]) ? trim($_POST["nome"]) : '';
    $unidade_medida = isset($_POST["unidade_medida"]) ? trim($_POST["unidade_medida"]) : 'gramas';
    $quantidade_atual = isset($_POST["quantidade_atual"]) ? floatval($_POST["quantidade_atual"]) : 0;

    if ($id > 0 && !empty($nome)) {
        try {
            // Valida se já existe outro ingrediente com o mesmo nome (evitando duplicatas)
            $sql_check = "SELECT id FROM ingrediente WHERE LOWER(nome) = LOWER(?) AND id != ?";
            $stmt_check = mysqli_prepare($conexao, $sql_check);
            mysqli_stmt_bind_param($stmt_check, "si", $nome, $id);
            mysqli_stmt_execute($stmt_check);
            $res_check = mysqli_stmt_get_result($stmt_check);

            if ($res_check && mysqli_num_rows($res_check) > 0) {
                throw new Exception("Já existe outro ingrediente cadastrado com esse nome!");
            }

            // Executa a atualização (UPDATE) no banco de dados
            $sql = "UPDATE ingrediente SET nome = ?, unidade_medida = ?, quantidade_atual = ? WHERE id = ?";
            $stmt = mysqli_prepare($conexao, $sql);
            mysqli_stmt_bind_param($stmt, "ssdi", $nome, $unidade_medida, $quantidade_atual, $id);

            if (mysqli_stmt_execute($stmt)) {
                $sucesso = true;
                $mensagem = "Ingrediente atualizado com sucesso no banco de dados!";
            } else {
                $mensagem = "Erro ao atualizar ingrediente: " . mysqli_error($conexao);
            }
        } catch (Throwable $e) {
            $mensagem = "Atenção: " . $e->getMessage();
        }
    } else {
        $mensagem = "Todos os campos obrigatórios precisam ser preenchidos!";
    }
} else {
    header("Location: consultar_ingrediente.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado - Edição de Ingrediente</title>
    
    <!-- Folhas de estilo externas unificadas na pasta style/ -->
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
                <h2 class="result-title result-title-success">Alteração Concluída!</h2>
                <div class="alert alert-success">
                    <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div class="result-buttons">
                    <a href="consultar_ingrediente.php" class="btn-dominos btn-primary">Voltar à Tabela de Ingredientes</a>
                </div>
            <?php else: ?>
                <div class="result-icon">⚠️</div>
                <h2 class="result-title result-title-error">Atenção</h2>
                <div class="alert alert-error">
                    <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div class="result-buttons">
                    <?php if ($id > 0): ?>
                        <a href="form_edit_ingrediente.php?id=<?= $id ?>" class="btn-dominos btn-primary">Tentar Novamente</a>
                    <?php else: ?>
                        <a href="consultar_ingrediente.php" class="btn-dominos btn-primary">Ir para Tabela de Ingredientes</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'rodape.php'; ?>

</body>
</html>