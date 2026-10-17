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
    <title>Novo Pedido - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'Menu1.php'; ?>

    <div class="container">
        <h1>Novo Pedido</h1>

        <form action="cad_pedido.php" method="POST">
            <label>Cliente *</label>
            <select name="id_cliente" required>
                <option value="">Selecione o cliente...</option>
                <?php if ($res_clientes && mysqli_num_rows($res_clientes) > 0): ?>
                    <?php while ($c = mysqli_fetch_assoc($res_clientes)): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                    <?php endwhile; ?>
                <?php else: ?>
                    <option value="" disabled>Nenhum cliente cadastrado (cadastre um primeiro)</option>
                <?php endif; ?>
            </select>

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
                    <option value="" disabled>Nenhum produto cadastrado (cadastre um primeiro)</option>
                <?php endif; ?>
            </select>

            <label>Quantidade *</label>
            <input type="number" name="quantidade" min="1" value="1" required>

            <label>Status do Pedido</label>
            <select name="status">
                <option value="Pendente">Pendente</option>
                <option value="Em preparo">Em preparo</option>
                <option value="Saiu para entrega">Saiu para entrega</option>
                <option value="Entregue">Entregue</option>
            </select>

            <label>Data do Pedido</label>
            <input type="date" name="data_pedido" value="<?= date('Y-m-d') ?>" required>

            <button type="submit">Cadastrar Pedido</button>
            <a href="consultar_pedido.php" class="btn btn-secondary" style="margin-top: 10px;">Ver Tabela de Pedidos</a>
        </form>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>
