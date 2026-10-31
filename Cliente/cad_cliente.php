<?php
include "conexao.php";

$nome = isset($_POST["nome"]) ? trim($_POST["nome"]) : '';
$telefone = isset($_POST["telefone"]) ? trim($_POST["telefone"]) : '';
$endereco = isset($_POST["endereco"]) ? trim($_POST["endereco"]) : '';
$email = isset($_POST["email"]) && !empty($_POST["email"]) ? trim($_POST["email"]) : null;
$senha = isset($_POST["senha"]) && !empty($_POST["senha"]) ? password_hash($_POST["senha"], PASSWORD_DEFAULT) : null;
$datacad = date('Y-m-d H:i:s');
$nivel = 1;

$mensagem = "";
$sucesso = false;

if (!empty($nome)) {
    try {
        if ($email !== null) {
            $stmt_check = mysqli_prepare($conexao, "SELECT id FROM cliente WHERE email = ?");
            mysqli_stmt_bind_param($stmt_check, "s", $email);
            mysqli_stmt_execute($stmt_check);
            $res_check = mysqli_stmt_get_result($stmt_check);
            if ($res_check && mysqli_num_rows($res_check) > 0) {
                throw new Exception("Este e-mail já está cadastrado.");
            }
        }

        $sql = "INSERT INTO cliente (nome, email, senha, telefone, endereco, datacad, nivel_acesso) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssi", $nome, $email, $senha, $telefone, $endereco, $datacad, $nivel);

        if (mysqli_stmt_execute($stmt)) {
            $sucesso = true;
            $mensagem = "Cliente salvo com sucesso no banco de dados!";
            sincronizar_bd_sql($conexao);
        } else {
            $mensagem = "Erro ao cadastrar: " . mysqli_error($conexao);
        }
    } catch (Throwable $e) {
        $mensagem = "Erro ao salvar no banco de dados: " . $e->getMessage();
    }
} else {
    $mensagem = "O nome do cliente é obrigatório!";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado - Cadastro de Cliente</title>
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
                    <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div class="result-buttons">
                    <a href="consultar_user.php" class="btn-dominos btn-primary">Visualizar Clientes Cadastrados</a>
                    <a href="form_cad_cliente.php" class="btn-dominos btn-outline">Cadastrar Outro Cliente</a>
                </div>
            <?php else: ?>
                <div class="result-icon">⚠️</div>
                <h2 class="result-title result-title-error">Atenção</h2>
                <div class="alert alert-error">
                    <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div class="result-buttons">
                    <a href="form_cad_cliente.php" class="btn-dominos btn-primary">Voltar ao Formulário</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>