<!DOCTYPE html>
<html lang="pt-br">

<?php include 'Menu1.php'   ?>
<head>

    <meta charset="UTF-8">

    <title>Cadastro de Usuário</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="container">

        <h1>Categoria</h1>

        <form action="cad_user.php" method="POST">
            <label>Nome</label>
            <input type="text" name="nome" required >

            <label>Descricao</label>
            <input  type="text"  name="text"   required >


            <button type="submit"> Cadastrar Categoria </button>
        </form>

    </div>

</body>

  <?php include 'rodape.php'   ?>

</html>
