<?php
include "conexao.php";

$mensagem = "";
$sucesso = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = isset($_POST["nome"]) ? trim($_POST["nome"]) : '';
    $id_categoria = isset($_POST["id_categoria"]) ? intval($_POST["id_categoria"]) : 0;
    $preco = isset($_POST["preco"]) ? floatval($_POST["preco"]) : 0;
    $estoque = isset($_POST["estoque"]) ? intval($_POST["estoque"]) : 1; // Quantidade de unidades produzidas para o estoque
    
    // Arrays vindos da ficha técnica dinâmica
    $ingredientes = isset($_POST["ingredientes"]) ? $_POST["ingredientes"] : [];
    $quantidades = isset($_POST["quantidades"]) ? $_POST["quantidades"] : [];

    if (!empty($nome) && $id_categoria > 0 && count($ingredientes) > 0) {
        // Inicia transação para garantir total integridade e segurança no banco de dados
        mysqli_begin_transaction($conexao);

        try {
            // 1. Cadastra o produto principal
            $sql_prod = "INSERT INTO produto (nome, id_categoria, preco, estoque) VALUES (?, ?, ?, ?)";
            $stmt_prod = mysqli_prepare($conexao, $sql_prod);
            mysqli_stmt_bind_param($stmt_prod, "sidi", $nome, $id_categoria, $preco, $estoque);
            
            if (!mysqli_stmt_execute($stmt_prod)) {
                throw new Exception("Erro ao cadastrar o produto: " . mysqli_error($conexao));
            }

            // Recupera o ID do produto recém-cadastrado
            $id_produto = mysqli_insert_id($conexao);

            // 2. Processa cada ingrediente da ficha técnica
            for ($i = 0; $i < count($ingredientes); $i++) {
                $id_ingrediente = intval($ingredientes[$i]);
                $qtd_unitaria = floatval($quantidades[$i]); // Quantidade gasta para fazer APENAS 1 unidade do produto

                if ($id_ingrediente > 0 && $qtd_unitaria > 0) {
                    // Grava na tabela de ligação da ficha técnica (armazena a quantidade unitária padrão)
                    $sql_ligacao = "INSERT INTO produto_ingrediente (id_produto, id_ingrediente, quantidade_necessaria) VALUES (?, ?, ?)";
                    $stmt_ligacao = mysqli_prepare($conexao, $sql_ligacao);
                    mysqli_stmt_bind_param($stmt_ligacao, "iid", $id_produto, $id_ingrediente, $qtd_unitaria);
                    mysqli_stmt_execute($stmt_ligacao);

                    // CÁLCULO MATEMÁTICO PROPORCIONAL DO LOTE:
                    // Total consumido = Quantidade Unitária x Quantidade de produtos colocados no estoque
                    $qtd_total_consumida = $qtd_unitaria * $estoque;

                    // Desconta o total proporcional do estoque atual do ingrediente no banco
                    $sql_baixa = "UPDATE ingrediente SET quantidade_atual = GREATEST(0, quantidade_atual - ?) WHERE id = ?";
                    $stmt_baixa = mysqli_prepare($conexao, $sql_baixa);
                    mysqli_stmt_bind_param($stmt_baixa, "di", $qtd_total_consumida, $id_ingrediente);
                    mysqli_stmt_execute($stmt_baixa);
                }
            }

            // Confirma todas as operações na base de dados com sucesso
            mysqli_commit($conexao);
            $sucesso = true;
            $mensagem = "Produto cadastrado, ficha técnica salva e estoque dos ingredientes descontado proporcionalmente com sucesso!";

        } catch (Throwable $e) {
            // Em caso de qualquer falha, desfaz todas as alterações garantindo consistência
            mysqli_rollback($conexao);
            $mensagem = "Erro ao processar o cadastro: " . $e->getMessage();
        }
    } else {
        $mensagem = "Preencha todos os campos obrigatórios e adicione pelo menos um ingrediente na ficha técnica!";
    }
} else {
    header("Location: form_cad_produto.php");
    exit;
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
                    <a href="consultar_produto.php" class="btn-dominos btn-primary">Visualizar Produtos Cadastrados</a>
                    <a href="form_cad_produto.php" class="btn-dominos btn-outline">Cadastrar Outro Produto</a>
                </div>
            <?php else: ?>
                <div class="result-icon">⚠️</div>
                <h2 class="result-title result-title-error">Atenção</h2>
                <div class="alert alert-error">
                    <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
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