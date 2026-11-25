<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "conexao.php";

$mensagem = "";
$sucesso = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = isset($_POST["nome"]) ? trim($_POST["nome"]) : "";
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $senha = isset($_POST["senha"]) ? trim($_POST["senha"]) : "";
    $telefone = isset($_POST["telefone"]) ? trim($_POST["telefone"]) : "";
    $endereco = isset($_POST["endereco"]) ? trim($_POST["endereco"]) : "";
    $datacad = date("Y-m-d H:i:s");
    $nivel_acesso = 1;

    if ($nome !== "" && $email !== "" && $senha !== "") {
        $inserido = false;
        $erro_db = "";

        try {
            $stmt_check = mysqli_prepare($conexao, "SELECT id FROM cliente WHERE email = ? LIMIT 1");
            if ($stmt_check) {
                mysqli_stmt_bind_param($stmt_check, "s", $email);
                mysqli_stmt_execute($stmt_check);
                $res_check = mysqli_stmt_get_result($stmt_check);
                if ($res_check && mysqli_num_rows($res_check) > 0) {
                    $mensagem = "Já existe um cadastro com este e-mail. Faça login para continuar.";
                }
            }
        } catch (Throwable $e) {
            // Segue para tentar inserir
        }

        if ($mensagem === "") {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            try {
                $stmt = mysqli_prepare(
                    $conexao,
                    "INSERT INTO cliente (nome, email, senha, telefone, endereco, datacad, nivel_acesso) VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "ssssssi", $nome, $email, $senha_hash, $telefone, $endereco, $datacad, $nivel_acesso);
                    if (mysqli_stmt_execute($stmt)) {
                        $inserido = true;
                        $novo_id = (int) mysqli_insert_id($conexao);
                    } else {
                        $erro_db = mysqli_stmt_error($stmt) ?: mysqli_error($conexao);
                    }
                } else {
                    $erro_db = mysqli_error($conexao);
                }
            } catch (Throwable $e) {
                $erro_db = $e->getMessage();
            }

            if ($inserido) {
                if (function_exists("sincronizar_bd_sql")) {
                    sincronizar_bd_sql($conexao);
                }
                $_SESSION["usuario_id"] = $novo_id ?? 0;
                $_SESSION["usuario_nome"] = $nome;
                $_SESSION["usuario_email"] = $email;
                $_SESSION["nivel_acesso"] = "Cliente";
                $_SESSION["tipo_usuario"] = "cliente";
                $sucesso = true;
                $mensagem = "Cadastro realizado com sucesso! Agora você já pode fazer seu pedido.";
            } else {
                $mensagem = "Não foi possível concluir seu cadastro." . ($erro_db !== "" ? " Detalhe do banco: " . $erro_db : " Verifique os campos e tente novamente.");
            }
        }
    } else {
        $mensagem = "Preencha nome completo, e-mail e senha para criar seu cadastro.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Cliente - Pizzaria da Chapada</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/base.css?v=27.0">
    <link rel="stylesheet" href="style/rodape.css?v=27.0">
    <link rel="stylesheet" href="style/vitrine.css?v=27.0">
</head>
<body class="cliente-body bg-pizzaria">

    <header class="cliente-topbar">
        <a href="index1.php" class="cliente-brand">
            <img src="logoCircularPizzariaChapada.png" alt="Pizzaria da Chapada">
        </a>

        <nav class="cliente-menu">
            <a href="index1.php" class="cliente-link">Início</a>
            <a href="index1.php#top-pizzas" class="cliente-link">Top Pizzas</a>
            <a href="fazer_pedido.php" class="cliente-cta">Faça seu Pedido</a>
            <a href="cadastro_cliente.php" class="cliente-btn-sec">Cadastro</a>
            <a href="login.php" class="cliente-btn-login">Login</a>
        </nav>
    </header>

    <main class="cliente-main">
        <section class="pedido-cliente-section">
            <div class="pedido-cliente-card">
                <div class="pedido-cliente-topo">
                    <span class="pedido-selo">Área do Cliente</span>
                    <h1>Criar Conta de Cliente</h1>
                    <p>Cadastre-se para pedir as pizzas da Pizzaria da Chapada com praticidade.</p>
                </div>

                <?php if ($mensagem !== ""): ?>
                    <div class="<?= $sucesso ? 'pedido-alerta-sucesso' : 'pedido-alerta-erro' ?>">
                        <?= htmlspecialchars($mensagem, ENT_QUOTES, "UTF-8") ?>
                        <?php if ($sucesso): ?>
                            <div class="pedido-acoes" style="margin-top: 14px;">
                                <a href="fazer_pedido.php" class="btn-pedido-confirmar" style="text-align:center; text-decoration:none;">Ir para Faça seu Pedido</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <form action="cadastro_cliente.php" method="POST" class="pedido-form-grid">
                    <div class="pedido-campo">
                        <label for="nome">Nome completo *</label>
                        <input type="text" name="nome" id="nome" value="<?= isset($_POST["nome"]) ? htmlspecialchars($_POST["nome"], ENT_QUOTES, "UTF-8") : "" ?>" placeholder="Digite seu nome completo" required>
                    </div>

                    <div class="pedido-campo">
                        <label for="email">E-mail *</label>
                        <input type="email" name="email" id="email" value="<?= isset($_POST["email"]) ? htmlspecialchars($_POST["email"], ENT_QUOTES, "UTF-8") : "" ?>" placeholder="cliente@exemplo.com" required>
                    </div>

                    <div class="pedido-campo">
                        <label for="senha">Senha *</label>
                        <input type="password" name="senha" id="senha" placeholder="Crie uma senha" required>
                    </div>

                    <div class="pedido-campo">
                        <label for="telefone">Telefone</label>
                        <input type="text" name="telefone" id="telefone" value="<?= isset($_POST["telefone"]) ? htmlspecialchars($_POST["telefone"], ENT_QUOTES, "UTF-8") : "" ?>" placeholder="(75) 99999-9999">
                    </div>

                    <div class="pedido-campo">
                        <label for="endereco">Endereço de entrega</label>
                        <input type="text" name="endereco" id="endereco" value="<?= isset($_POST["endereco"]) ? htmlspecialchars($_POST["endereco"], ENT_QUOTES, "UTF-8") : "" ?>" placeholder="Rua, número, bairro">
                    </div>

                    <div class="pedido-acoes">
                        <button type="submit" class="btn-pedido-confirmar">Finalizar Cadastro</button>
                        <a href="index1.php" class="btn-pedido-voltar">Voltar</a>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <?php include 'rodape.php'; ?>
</body>
</html>