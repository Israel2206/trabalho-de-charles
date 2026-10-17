<?php
include "conexao.php";

// Busca categorias para popular o select
$res_cat = mysqli_query($conexao, "SELECT id, nome FROM categoria ORDER BY nome ASC");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produto - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css?v=5.0">
</head>
<body class="bg-pizzaria">
    <?php include 'Menu1.php'; ?>

    <div class="split-container">
        <!-- LADO ESQUERDO: Arte e Mensagem de Destaque -->
        <div class="split-art">
            <div class="art-text">
                <h2>Cadastre os Sabores</h2>
                <p>Adicione novas pizzas e produtos deliciosos ao cardápio!</p>
            </div>
            <img src="Pizzaria-da-ChapadaLogin.png" alt="Pizzaria da Chapada Logo" class="art-image">
        </div>

        <!-- LADO DIREITO: Card do Formulário Moderno -->
        <div class="split-form-card">
            <h2>Cadastrar Produto</h2>

            <form action="cad_produto.php" method="POST" class="modern-form">
                <div class="form-group">
                    <label>Nome do Produto / Pizza *</label>
                    <input type="text" name="nome" placeholder="Ex: Pizza Calabresa Especial" required>
                </div>

                <div class="form-group">
                    <label>Categoria *</label>
                    <select name="id_categoria" required>
                        <option value="">Selecione uma categoria...</option>
                        <?php if ($res_cat && mysqli_num_rows($res_cat) > 0): ?>
                            <?php while ($cat = mysqli_fetch_assoc($res_cat)): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <option value="" disabled>Nenhuma categoria cadastrada</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Preço (R$) *</label>
                        <input type="number" name="preco" step="0.01" min="0" placeholder="Ex: 45.90" required>
                    </div>

                    <div class="form-group">
                        <label>Quantidade em Estoque *</label>
                        <input type="number" name="estoque" min="0" value="10" required>
                    </div>
                </div>

                <div class="form-buttons" style="margin-top: 15px;">
                    <button type="submit" class="btn-dominos btn-primary btn-full">Cadastrar Produto</button>
                    <a href="consultar_produto.php" class="btn-dominos btn-outline btn-full" style="display: block; text-align: center; box-sizing: border-box;">Ver Tabela de Produtos</a>
                </div>
            </form>
        </div>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>