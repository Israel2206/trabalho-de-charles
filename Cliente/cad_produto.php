<?php
include "verifica_admin.php";
include "conexao.php";

$mensagem = "";
$sucesso = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = isset($_POST["nome"]) ? trim($_POST["nome"]) : '';
    $id_categoria = isset($_POST["id_categoria"]) ? intval($_POST["id_categoria"]) : 0;
    $preco = isset($_POST["preco"]) ? floatval(str_replace(',', '.', $_POST["preco"])) : 0;
    $estoque = isset($_POST["estoque"]) ? intval($_POST["estoque"]) : 1;

    // Arrays vindos da ficha técnica dinâmica
    $ingredientes = isset($_POST["ingredientes"]) && is_array($_POST["ingredientes"]) ? $_POST["ingredientes"] : [];
    $quantidades = isset($_POST["quantidades"]) && is_array($_POST["quantidades"]) ? $_POST["quantidades"] : [];

    // Filtra apenas ingredientes válidos (id > 0 e quantidade > 0)
    $itens_ficha = [];
    for ($i = 0; $i < count($ingredientes); $i++) {
        $id_ing = intval($ingredientes[$i]);
        $qtd_ing = isset($quantidades[$i]) ? floatval(str_replace(',', '.', $quantidades[$i])) : 0;
        if ($id_ing > 0 && $qtd_ing > 0) {
            $itens_ficha[] = ['id' => $id_ing, 'qtd' => $qtd_ing];
        }
    }

    // Verifica se a categoria selecionada é de pizza (contém "pizza")
    $eh_pizza = false;
    if ($id_categoria > 0) {
        $stmt_cat = mysqli_prepare($conexao, "SELECT nome FROM categoria WHERE id = ? LIMIT 1");
        if ($stmt_cat) {
            mysqli_stmt_bind_param($stmt_cat, "i", $id_categoria);
            mysqli_stmt_execute($stmt_cat);
            $res_cat = mysqli_stmt_get_result($stmt_cat);
            if ($res_cat && $row_cat = mysqli_fetch_assoc($res_cat)) {
                $nome_cat = mb_strtolower(trim($row_cat["nome"]), 'UTF-8');
                $eh_pizza = (mb_strpos($nome_cat, 'pizza', 0, 'UTF-8') !== false);
            }
        }
    }

    if (empty($nome) || $id_categoria <= 0 || $preco <= 0 || $estoque < 0) {
        $mensagem = "Preencha nome, categoria, preço válido e estoque!";
    } elseif ($eh_pizza && count($itens_ficha) === 0) {
        $mensagem = "Para categorias de pizza, adicione pelo menos um ingrediente na ficha técnica!";
    } else {
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

            // 2. Processa cada ingrediente da ficha técnica (quando houver)
            foreach ($itens_ficha as $item) {
                $id_ingrediente = $item['id'];
                $qtd_unitaria = $item['qtd'];

                $sql_ligacao = "INSERT INTO produto_ingrediente (id_produto, id_ingrediente, quantidade_necessaria) VALUES (?, ?, ?)";
                $stmt_ligacao = mysqli_prepare($conexao, $sql_ligacao);
                mysqli_stmt_bind_param($stmt_ligacao, "iid", $id_produto, $id_ingrediente, $qtd_unitaria);
                mysqli_stmt_execute($stmt_ligacao);

                $qtd_total_consumida = $qtd_unitaria * $estoque;

                $sql_baixa = "UPDATE ingrediente SET quantidade_atual = GREATEST(0, quantidade_atual - ?) WHERE id = ?";
                $stmt_baixa = mysqli_prepare($conexao, $sql_baixa);
                mysqli_stmt_bind_param($stmt_baixa, "di", $qtd_total_consumida, $id_ingrediente);
                mysqli_stmt_execute($stmt_baixa);
            }

            mysqli_commit($conexao);

            if (function_exists("sincronizar_bd_sql")) {
                sincronizar_bd_sql($conexao);
            }

            $sucesso = true;
            $mensagem = count($itens_ficha) > 0
                ? "Produto cadastrado, ficha técnica salva e estoque dos ingredientes descontado proporcionalmente com sucesso!"
                : "Produto cadastrado com sucesso no banco de dados!";

        } catch (Throwable $e) {
            mysqli_rollback($conexao);
            $mensagem = "Erro ao processar o cadastro: " . $e->getMessage();
        }
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