<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Categoria - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style/base.css?v=10.0">
    <link rel="stylesheet" href="style/menu.css?v=10.0">
    <link rel="stylesheet" href="style/rodape.css?v=10.0">
    <link rel="stylesheet" href="style/componentes.css?v=10.0">
    <link rel="stylesheet" href="style/formularios.css?v=10.0">
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
                    <textarea name="descricao" rows="3" placeholder="Descrição da categoria (opcional)"></textarea>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-dominos btn-primary btn-full">Cadastrar Categoria</button>
                    <a href="consultar_categoria.php" class="btn-dominos btn-outline btn-full">Ver Tabela de Categorias</a>
                </div>
            </form>
        </div>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>