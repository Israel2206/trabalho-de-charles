<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Categoria - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css?v=4.2">
</head>
<body class="bg-pizzaria">
    <?php include 'Menu1.php'; ?>

    <div class="split-container">
        <!-- LADO ESQUERDO: Arte e Mensagem de Destaque -->
        <div class="split-art">
            <div class="art-text">
                <h2>Gerencie o Cardápio</h2>
                <p>Organize suas categorias de pizzas e produtos com facilidade!</p>
            </div>
            <img src="Pizzaria-da-ChapadaLogin.png" alt="Pizzaria da Chapada Logo" class="art-image">
        </div>

        <!-- LADO DIREITO: Card do Formulário Moderno -->
        <div class="split-form-card">
            <h2>Cadastrar Categoria</h2>

            <form action="cad_categoria.php" method="POST" class="modern-form">
                <div class="form-group">
                    <label>Nome da Categoria *</label>
                    <input type="text" name="nome" placeholder="Ex: Pizzas Tradicionais, Bebidas, Sobremesas" required>
                </div>

                <div class="form-group">
                    <label>Descrição</label>
                    <textarea name="descricao" rows="3" placeholder="Descrição da categoria (opcional)" style="width: 100%; padding: 12px 14px; border: 1.5px solid #fcd34d; border-radius: 10px; font-size: 14px; font-family: 'Poppins', sans-serif; background-color: #fffdf5; color: #1f2937; resize: none; box-sizing: border-box;"></textarea>
                </div>

                <div class="form-buttons" style="margin-top: 15px;">
                    <button type="submit" class="btn-dominos btn-primary btn-full">Cadastrar Categoria</button>
                    <a href="consultar_categoria.php" class="btn-dominos btn-outline btn-full" style="display: block; text-align: center; box-sizing: border-box;">Ver Tabela de Categorias</a>
                </div>
            </form>
        </div>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>