<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Categoria - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'Menu1.php'; ?>

    <div class="container">
        <h1>Cadastrar Categoria</h1>

        <form action="cad_categoria.php" method="POST">
            <label>Nome da Categoria *</label>
            <input type="text" name="nome" placeholder="Ex: Pizzas Tradicionais, Bebidas, Sobremesas" required>

            <label>Descrição</label>
            <textarea name="descricao" rows="4" placeholder="Descrição da categoria (opcional)"></textarea>

            <button type="submit">Cadastrar Categoria</button>
            <a href="consultar_categoria.php" class="btn btn-secondary" style="margin-top: 10px;">Ver Tabela de Categorias</a>
        </form>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>
