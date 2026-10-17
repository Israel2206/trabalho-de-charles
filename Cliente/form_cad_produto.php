<?php
include "conexao.php";

// Busca categorias para popular o select
$res_cat = mysqli_query($conexao, "SELECT id, nome FROM categoria ORDER BY nome ASC");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Produto - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'Menu1.php'; ?>

    <div class="container">
        <h1>Cadastrar Produto</h1>

        <form action="cad_produto.php" method="POST">
            <label>Nome do Produto / Pizza *</label>
            <input type="text" name="nome" placeholder="Ex: Pizza Calabresa Especial" required>

            <label>Categoria *</label>
            <select name="id_categoria" required>
                <option value="">Selecione uma categoria...</option>
                <?php if ($res_cat && mysqli_num_rows($res_cat) > 0): ?>
                    <?php while ($cat = mysqli_fetch_assoc($res_cat)): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                    <?php endwhile; ?>
                <?php else: ?>
                    <option value="" disabled>Nenhuma categoria cadastrada (cadastre uma primeiro)</option>
                <?php endif; ?>
            </select>

            <label>Preço (R$) *</label>
            <input type="number" name="preco" step="0.01" min="0" placeholder="Ex: 45.90" required>

            <label>Quantidade em Estoque *</label>
            <input type="number" name="estoque" min="0" value="10" required>

            <button type="submit">Cadastrar Produto</button>
            <a href="consultar_produto.php" class="btn btn-secondary" style="margin-top: 10px;">Ver Tabela de Produtos</a>
        </form>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>
