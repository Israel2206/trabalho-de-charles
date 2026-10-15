<!DOCTYPE html>
<html lang="pt-br">

<?php include 'Menu1.php'   ?>
<head>

    <meta charset="UTF-8">

    <title>Cadastro de Produto</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="container">

        <h1>Produto</h1>

        <form action="cad_user.php" method="POST">
            <label>Nome</label>
            <input type="text" name="nome" required >

            <label>Preço</label>
            <input  type="number"  name="number"   required >

            <label>Estoque</label>
            <input  type="number"  name="number"   required  >

            <button type="submit"> Cadastrar produto </button>

        </form>

    </div>

</body>

  <?php include 'rodape.php'   ?>

</html>
