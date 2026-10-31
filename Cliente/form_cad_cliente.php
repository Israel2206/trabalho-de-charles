<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Rápido de Cliente - Pizzaria da Chapada</title>
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
                <h2>Cadastro de Clientes</h2>
                <p>Cadastre os dados de entrega para agilizar os pedidos da pizzaria!</p>
            </div>
            <img src="Pizzaria-da-ChapadaLogin.png" alt="Pizzaria da Chapada Logo" class="art-image">
        </div>

        <!-- LADO DIREITO: Card do Formulário Moderno -->
        <div class="split-form-card">
            <h2>Cadastrar Cliente</h2>

            <form action="cad_cliente.php" method="POST" class="modern-form">
                <div class="form-group">
                    <label>Nome Completo *</label>
                    <input type="text" name="nome" placeholder="Nome do cliente" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Telefone / WhatsApp *</label>
                        <input type="text" name="telefone" placeholder="(75) 99999-9999" required>
                    </div>

                    <div class="form-group">
                        <label>E-mail (opcional)</label>
                        <input type="email" name="email" placeholder="cliente@email.com">
                    </div>
                </div>

                <div class="form-group">
                    <label>Endereço de Entrega *</label>
                    <input type="text" name="endereco" placeholder="Rua, número, ponto de referência" required>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-dominos btn-primary btn-full">Salvar no Banco de Dados</button>
                    <a href="consultar_user.php" class="btn-dominos btn-outline btn-full">Ver Tabela de Clientes</a>
                </div>
            </form>
        </div>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>
