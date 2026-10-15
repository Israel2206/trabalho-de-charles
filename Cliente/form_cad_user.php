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

        <h1>Cadastro</h1>

        <form action="cad_user.php" method="POST">
            <label>Nome</label>
            <input type="text" name="nome" required >

            <label>E-mail</label>
            <input  type="email"  name="email"   required >

            <label>Senha</label>
            <input  type="password"  name="senha"   required  >

            <label>Data</label>
            <input  type="date"  name="datacad"   required  >

            <label>Nivel de acesso</label>
            <input type="number" name="nivel" required >


            <button type="submit"> Cadastrar Cliente </button>
        </form>

    </div>

</body>

  <?php include 'rodape.php'   ?>

</html>
