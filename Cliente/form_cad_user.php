<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Cliente - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css?v=3.0">
</head>
<body class="bg-pizzaria">
    <?php include 'Menu1.php'; ?>

    <div class="split-container">
        <!-- LADO ESQUERDO: Arte e Mensagem de Destaque -->
        <div class="split-art">
            <div class="art-text">
                <h2>Faça seu Cadastro</h2>
                <p>E venha saborear as melhores pizzas da Chapada!</p>
            </div>
            <img src="Pizzaria-da-ChapadaLogin.png" alt="Pizzaria da Chapada Logo" class="art-image">
        </div>

        <!-- LADO DIREITO: Card do Formulário Moderno -->
        <div class="split-form-card">
            <h2>Cadastrar Cliente</h2>

            <form action="cad_user.php" method="POST" class="modern-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome completo *</label>
                        <input type="text" name="nome" placeholder="Digite o nome completo" required>
                    </div>

                    <div class="form-group">
                        <label>E-mail *</label>
                        <input type="email" name="email" placeholder="cliente@exemplo.com" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Senha *</label>
                        <input type="password" name="senha" placeholder="Digite a senha" required>
                    </div>

                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="text" name="telefone" placeholder="(75) 99999-9999">
                    </div>
                </div>

                <div class="form-group">
                    <label>Endereço de entrega</label>
                    <input type="text" name="endereco" placeholder="Rua, número, bairro">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Data de Cadastro</label>
                        <input type="date" name="datacad" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Nível de acesso</label>
                        <select name="nivel">
                            <option value="1">Cliente (Padrão)</option>
                            <option value="2">Administrador</option>
                        </select>
                    </div>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-dominos btn-primary btn-full">Cadastrar Cliente</button>
                    <a href="consultar_user.php" class="btn-dominos btn-outline btn-full">Ver Tabela de Clientes</a>
                </div>
            </form>
        </div>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>