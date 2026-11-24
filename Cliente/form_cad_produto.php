<?php
include "conexao.php";

// Busca categorias para popular o select
$res_cat = mysqli_query($conexao, "SELECT id, nome FROM categoria ORDER BY nome ASC");

// Busca os ingredientes cadastrados na tabela para popular as opções dinâmicas da ficha técnica
$res_ing = mysqli_query($conexao, "SELECT id, nome, unidade_medida FROM ingrediente ORDER BY nome ASC");
$lista_ingredientes = [];
if ($res_ing && mysqli_num_rows($res_ing) > 0) {
    while ($ing = mysqli_fetch_assoc($res_ing)) {
        $lista_ingredientes[] = $ing;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produto - Pizzaria da Chapada</title>
    
    <!-- Folhas de estilo externas unificadas na pasta style/ -->
    <link rel="stylesheet" href="style/base.css?v=10.0">
    <link rel="stylesheet" href="style/menu.css?v=10.0">
    <link rel="stylesheet" href="style/rodape.css?v=10.0">
    <link rel="stylesheet" href="style/componentes.css?v=10.0">
    <link rel="stylesheet" href="style/formularios.css?v=10.0">
    <link rel="stylesheet" href="style/ingrediente.css?v=10.0">
</head>
<body class="bg-pizzaria">
    <?php include 'Menu1.php'; ?>

    <div class="split-container">
        <!-- LADO ESQUERDO: Arte e Mensagem de Destaque -->
        <div class="split-art">
            <div class="art-text">
                <h2>Cadastre os Sabores</h2>
                <p>Adicione novas pizzas, defina preços e configure a ficha técnica obrigatória com os ingredientes necessários!</p>
            </div>
            <img src="Pizzaria-da-ChapadaLogin.png" alt="Pizzaria da Chapada Logo" class="art-image">
        </div>

        <!-- LADO DIREITO: Card do Formulário Moderno -->
        <div class="split-form-card">
            <h2>Cadastrar Produto</h2>

            <form action="cad_produto.php" method="POST" class="modern-form" onsubmit="return validarFormulario();">
                <div class="form-group">
                    <label>Nome do Produto / Pizza *</label>
                    <input type="text" name="nome" placeholder="Ex: Pizza Calabresa Especial" required>
                </div>

                <div class="form-group">
                    <label>Categoria *</label>
                    <select name="id_categoria" required>
                        <option value="">Selecione uma categoria...</option>
                        <?php if ($res_cat && mysqli_num_rows($res_cat) > 0): ?>
                            <?php while ($cat = mysqli_fetch_assoc($res_cat)): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <option value="" disabled>Nenhuma categoria cadastrada</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Preço (R$) *</label>
                        <input type="number" name="preco" step="0.01" min="0" placeholder="Ex: 45.90" required>
                    </div>

                    <div class="form-group">
                        <label>Quantidade em Estoque (Lotes) *</label>
                        <input type="number" id="estoque" name="estoque" min="1" value="10" required>
                    </div>
                </div>

                <!-- SEÇÃO DA FICHA TÉCNICA OBRIGATÓRIA -->
                <div class="ingredients-section">
                    <label class="ingredients-section-label">Ficha Técnica / Ingredientes Necessários *</label>
                    <div id="ingredients-container">
                        <!-- Os slots dinâmicos gerados via JavaScript entram aqui -->
                    </div>
                    
                    <?php if (count($lista_ingredientes) > 0): ?>
                        <button type="button" class="btn-add-ing" onclick="addIngredientRow()">+ Adicionar Ingrediente</button>
                    <?php else: ?>
                        <p class="ingredients-empty-msg">Nenhum ingrediente cadastrado no sistema. É necessário cadastrar insumos na aba de ingredientes antes de cadastrar produtos.</p>
                    <?php endif; ?>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-dominos btn-primary btn-full">Cadastrar Produto</button>
                    <a href="consultar_produto.php" class="btn-dominos btn-outline btn-full">Ver Tabela de Produtos</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Script JavaScript para gerenciar slots e validar a obrigatoriedade -->
    <script>
        const ingredientesDisponiveis = <?= json_encode($lista_ingredientes) ?>;

        // Adiciona a primeira linha de ingrediente automaticamente ao carregar a página
        window.onload = function() {
            if (ingredientesDisponiveis.length > 0) {
                addIngredientRow();
            }
        };

        function addIngredientRow() {
            const container = document.getElementById('ingredients-container');
            
            let optionsHtml = '<option value="">Selecione o ingrediente...</option>';
            ingredientesDisponiveis.forEach(ing => {
                optionsHtml += `<option value="${ing.id}">${ing.nome} (${ing.unidade_medida})</option>`;
            });

            const row = document.createElement('div');
            row.className = 'ingredient-row';
            row.innerHTML = `
                <select name="ingredientes[]" class="ingredient-select" required>
                    ${optionsHtml}
                </select>
                <input type="number" name="quantidades[]" class="ingredient-input-qtd" placeholder="Qtd unit." min="0.01" step="any" required>
                <button type="button" class="btn-remove-ing" onclick="removerLinha(this)">X</button>
            `;
            container.appendChild(row);
        }

        function removerLinha(botao) {
            const container = document.getElementById('ingredients-container');
            if (container.children.length > 1) {
                botao.parentElement.remove();
            } else {
                alert("A ficha técnica precisa ter pelo menos um ingrediente.");
            }
        }

        function validarFormulario() {
            const container = document.getElementById('ingredients-container');
            if (container.children.length === 0) {
                alert("Adicione pelo menos um ingrediente à receita do produto.");
                return false;
            }
            return true;
        }
    </script>

    <?php include 'rodape.php'; ?>
</body>
</html>