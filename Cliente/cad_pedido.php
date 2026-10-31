
<?php
include "conexao.php";

// Recebe e sanitiza os dados do formulário de pedidos
$id_cliente = isset($_POST["id_cliente"]) ? intval($_POST["id_cliente"]) : 0;
$id_produto = isset($_POST["id_produto"]) ? intval($_POST["id_produto"]) : 0;
$quantidade = isset($_POST["quantidade"]) ? intval($_POST["quantidade"]) : 1;
$status = isset($_POST["status"]) ? trim($_POST["status"]) : 'Pendente';
$data_pedido = !empty($_POST["data_pedido"])
    ? trim($_POST["data_pedido"])
    : date('Y-m-d H:i:s');

$mensagem = "";
$sucesso = false;

// Valida os campos obrigatórios
if ($id_cliente > 0 && $id_produto > 0 && $quantidade > 0) {

    mysqli_begin_transaction($conexao);

    try {
        // Verifica se o cliente existe
        $sql_cliente = "SELECT id FROM cliente WHERE id = ?";
        $stmt_cliente = mysqli_prepare($conexao, $sql_cliente);
        mysqli_stmt_bind_param($stmt_cliente, "i", $id_cliente);
        mysqli_stmt_execute($stmt_cliente);
        $res_cliente = mysqli_stmt_get_result($stmt_cliente);

        if (mysqli_num_rows($res_cliente) == 0) {
            throw new Exception("Cliente não encontrado no sistema.");
        }

        // Busca o produto e verifica o estoque
        $sql_prod = "SELECT preco, estoque FROM produto WHERE id = ? FOR UPDATE";
        $stmt_prod = mysqli_prepare($conexao, $sql_prod);
        mysqli_stmt_bind_param($stmt_prod, "i", $id_produto);
        mysqli_stmt_execute($stmt_prod);
        $res_prod = mysqli_stmt_get_result($stmt_prod);

        if (!$res_prod || mysqli_num_rows($res_prod) == 0) {
            throw new Exception("Produto não encontrado no sistema.");
        }

        $prod = mysqli_fetch_assoc($res_prod);
        $preco_unitario = (float) $prod['preco'];
        $estoque_atual = (int) $prod['estoque'];

        if ($estoque_atual < $quantidade) {
            throw new Exception("Estoque insuficiente para a quantidade solicitada.");
        }

        // Calcula o valor total
        $valor_total = $preco_unitario * $quantidade;

        // Cadastra o pedido
        $sql_pedido = "INSERT INTO pedido
            (data_pedido, status, valor_total, id_cliente)
            VALUES (?, ?, ?, ?)";

        $stmt_pedido = mysqli_prepare($conexao, $sql_pedido);
        mysqli_stmt_bind_param(
            $stmt_pedido,
            "ssdi",
            $data_pedido,
            $status,
            $valor_total,
            $id_cliente
        );

        if (!mysqli_stmt_execute($stmt_pedido)) {
            throw new Exception("Erro ao cadastrar pedido.");
        }

        $id_pedido = mysqli_insert_id($conexao);

        // Cadastra o item do pedido
        $sql_item = "INSERT INTO item_pedido
            (id_pedido, id_produto, quantidade, preco_unitario)
            VALUES (?, ?, ?, ?)";

        $stmt_item = mysqli_prepare($conexao, $sql_item);
        mysqli_stmt_bind_param(
            $stmt_item,
            "iiid",
            $id_pedido,
            $id_produto,
            $quantidade,
            $preco_unitario
        );

        if (!mysqli_stmt_execute($stmt_item)) {
            throw new Exception("Erro ao cadastrar o item do pedido.");
        }

        // Atualiza o estoque
        $sql_estoque = "UPDATE produto
                        SET estoque = estoque - ?
                        WHERE id = ? AND estoque >= ?";

        $stmt_estoque = mysqli_prepare($conexao, $sql_estoque);
        mysqli_stmt_bind_param(
            $stmt_estoque,
            "iii",
            $quantidade,
            $id_produto,
            $quantidade
        );

        if (!mysqli_stmt_execute($stmt_estoque) ||
            mysqli_stmt_affected_rows($stmt_estoque) != 1) {
            throw new Exception("Não foi possível atualizar o estoque.");
        }

        // Confirma todas as operações
        mysqli_commit($conexao);

        // Persiste as alterações no BD.sql
        sincronizar_bd_sql($conexao);

        $sucesso = true;
        $mensagem = "Pedido #$id_pedido cadastrado com sucesso! Valor total: R$ "
            . number_format($valor_total, 2, ',', '.');

    } catch (Throwable $e) {
        mysqli_rollback($conexao);

        $mensagem = $e instanceof mysqli_sql_exception
            ? "Ocorreu um erro ao processar o pedido. Verifique os dados e tente novamente."
            : $e->getMessage();
    }

} else {
    $mensagem = "Por favor, selecione o cliente, o produto e uma quantidade válida.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado - Cadastro de Pedido</title>

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

                <img src="BannerPizzaDaChapada.png"
                     alt="Banner Pizzaria da Chapada"
                     class="result-banner">

                <h2 class="result-title result-title-success">
                    Tudo Pronto!
                </h2>

                <div class="alert alert-success">
                    <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                </div>

                <div class="result-buttons">
                    <a href="consultar_pedido.php" class="btn-dominos btn-primary">
                        Visualizar Todos os Pedidos
                    </a>

                    <a href="form_cad_pedido.php" class="btn-dominos btn-outline">
                        Cadastrar Novo Pedido
                    </a>
                </div>

            <?php else: ?>

                <div class="result-icon">⚠️</div>

                <h2 class="result-title result-title-error">
                    Atenção
                </h2>

                <div class="alert alert-error">
                    <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                </div>

                <div class="result-buttons">
                    <a href="form_cad_pedido.php" class="btn-dominos btn-primary">
                        Voltar ao Formulário
                    </a>
                </div>

            <?php endif; ?>

        </div>
    </div>

    <?php include 'rodape.php'; ?>

</body>
</html>
