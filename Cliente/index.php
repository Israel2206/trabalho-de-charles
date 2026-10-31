<?php
include "conexao.php";

$total_clientes = 0;
$total_categorias = 0;
$total_produtos = 0;
$total_pedidos = 0;

$q_cli = @mysqli_query($conexao, "SELECT COUNT(*) as total FROM cliente");
if ($q_cli) { $total_clientes = mysqli_fetch_assoc($q_cli)['total']; }

$q_cat = @mysqli_query($conexao, "SELECT COUNT(*) as total FROM categoria");
if ($q_cat) { $total_categorias = mysqli_fetch_assoc($q_cat)['total']; }

$q_pro = @mysqli_query($conexao, "SELECT COUNT(*) as total FROM produto");
if ($q_pro) { $total_produtos = mysqli_fetch_assoc($q_pro)['total']; }

$q_ped = @mysqli_query($conexao, "SELECT COUNT(*) as total FROM pedido");
if ($q_ped) { $total_pedidos = mysqli_fetch_assoc($q_ped)['total']; }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style/base.css?v=10.0">
    <link rel="stylesheet" href="style/menu.css?v=10.0">
    <link rel="stylesheet" href="style/rodape.css?v=10.0">
    <link rel="stylesheet" href="style/componentes.css?v=10.0">
    <link rel="stylesheet" href="style/index.css?v=10.0">
</head>
<body class="bg-pizzaria">
    <?php include 'Menu1.php'; ?>

    <div class="main-wrapper">
        <div class="painel-title">
            <h2>Painel Pizzaria da Chapada</h2>
            <p>Sistema de Gestão simples e integrado com o banco de dados.</p>
        </div>

        <div class="dominos-grid">
            <!-- Slot Clientes -->
            <div class="dominos-card">
                <div class="card-header-info">
                    <h3>Clientes</h3>
                    <span class="badge-count"><?= $total_clientes ?> cadastrados</span>
                </div>
                <div class="card-actions">
                    <a href="form_cad_user.php" class="btn-dominos btn-primary">+ Cadastrar</a>
                    <a href="consultar_user.php" class="btn-dominos btn-outline">Ver Tabela</a>
                </div>
            </div>

            <!-- Slot Categorias -->
            <div class="dominos-card">
                <div class="card-header-info">
                    <h3>Categorias</h3>
                    <span class="badge-count"><?= $total_categorias ?> cadastradas</span>
                </div>
                <div class="card-actions">
                    <a href="form_cad_categoria.php" class="btn-dominos btn-primary">+ Cadastrar</a>
                    <a href="consultar_categoria.php" class="btn-dominos btn-outline">Ver Tabela</a>
                </div>
            </div>

            <!-- Slot Produtos -->
            <div class="dominos-card">
                <div class="card-header-info">
                    <h3>Produtos</h3>
                    <span class="badge-count"><?= $total_produtos ?> cadastrados</span>
                </div>
                <div class="card-actions">
                    <a href="form_cad_produto.php" class="btn-dominos btn-primary">+ Cadastrar</a>
                    <a href="consultar_produto.php" class="btn-dominos btn-outline">Ver Tabela</a>
                </div>
            </div>

            <!-- Slot Pedidos -->
            <div class="dominos-card">
                <div class="card-header-info">
                    <h3>Pedidos</h3>
                    <span class="badge-count"><?= $total_pedidos ?> realizados</span>
                </div>
                <div class="card-actions">
                    <a href="form_cad_pedido.php" class="btn-dominos btn-primary">+ Novo Pedido</a>
                    <a href="consultar_pedido.php" class="btn-dominos btn-outline">Ver Tabela</a>
                </div>
            </div>
        </div>

        <div class="dica-card">
            <h4>Bem-Vindo!!!</h4>
            <p>Cadastre as coisa ai!</p>
        </div>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>