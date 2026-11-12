<?php
// Inclui a conexão com o banco de dados
include "conexao.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Ingrediente - Pizzaria da Chapada</title>
    
    <!-- Folhas de estilo externas unificadas na pasta style/ -->
    <link rel="stylesheet" href="style/base.css?v=10.0">
    <link rel="stylesheet" href="style/menu.css?v=10.0">
    <link rel="stylesheet" href="style/rodape.css?v=10.0">
    <link rel="stylesheet" href="style/componentes.css?v=10.0">
    <link rel="stylesheet" href="style/formularios.css?v=10.0">
    <link rel="stylesheet" href="style/ingrediente.css?v=10.0">
</head>
<body class="bg-pizzaria">

    <!-- Inclusão do Menu Superior Padronizado -->
    <?php include 'Menu1.php'; ?>

    <div class="split-container">
        
        <!-- Lado esquerdo: Seção visual e descritiva -->
        <div class="split-art">
            <div class="art-text">
                <h2>Controle de Estoque e Insumos</h2>
                <p>Cadastre os ingredientes para sincronizar com o controle de receitas da cozinha.</p>
            </div>
            <img src="BannerPizzaDaChapada.png" alt="Ingredientes" class="art-image" onerror="this.style.display='none'">
        </div>

        <!-- Lado direito: Cartão do Formulário -->
        <div class="split-form-card">
            <h2>Novo Ingrediente</h2>
            
            <form action="cad_ingrediente.php" method="POST" class="modern-form">
                
                <!-- Nome do Ingrediente -->
                <div class="form-group">
                    <label for="nome">Nome do Ingrediente:</label>
                    <input type="text" id="nome" name="nome" placeholder="Ex: Mussarela, Calabresa, Molho..." required>
                </div>

                <!-- Unidade de Medida e Quantidade -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="unidade_medida">Unidade de Medida:</label>
                        <select id="unidade_medida" name="unidade_medida" required>
                            <option value="gramas">Gramas (g)</option>
                            <option value="ml">Mililitros (ml)</option>
                            <option value="unidades">Unidades</option>
                            <option value="kg">Quilogramas (kg)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantidade_atual">Quantidade:</label>
                        <input type="number" step="any" min="0" id="quantidade_atual" name="quantidade_atual" placeholder="Ex: 10, 50, 100..." required>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="form-buttons form-buttons--spaced">
                    <button type="submit" class="btn-dominos btn-primary" style="width: 100%; padding: 12px; border-radius: 20px; font-size: 15px; cursor: pointer;">
                        Salvar Ingrediente
                    </button>
                    <a href="index.php" class="btn-dominos btn-outline" style="text-align: center; text-decoration: none; padding: 10px; border-radius: 20px; font-size: 14px;">
                        Voltar ao Painel
                    </a>
                </div>

            </form>
        </div>

    </div>

    <!-- Rodapé Padrão -->
    <?php include 'rodape.php'; ?>

</body>
</html>