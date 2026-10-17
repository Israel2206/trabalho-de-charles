<?php
include "conexao.php";

// Busca clientes e produtos para os selects
$res_clientes = mysqli_query($conexao, "SELECT id, nome FROM cliente ORDER BY nome ASC");
$res_produtos = mysqli_query($conexao, "SELECT id, nome, preco FROM produto ORDER BY nome ASC");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Pedido - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css?v=6.0">
</head>
<body class="bg-pizzaria">
    <?php include 'Menu1.php'; ?>

    <div class="split-container">
        <!-- LADO ESQUERDO: Arte e Mensagem de Destaque -->
        <div class="split-art">
            <div class="art-text">
                <h2>Realizar Novo Pedido</h2>
                <p>Selecione o cliente, escolha os produtos e registre os pedidos com rapidez!</p>
            </div>
            <img src="Pizzaria-da-ChapadaLogin.png" alt="Pizzaria da Chapada Logo" class="art-image">
        </div>

        <!-- LADO DIREITO: Card do Formulário Moderno -->
        <div class="split-form-card">
            <h2>Novo Pedido</h2>

            <form action="cad_pedido.php" method="POST" class="modern-form">
                <div class="form-group">
                    <label>Cliente *</label>
                    <select name="id_cliente" required>
                        <option value="">Selecione o cliente...</option>
                        <?php if ($res_clientes && mysqli_num_rows($res_clientes) > 0): ?>
                            <?php while ($c = mysqli_fetch_assoc($res_clientes)): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <option value="" disabled>Nenhum cliente cadastrado</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Produto / Pizza *</label>
                    <select name="id_produto" required>
                        <option value="">Selecione a pizza ou produto...</option>
                        <?php if ($res_produtos && mysqli_num_rows($res_produtos) > 0): ?>
                            <?php while ($p = mysqli_fetch_assoc($res_produtos)): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['nome']) ?> - R$ <?= number_format($p['preco'], 2, ',', '.') ?>
                                </option>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <option value="" disabled>Nenhum produto cadastrado</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Quantidade *</label>
                        <input type="number" name="quantidade" min="1" value="1" required>
                    </div>

                    <div class="form-group">
                        <label>Status do Pedido</label>
                        <select name="status">
                            <option value="Pendente">Pendente</option>
                            <option value="Em preparo">Em preparo</option>
                            <option value="Saiu para entrega">Saiu para entrega</option>
                            <option value="Entregue">Entregue</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Data do Pedido</label>
                    <input type="date" name="data_pedido" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-buttons" style="margin-top: 15px;">
                    <button type="submit" class="btn-dominos btn-primary btn-full">Cadastrar Pedido</button>
                    <a href="consultar_pedido.php" class="btn-dominos btn-outline btn-full" style="display: block; text-align: center; box-sizing: border-box;">Ver Tabela de Pedidos</a>
                </div>
            </form>
        </div>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>