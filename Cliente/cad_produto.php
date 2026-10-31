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
                    <?= $mensagem ?>
                </div>

                <div class="result-buttons">
                    <a href="consultar_produto.php" class="btn-dominos btn-primary">Visualizar Produtos</a>
                    <a href="form_cad_produto.php" class="btn-dominos btn-outline">Cadastrar Outro Produto</a>
                </div>
            <?php else: ?>
                <div style="font-size: 50px; margin-bottom: 15px;">⚠️</div>
                <h2 style="color: #991b1b; font-size: 26px; margin-bottom: 15px; font-weight: 700;">Atenção</h2>
                
                <div style="background-color: #fee2e2; color: #991b1b; border: 1.5px solid #fecaca; padding: 14px; border-radius: 12px; font-weight: 600; margin-bottom: 25px; font-size: 15px;">
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

