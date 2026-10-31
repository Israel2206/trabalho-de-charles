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
    <link rel="stylesheet" href="style.css?v=9.1">
    <style>
        .result-banner {
            width: 180px;
            height: auto;
            margin-bottom: 15px;
            filter: drop-shadow(0 6px 12px rgba(120, 53, 15, 0.2));
        }
        .result-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
            box-sizing: border-box;
        }
        .result-buttons a {
            display: block;
            width: 100%;
            text-align: center;
            box-sizing: border-box;
            text-decoration: none;
            border-radius: 25px;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-pizzaria">
    <?php include 'Menu1.php'; ?>

    <div class="main-wrapper" style="display: flex; justify-content: center; align-items: center; min-height: 60vh;">
        <div class="table-card-container" style="max-width: 500px; width: 100%; text-align: center; padding: 40px 30px; box-sizing: border-box;">
            <?php if ($sucesso): ?>
                <img src="BannerPizzaDaChapada.png" alt="Banner Pizzaria da Chapada" class="result-banner">
                <h2 style="color: #78350f; font-size: 26px; margin-bottom: 15px; font-weight: 700;">Tudo Pronto!</h2>
                <div style="background-color: #d1fae5; color: #065f46; border: 1.5px solid #a7f3d0; padding: 14px; border-radius: 12px; font-weight: 600; margin-bottom: 25px; font-size: 15px;">
                    <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div class="result-buttons">
                    <a href="consultar_user.php" class="btn-dominos btn-primary">Visualizar Clientes Cadastrados</a>
                    <a href="form_cad_cliente.php" class="btn-dominos btn-outline">Cadastrar Outro Cliente</a>
                </div>
            <?php else: ?>
                <div style="font-size: 50px; margin-bottom: 15px;">⚠️</div>
                <h2 style="color: #991b1b; font-size: 26px; margin-bottom: 15px; font-weight: 700;">Atenção</h2>
                <div style="background-color: #fee2e2; color: #991b1b; border: 1.5px solid #fecaca; padding: 14px; border-radius: 12px; font-weight: 600; margin-bottom: 25px; font-size: 15px;">
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