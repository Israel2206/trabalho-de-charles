<?php
include "verifica_admin.php";
include "conexao.php";

// Busca categorias para popular o select
$res_cat = mysqli_query($conexao, "SELECT id, nome FROM categoria ORDER BY nome ASC");
$lista_categorias = [];
if ($res_cat && mysqli_num_rows($res_cat) > 0) {
    while ($cat = mysqli_fetch_assoc($res_cat)) {
        $lista_categorias[] = $cat;
    }
}

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
                <p>Adicione novas pizzas, bebidas ou sobremesas, defina preços e configure a ficha técnica quando a categoria for de pizza!</p>
            </div>
            <img src="Pizzaria-da-ChapadaLogin.png" alt="Pizzaria da Chapada Logo" class="art-image">
        </div>

        <!-- LADO DIREITO: Card do Formulário Moderno -->
        <div class="split-form-card">
            <h2>Cadastrar Produto</h2>

            <form action="cad_produto.php" method="POST" class="modern-form" onsubmit="return validarFormulario();">
                <div class="form-group">
                    <label>Nome do Produto / Pizza *</label>
                    <input type="text" name="nome" placeholder="Ex: Pizza Calabresa Especial ou Coca-Cola" required>
                </div>

                <div class="form-group">
                    <label for="id_categoria">Categoria *</label>
                    <select name="id_categoria" id="id_categoria" required onchange="atualizarObrigatoriedadeIngredientes()">
                        <option value="" data-nome="">Selecione uma categoria...</option>
                        <?php if (count($lista_categorias) > 0): ?>
                            <?php foreach ($lista_categorias as $cat): ?>
                                <option
                                    value="<?= (int) $cat['id'] ?>"
                                    data-nome="<?= htmlspecialchars($cat['nome'], ENT_QUOTES, 'UTF-8') ?>"
                                >
                                    <?= htmlspecialchars($cat['nome'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
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

                <!-- SEÇÃO DA FICHA TÉCNICA (OBRIGATÓRIA QUANDO A CATEGORIA FOR DE PIZZA) -->
                <div class="ingredients-section" id="ingredients-section">
                    <label class="ingredients-section-label" id="ingredients-label">
                        Ficha Técnica / Ingredientes Necessários (Obrigatório para Pizza)
                    </label>
                    <small id="ingredients-hint" style="display:block; margin-bottom:10px; color:#57534e;">
                        Selecione uma categoria de pizza para informar os ingredientes necessários.
                    </small>

                    <div id="ingredients-container">
                        <!-- Os slots dinâmicos gerados via JavaScript entram aqui -->
                    </div>
                    
                    <?php if (count($lista_ingredientes) > 0): ?>
                        <button type="button" class="btn-add-ing" id="btn-add-ing" onclick="addIngredientRow()">+ Adicionar Ingrediente</button>
                    <?php else: ?>
                        <p class="ingredients-empty-msg">Nenhum ingrediente cadastrado no sistema. É necessário cadastrar insumos na aba de ingredientes antes de cadastrar pizzas.</p>
                    <?php endif; ?>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-dominos btn-primary btn-full">Cadastrar Produto</button>
                    <a href="consultar_produto.php" class="btn-dominos btn-outline btn-full">Ver Tabela de Produtos</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Script JavaScript para gerenciar slots e validar a obrigatoriedade apenas quando categoria começa com "pizza" -->
    <script>
        const ingredientesDisponiveis = <?= json_encode($lista_ingredientes) ?>;

        function categoriaEhPizza() {
            const selectCat = document.getElementById('id_categoria');
            if (!selectCat) return false;
            const option = selectCat.options[selectCat.selectedIndex];
            const nomeCat = option ? (option.getAttribute('data-nome') || option.textContent || '') : '';
            const normalizado = nomeCat
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim()
                .toLowerCase();
            return normalizado.includes('pizza');
        }

        function atualizarObrigatoriedadeIngredientes() {
            const ehPizza = categoriaEhPizza();
            const secao = document.getElementById('ingredients-section');
            const label = document.getElementById('ingredients-label');
            const hint = document.getElementById('ingredients-hint');
            const container = document.getElementById('ingredients-container');
            const btnAdd = document.getElementById('btn-add-ing');

            const selects = container.querySelectorAll('.ingredient-select');
            const inputs = container.querySelectorAll('.ingredient-input-qtd');

            if (ehPizza) {
                if (secao) secao.style.display = '';
                if (btnAdd) btnAdd.style.display = '';
                if (label) label.textContent = 'Ficha Técnica / Ingredientes Necessários *';
                if (hint) hint.textContent = 'Categoria de pizza identificada: informe ao menos um ingrediente e sua quantidade.';

                if (container.children.length === 0 && ingredientesDisponiveis.length > 0) {
                    addIngredientRow(true);
                } else {
                    selects.forEach(s => s.required = true);
                    inputs.forEach(i => i.required = true);
                }
            } else {
                if (secao) secao.style.display = 'none';
                if (btnAdd) btnAdd.style.display = 'none';
                if (label) label.textContent = 'Ficha Técnica / Ingredientes Necessários (Opcional)';
                if (hint) hint.textContent = 'Categoria não é de pizza: ingredientes não são obrigatórios.';

                selects.forEach(s => {
                    s.required = false;
                    s.value = '';
                });
                inputs.forEach(i => {
                    i.required = false;
                    i.value = '';
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (ingredientesDisponiveis.length > 0) {
                addIngredientRow(false);
            }
            atualizarObrigatoriedadeIngredientes();
        });

        function addIngredientRow(obrigatorio = null) {
            const container = document.getElementById('ingredients-container');
            const exigir = (obrigatorio === null) ? categoriaEhPizza() : obrigatorio;

            let optionsHtml = '<option value="">Selecione o ingrediente...</option>';
            ingredientesDisponiveis.forEach(ing => {
                optionsHtml += `<option value="${ing.id}">${ing.nome} (${ing.unidade_medida})</option>`;
            });

            const row = document.createElement('div');
            row.className = 'ingredient-row';
            row.innerHTML = `
                <select name="ingredientes[]" class="ingredient-select" ${exigir ? 'required' : ''}>
                    ${optionsHtml}
                </select>
                <input type="number" name="quantidades[]" class="ingredient-input-qtd" placeholder="Qtd unit." min="0.01" step="any" ${exigir ? 'required' : ''}>
                <button type="button" class="btn-remove-ing" onclick="removerLinha(this)">X</button>
            `;
            container.appendChild(row);
        }

        function removerLinha(botao) {
            const container = document.getElementById('ingredients-container');
            const ehPizza = categoriaEhPizza();

            if (container.children.length > 1) {
                botao.parentElement.remove();
            } else if (!ehPizza) {
                const select = botao.parentElement.querySelector('.ingredient-select');
                const input = botao.parentElement.querySelector('.ingredient-input-qtd');
                if (select) select.value = '';
                if (input) input.value = '';
            } else {
                alert("Para categorias de pizza, a ficha técnica precisa ter pelo menos um ingrediente.");
            }
        }

        function validarFormulario() {
            if (!categoriaEhPizza()) {
                return true;
            }

            const container = document.getElementById('ingredients-container');
            const selects = container.querySelectorAll('.ingredient-select');
            const inputs = container.querySelectorAll('.ingredient-input-qtd');

            if (container.children.length === 0 || selects.length === 0) {
                alert("Para categorias iniciadas com 'Pizza', adicione pelo menos um ingrediente.");
                return false;
            }

            for (let i = 0; i < selects.length; i++) {
                const ingVal = selects[i].value;
                const qtdVal = parseFloat(inputs[i] ? inputs[i].value : '0');
                if (!ingVal || isNaN(qtdVal) || qtdVal <= 0) {
                    alert("Preencha o ingrediente e uma quantidade maior que zero para a pizza.");
                    return false;
                }
            }

            return true;
        }
    </script>

    <?php include 'rodape.php'; ?>
</body>
</html>