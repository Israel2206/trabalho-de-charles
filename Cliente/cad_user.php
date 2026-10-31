<?php
include "conexao.php";

// Captura e sanitiza as informações de cadastro do cliente
$nome = isset($_POST["nome"]) ? trim($_POST["nome"]) : '';
$email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
$senha = isset($_POST["senha"]) ? $_POST["senha"] : '';
$telefone = isset($_POST["telefone"]) ? trim($_POST["telefone"]) : '';
$endereco = isset($_POST["endereco"]) ? trim($_POST["endereco"]) : '';
$datacad = !empty($_POST["datacad"]) ? trim($_POST["datacad"]) : date('Y-m-d H:i:s');
$nivel = isset($_POST["nivel"]) ? intval($_POST["nivel"]) : 1;

$mensagem = "";
$sucesso = false;

// Valida os campos obrigatórios
if (!empty($nome) && !empty($email) && !empty($senha)) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Por favor, informe um e-mail válido!";
    } else {
        try {
            // Verifica se o e-mail já existe para não falhar
            $stmt_check = mysqli_prepare($conexao, "SELECT id FROM cliente WHERE email = ?");
            mysqli_stmt_bind_param($stmt_check, "s", $email);
            mysqli_stmt_execute($stmt_check);
            $res_check = mysqli_stmt_get_result($stmt_check);

            if ($res_check && mysqli_num_rows($res_check) > 0) {
                $mensagem = "Este e-mail já está cadastrado para outro cliente!";
            } else {
                $senha_segura = password_hash($senha, PASSWORD_DEFAULT);

                $sql = "INSERT INTO cliente (nome, email, senha, telefone, endereco, datacad, nivel_acesso)
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conexao, $sql);
                mysqli_stmt_bind_param($stmt, "ssssssi", $nome, $email, $senha_segura, $telefone, $endereco, $datacad, $nivel);

                if (mysqli_stmt_execute($stmt)) {
                    $sucesso = true;
                    $mensagem = "Cliente cadastrado com sucesso no banco de dados!";
                    sincronizar_bd_sql($conexao);
                } else {
                    $mensagem = "Erro ao cadastrar cliente: " . mysqli_error($conexao);
                }
            }
        } catch (Throwable $e) {
            $mensagem = "Erro ao salvar no banco: " . $e->getMessage();
        }
    }
} else {
    $mensagem = "Por favor, preencha todos os campos obrigatórios (Nome, E-mail e Senha)!";
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

                <img src="BannerPizzaDaChapada.png"
                     alt="Banner Pizzaria da Chapada"
                     class="result-banner">

                <h2 style="color: #78350f; font-size: 26px; margin-bottom: 15px; font-weight: 700;">
                    Tudo Pronto!
                </h2>

                <div style="background-color: #d1fae5; color: #065f46; border: 1.5px solid #a7f3d0; padding: 14px; border-radius: 12px; font-weight: 600; margin-bottom: 25px; font-size: 15px;">
                    <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                </div>

                <div class="result-buttons">
                    <a href="consultar_user.php" class="btn-dominos btn-primary">
                        Visualizar Clientes Cadastrados
                    </a>

                    <a href="form_cad_user.php" class="btn-dominos btn-outline">
                        Cadastrar Outro Cliente
                    </a>
                </div>

            <?php else: ?>

                <div style="font-size: 50px; margin-bottom: 15px;">⚠️</div>

                <h2 style="color: #991b1b; font-size: 26px; margin-bottom: 15px; font-weight: 700;">
                    Atenção
                </h2>

                <div style="background-color: #fee2e2; color: #991b1b; border: 1.5px solid #fecaca; padding: 14px; border-radius: 12px; font-weight: 600; margin-bottom: 25px; font-size: 15px;">
                    <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                </div>

                <div class="result-buttons">
                    <a href="form_cad_user.php" class="btn-dominos btn-primary">
                        Voltar ao Formulário
                    </a>
                </div>

            <?php endif; ?>

        </div>
    </div>

    <?php include 'rodape.php'; ?>

</body>
</html>
