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

        <h1>Pedido</h1>

        <form action="cad_user.php" method="POST">
            <label>data_pedido</label>
            <input type="date" name="data" required >

            <label>valor_total</label>
            <input  type="number"  name="number"   required >

            <label>quantidade</label>
            <input  type="number"  name="number"   required >

            <label>preco_unitario</label>
            <input  type="number"  name="number"   required >

            <button type="submit"> Cadastrar pedido </button>
        </form>

    </div>

</body>

  <?php include 'rodape.php'   ?>

</html>
