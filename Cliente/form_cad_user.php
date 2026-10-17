<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Cliente - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'Menu1.php'; ?>

    <div class="container">
        <h1>Cadastrar Cliente</h1>

        <form action="cad_user.php" method="POST">
            <label>Nome completo *</label>
            <input type="text" name="nome" placeholder="Digite o nome" required>

            <label>E-mail *</label>
            <input type="email" name="email" placeholder="cliente@exemplo.com" required>

            <label>Senha *</label>
            <input type="password" name="senha" placeholder="Digite a senha" required>

            <label>Telefone</label>
            <input type="text" name="telefone" placeholder="(75) 99999-9999">

            <label>Endereço de entrega</label>
            <input type="text" name="endereco" placeholder="Rua, número, bairro">

            <label>Data de Cadastro</label>
            <input type="date" name="datacad" value="<?= date('Y-m-d') ?>" required>

            <label>Nível de acesso</label>
            <select name="nivel">
                <option value="1">Cliente (Padrão)</option>
                <option value="2">Administrador</option>
            </select>

            <button type="submit">Cadastrar Cliente</button>
            <a href="consultar_user.php" class="btn btn-secondary" style="margin-top: 10px;">Ver Tabela de Clientes</a>
        </form>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>
