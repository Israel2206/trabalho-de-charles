<!DOCTYPE html>
<html lang="pt-br">

<?php include 'Menu1.php'   ?>
<head>

    <meta charset="UTF-8">

    <title>Cadastro de Cliente</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="container">

        <h1>Cadastro de Cliente</h1>

        <form action="cad_cliente.php" method="POST">
            <label>Nome</label>
            <input type="text" name="nome" required >

            <label>telefone</label>
            <input  type="number"  name="telefone"   required >

            <label>endereco</label>
            <input  type="text"  name="endereco"   required  >

            <button type="submit"> Cadastrar Cliente </button>
        </form>

    </div>

</body>

  <?php include 'rodape.php'   ?>

</html>
