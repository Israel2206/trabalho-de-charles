<?php
include "conexao.php";

// Contagens rápidas de registros
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
    <title>Painel - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'Menu1.php'; ?>

    <div class="container-large">
        <h1>🍕 Painel Pizzaria da Chapada</h1>
        <p style="text-align: center; color: #6b7280; margin-top: -15px; margin-bottom: 30px;">
            Sistema de Gestão simples e integrado com o banco de dados.
        </p>

        <div class="cards-grid">
            <!-- Card Clientes -->
            <div class="card">
                <h3>👥 Clientes</h3>
                <div class="card-number"><?= $total_clientes ?></div>
                <div>
                    <a href="form_cad_user.php">+ Cadastrar</a> | 
                    <a href="consultar_user.php">Ver Tabela</a>
                </div>
            </div>

            <!-- Card Categorias -->
            <div class="card">
                <h3>📁 Categorias</h3>
                <div class="card-number"><?= $total_categorias ?></div>
                <div>
                    <a href="form_cad_categoria.php">+ Cadastrar</a> | 
                    <a href="consultar_categoria.php">Ver Tabela</a>
                </div>
            </div>

            <!-- Card Produtos -->
            <div class="card">
                <h3>🍕 Produtos</h3>
                <div class="card-number"><?= $total_produtos ?></div>
                <div>
                    <a href="form_cad_produto.php">+ Cadastrar</a> | 
                    <a href="consultar_produto.php">Ver Tabela</a>
                </div>
            </div>

            <!-- Card Pedidos -->
            <div class="card">
                <h3>📦 Pedidos</h3>
                <div class="card-number"><?= $total_pedidos ?></div>
                <div>
                    <a href="form_cad_pedido.php">+ Novo Pedido</a> | 
                    <a href="consultar_pedido.php">Ver Tabela</a>
                </div>
            </div>
        </div>

        <div style="margin-top: 40px; padding: 20px; background-color: #fef3c7; border-radius: 8px; border: 1px solid #fde68a; color: #92400e;">
            <h3 style="margin-top: 0; color: #92400e; font-size: 16px;">💡 Dica de Utilização:</h3>
            <p style="margin: 5px 0; font-size: 14px;">
                1. Comece cadastrando <strong>Clientes</strong> e <strong>Categorias</strong>.<br>
                2. Depois cadastre os <strong>Produtos</strong> vinculando à categoria desejada.<br>
                3. Por fim, realize novos <strong>Pedidos</strong> selecionando o cliente e o produto!
            </p>
        </div>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>
