<?php
include "verifica_admin.php";
include "conexao.php";

$tabela = isset($_GET["tabela"]) ? trim($_GET["tabela"]) : "";
$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$mensagem = "";
$sucesso = false;

$tabelas_permitidas = [
    "cliente"     => "consultar_user.php",
    "categoria"   => "consultar_categoria.php",
    "produto"     => "consultar_produto.php",
    "pedido"      => "consultar_pedido.php",
    "ingrediente" => "consultar_ingrediente.php",
];

if ($id > 0 && array_key_exists($tabela, $tabelas_permitidas)) {
    mysqli_begin_transaction($conexao);

    try {
        if ($tabela === "categoria") {
            // Busca os produtos ligados à categoria para remover dependências antes de excluir
            $stmt_prods = mysqli_prepare($conexao, "SELECT id FROM produto WHERE id_categoria = ?");
            if ($stmt_prods) {
                mysqli_stmt_bind_param($stmt_prods, "i", $id);
                mysqli_stmt_execute($stmt_prods);
                $res_prods = mysqli_stmt_get_result($stmt_prods);

                while ($res_prods && $prod = mysqli_fetch_assoc($res_prods)) {
                    $id_prod = (int) $prod["id"];
                    $stmt_del_item = mysqli_prepare($conexao, "DELETE FROM item_pedido WHERE id_produto = ?");
                    if ($stmt_del_item) {
                        mysqli_stmt_bind_param($stmt_del_item, "i", $id_prod);
                        mysqli_stmt_execute($stmt_del_item);
                    }
                    $stmt_del_ing = mysqli_prepare($conexao, "DELETE FROM produto_ingrediente WHERE id_produto = ?");
                    if ($stmt_del_ing) {
                        mysqli_stmt_bind_param($stmt_del_ing, "i", $id_prod);
                        mysqli_stmt_execute($stmt_del_ing);
                    }
                }
            }

            $stmt_del_prods = mysqli_prepare($conexao, "DELETE FROM produto WHERE id_categoria = ?");
            if ($stmt_del_prods) {
                mysqli_stmt_bind_param($stmt_del_prods, "i", $id);
                mysqli_stmt_execute($stmt_del_prods);
            }
        } elseif ($tabela === "cliente") {
            $stmt_ids_ped = mysqli_prepare($conexao, "SELECT id FROM pedido WHERE id_cliente = ?");
            if ($stmt_ids_ped) {
                mysqli_stmt_bind_param($stmt_ids_ped, "i", $id);
                mysqli_stmt_execute($stmt_ids_ped);
                $res_ped = mysqli_stmt_get_result($stmt_ids_ped);

                while ($res_ped && $ped = mysqli_fetch_assoc($res_ped)) {
                    $id_ped = (int) $ped["id"];
                    $stmt_del_item = mysqli_prepare($conexao, "DELETE FROM item_pedido WHERE id_pedido = ?");
                    if ($stmt_del_item) {
                        mysqli_stmt_bind_param($stmt_del_item, "i", $id_ped);
                        mysqli_stmt_execute($stmt_del_item);
                    }
                }
            }

            $stmt_del_ped = mysqli_prepare($conexao, "DELETE FROM pedido WHERE id_cliente = ?");
            if ($stmt_del_ped) {
                mysqli_stmt_bind_param($stmt_del_ped, "i", $id);
                mysqli_stmt_execute($stmt_del_ped);
            }
        } elseif ($tabela === "pedido") {
            $stmt_del_item = mysqli_prepare($conexao, "DELETE FROM item_pedido WHERE id_pedido = ?");
            if ($stmt_del_item) {
                mysqli_stmt_bind_param($stmt_del_item, "i", $id);
                mysqli_stmt_execute($stmt_del_item);
            }
        } elseif ($tabela === "produto") {
            $stmt_del_item_prod = mysqli_prepare($conexao, "DELETE FROM item_pedido WHERE id_produto = ?");
            if ($stmt_del_item_prod) {
                mysqli_stmt_bind_param($stmt_del_item_prod, "i", $id);
                mysqli_stmt_execute($stmt_del_item_prod);
            }
            $stmt_del_ing_prod = mysqli_prepare($conexao, "DELETE FROM produto_ingrediente WHERE id_produto = ?");
            if ($stmt_del_ing_prod) {
                mysqli_stmt_bind_param($stmt_del_ing_prod, "i", $id);
                mysqli_stmt_execute($stmt_del_ing_prod);
            }
        } elseif ($tabela === "ingrediente") {
            $stmt_del_prod_ing = mysqli_prepare($conexao, "DELETE FROM produto_ingrediente WHERE id_ingrediente = ?");
            if ($stmt_del_prod_ing) {
                mysqli_stmt_bind_param($stmt_del_prod_ing, "i", $id);
                mysqli_stmt_execute($stmt_del_prod_ing);
            }
        }

        $sql_del = "DELETE FROM `" . $tabela . "` WHERE id = ?";
        $stmt_del = mysqli_prepare($conexao, $sql_del);
        if (!$stmt_del) {
            throw new Exception("Erro ao preparar a exclusão.");
        }

        mysqli_stmt_bind_param($stmt_del, "i", $id);
        if (!mysqli_stmt_execute($stmt_del)) {
            throw new Exception("Erro ao executar a exclusão no banco.");
        }

        if (mysqli_stmt_affected_rows($stmt_del) === 0) {
            throw new Exception("Registro não encontrado para exclusão.");
        }

        mysqli_commit($conexao);

        if (function_exists("sincronizar_bd_sql")) {
            sincronizar_bd_sql($conexao);
        }

        $sucesso = true;
        $mensagem = "Registro #" . $id . " excluído com sucesso.";
    } catch (Throwable $e) {
        mysqli_rollback($conexao);
        $mensagem = "Não foi possível excluir o registro: " . $e->getMessage();
    }
} else {
    $mensagem = "Parâmetros inválidos para exclusão.";
}

$voltar_url = $tabelas_permitidas[$tabela] ?? "index.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Registro - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style/base.css?v=24.0">
    <link rel="stylesheet" href="style/menu.css?v=24.0">
    <link rel="stylesheet" href="style/rodape.css?v=24.0">
    <link rel="stylesheet" href="style/componentes.css?v=24.0">
    <link rel="stylesheet" href="style/resultado.css?v=24.0">
</head>
<body class="bg-pizzaria">
    <?php include "Menu1.php"; ?>

    <div class="main-wrapper result-wrapper">
        <div class="table-card-container result-card">
            <h2 class="result-title <?= $sucesso ? 'result-title-success' : 'result-title-error' ?>">
                <?= $sucesso ? "Excluído!" : "Atenção" ?>
            </h2>

            <div class="alert <?= $sucesso ? 'alert-success' : 'alert-error' ?>">
                <?= htmlspecialchars($mensagem, ENT_QUOTES, "UTF-8") ?>
            </div>

            <div class="result-buttons">
                <a href="<?= htmlspecialchars($voltar_url, ENT_QUOTES, "UTF-8") ?>" class="btn-dominos btn-primary">
                    Voltar para a Tabela
                </a>
                <a href="index.php" class="btn-dominos btn-outline">
                    Painel Administrativo
                </a>
            </div>
        </div>
    </div>

    <?php include "rodape.php"; ?>
</body>
</html>