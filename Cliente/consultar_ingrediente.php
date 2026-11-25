<?php
include "verifica_admin.php";
// Inclui a conexão com o banco de dados
include "conexao.php";

// Lógica de exclusão rápida direto na mesma tela
if (isset($_GET['acao']) && $_GET['acao'] === 'excluir' && isset($_GET['id'])) {
    $id_excluir = intval($_GET['id']);
    
    if ($id_excluir > 0) {
        $sql_del = "DELETE FROM ingrediente WHERE id = ?";
        $stmt_del = mysqli_prepare($conexao, $sql_del);
        mysqli_stmt_bind_param($stmt_del, "i", $id_excluir);
        mysqli_stmt_execute($stmt_del);
        if (function_exists("sincronizar_bd_sql")) {
            sincronizar_bd_sql($conexao);
        }
    }
    
    // Recarrega a própria página limpa (sem parâmetros GET) mantendo o usuário na tela
    header("Location: consultar_ingrediente.php");
    exit;
}

// Consulta os ingredientes cadastrados do mais recente para o mais antigo
$sql = "SELECT id, nome, unidade_medida, quantidade_atual 
        FROM ingrediente 
        ORDER BY id DESC";

$resultado = mysqli_query($conexao, $sql);
$total_ingredientes = ($resultado) ? mysqli_num_rows($resultado) : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Ingredientes - Pizzaria da Chapada</title>
    
    <!-- Carregamento de estilos localizados na pasta style/ -->
    <link rel="stylesheet" href="style/base.css?v=10.0">
    <link rel="stylesheet" href="style/menu.css?v=10.0">
    <link rel="stylesheet" href="style/rodape.css?v=10.0">
    <link rel="stylesheet" href="style/componentes.css?v=10.0">
    <link rel="stylesheet" href="style/tabelas.css?v=10.0">
    <link rel="stylesheet" href="style/consultas.css?v=10.0">
    <link rel="stylesheet" href="style/ingrediente.css?v=10.0">
</head>
<body class="bg-pizzaria">

    <!-- Inclusão do Menu Superior Padronizado -->
    <?php include 'Menu1.php'; ?>

    <div class="main-wrapper">
        <div class="table-card-container">

            <!-- Cabeçalho com Logo e Contador -->
            <div class="table-header-flex">
                <div class="title-with-logo">
                    <img src="Ingredientes.png" alt="Logo Ingredientes" class="section-logo" onerror="this.style.display='none'">
                    <h2>Tabela de Ingredientes</h2>
                </div>
                <span class="badge-count">
                    <?= $total_ingredientes ?> ingrediente(s) cadastrado(s)
                </span>
            </div>

            <!-- Botão de Novo Cadastro -->
            <div class="table-actions-bar">
                <a href="form_cad_ingrediente.php" class="btn-dominos btn-primary">
                    + Novo Ingrediente
                </a>
            </div>

            <!-- Tabela de Dados -->
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Nome do Ingrediente</th>
                            <th>Quantidade</th>
                            <th>Unidade de Medida</th>
                            <th style="width: 160px; text-align: center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_ingredientes > 0): ?>
                            <?php while ($linha = mysqli_fetch_assoc($resultado)): ?>
                                <tr>
                                    <!-- ID -->
                                    <td>
                                        <strong>#<?= (int)$linha['id'] ?></strong>
                                    </td>

                                    <!-- Nome -->
                                    <td>
                                        <span class="category-name">
                                            <?= htmlspecialchars($linha['nome'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>

                                    <!-- Quantidade (Valor Inteiro Puro) -->
                                    <td>
                                        <strong style="color: #065f46;">
                                            <?= (int)$linha['quantidade_atual'] ?>
                                        </strong>
                                    </td>

                                    <!-- Unidade de Medida -->
                                    <td>
                                        <?= htmlspecialchars($linha['unidade_medida'], ENT_QUOTES, 'UTF-8') ?>
                                    </td>

                                    <!-- Coluna de Ações (Editar e Excluir) -->
                                    <td class="cell-actions" style="text-align: center;">
                                        <a href="form_edit_ingrediente.php?id=<?= (int)$linha['id'] ?>" 
                                           class="btn-action-edit">
                                            Editar
                                        </a>
                                        
                                        <a href="consultar_ingrediente.php?acao=excluir&id=<?= (int)$linha['id'] ?>" 
                                           class="btn-action-danger" 
                                           onclick="return confirm('Deseja realmente excluir o ingrediente <?= htmlspecialchars($linha['nome'], ENT_QUOTES, 'UTF-8') ?>?');">
                                            Excluir
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="empty-table-msg">
                                    Nenhum ingrediente cadastrado no momento.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Rodapé Padronizado -->
    <?php include 'rodape.php'; ?>

</body>
</html>