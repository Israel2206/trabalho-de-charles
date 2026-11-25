<?php
include "verifica_admin.php";
// Inclui a conexão com o banco de dados
include "conexao.php";

// Recupera o ID enviado via URL (GET)
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$ingrediente = null;

// Busca os dados do ingrediente no banco para preencher o formulário
if ($id > 0) {
    $sql = "SELECT id, nome, unidade_medida, quantidade_atual FROM ingrediente WHERE id = ?";
    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $ingrediente = mysqli_fetch_assoc($resultado);
    }
}

// Se o ingrediente não for encontrado, redireciona para a consulta
if (!$ingrediente) {
    header("Location: consultar_ingrediente.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Ingrediente - Pizzaria da Chapada</title>
    
    <!-- Carregamento organizado de estilos na pasta style/ (Executado no topo do documento) -->
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

    <!-- Container Principal do Formulário -->
    <div class="split-container">
        
        <!-- Lado esquerdo: Arte e Descrição -->
        <div class="split-art">
            <div class="art-text">
                <h2>Atualização de Estoque</h2>
                <p>Altere os dados ou recalibre a quantidade em estoque para manter a cozinha organizada.</p>
            </div>
            <img src="BannerPizzaDaChapada.png" alt="Pizzaria da Chapada" class="art-image" onerror="this.style.display='none'">
        </div>

        <!-- Lado direito: Cartão do Formulário de Edição -->
        <div class="split-form-card">
            <h2>Editar Ingrediente #<?= (int)$ingrediente['id'] ?></h2>
            
            <form action="edit_ingrediente.php" method="POST" class="modern-form">
                
                <!-- ID Oculto para identificar qual registro atualizar -->
                <input type="hidden" name="id" value="<?= (int)$ingrediente['id'] ?>">
                
                <!-- Campo: Nome do Ingrediente -->
                <div class="form-group">
                    <label for="nome">Nome do Ingrediente:</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($ingrediente['nome'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <!-- Linha dupla: Unidade de Medida e Quantidade -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="unidade_medida">Unidade de Medida:</label>
                        <select id="unidade_medida" name="unidade_medida" required>
                            <option value="gramas" <?= $ingrediente['unidade_medida'] == 'gramas' ? 'selected' : '' ?>>Gramas (g)</option>
                            <option value="ml" <?= $ingrediente['unidade_medida'] == 'ml' ? 'selected' : '' ?>>Mililitros (ml)</option>
                            <option value="unidades" <?= $ingrediente['unidade_medida'] == 'unidades' ? 'selected' : '' ?>>Unidades</option>
                            <option value="kg" <?= $ingrediente['unidade_medida'] == 'kg' ? 'selected' : '' ?>>Quilogramas (kg)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantidade_atual">Quantidade em Estoque:</label>
                        <input type="number" step="1" min="0" id="quantidade_atual" name="quantidade_atual" value="<?= (int)$ingrediente['quantidade_atual'] ?>" required>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="form-buttons form-buttons--spaced">
                    <button type="submit" class="btn-dominos btn-primary" style="width: 100%; padding: 12px; border-radius: 20px; font-size: 15px; cursor: pointer;">
                        Salvar Alterações
                    </button>
                    <a href="consultar_ingrediente.php" class="btn-dominos btn-outline" style="text-align: center; text-decoration: none; padding: 10px; border-radius: 20px; font-size: 14px;">
                        Cancelar
                    </a>
                </div>

            </form>
        </div>

    </div>

    <!-- Rodapé Padronizado -->
    <?php include 'rodape.php'; ?>

</body>
</html>