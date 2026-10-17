<?php
include "conexao.php";

$id_cliente = isset($_POST["id_cliente"]) ? intval($_POST["id_cliente"]) : 0;
$id_produto = isset($_POST["id_produto"]) ? intval($_POST["id_produto"]) : 0;
$quantidade = isset($_POST["quantidade"]) ? intval($_POST["quantidade"]) : 1;
$status = isset($_POST["status"]) ? mysqli_real_escape_string($conexao, trim($_POST["status"])) : 'Pendente';
$data_pedido = !empty($_POST["data_pedido"]) ? $_POST["data_pedido"] : date('Y-m-d H:i:s');

$mensagem = "";
$sucesso = false;

if ($id_cliente > 0 && $id_produto > 0 && $quantidade > 0) {
    // Busca o preço unitário do produto cadastrado
    $busca_prod = mysqli_query($conexao, "SELECT preco FROM produto WHERE id = $id_produto");
    if ($busca_prod && mysqli_num_rows($busca_prod) > 0) {
        $prod = mysqli_fetch_assoc($busca_prod);
        $preco_unitario = floatval($prod['preco']);
        $valor_total = $preco_unitario * $quantidade;

        // Inserir na tabela pedido
        $sql_pedido = "INSERT INTO pedido (data_pedido, status, valor_total, id_cliente) 
                       VALUES ('$data_pedido', '$status', '$valor_total', '$id_cliente')";

        if (mysqli_query($conexao, $sql_pedido)) {
            $id_pedido = mysqli_insert_id($conexao);

            // Inserir na tabela item_pedido
            $sql_item = "INSERT INTO item_pedido (id_pedido, id_produto, quantidade, preco_unitario) 
                         VALUES ('$id_pedido', '$id_produto', '$quantidade', '$preco_unitario')";
            mysqli_query($conexao, $sql_item);

            // Atualiza o estoque do produto
            mysqli_query($conexao, "UPDATE produto SET estoque = GREATEST(0, estoque - $quantidade) WHERE id = $id_produto");

            $sucesso = true;
            $mensagem = "Pedido #$id_pedido cadastrado com sucesso! Valor total: R$ " . number_format($valor_total, 2, ',', '.');
        } else {
            $mensagem = "Erro ao cadastrar pedido: " . mysqli_error($conexao);
        }
    } else {
        $mensagem = "Produto não encontrado no sistema.";
    }
} else {
    $mensagem = "Por favor, selecione o cliente, produto e quantidade válida.";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultado - Cadastro de Pedido</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'Menu1.php'; ?>

    <div class="container">
        <h1>Status do Pedido</h1>

        <?php if ($sucesso): ?>
            <div class="alert alert-success">
                <?= $mensagem ?>
            </div>
            <a href="consultar_pedido.php" class="btn">Visualizar Todos os Pedidos</a>
            <a href="form_cad_pedido.php" class="btn btn-secondary" style="margin-top: 10px;">Cadastrar Novo Pedido</a>
        <?php else: ?>
            <div class="alert alert-error">
                <?= $mensagem ?>
            </div>
            <a href="form_cad_pedido.php" class="btn btn-secondary">Voltar ao Formulário</a>
        <?php endif; ?>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>
