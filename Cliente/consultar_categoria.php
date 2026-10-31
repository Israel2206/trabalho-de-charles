<?php
include "conexao.php";

// Consulta todas as categorias cadastradas em ordem decrescente de cadastro
$sql = "SELECT id, nome, descricao FROM categoria ORDER BY id DESC";
$resultado = mysqli_query($conexao, $sql);
$total_cat = ($resultado) ? mysqli_num_rows($resultado) : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Categorias - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style/base.css?v=10.0">
    <link rel="stylesheet" href="style/menu.css?v=10.0">
    <link rel="stylesheet" href="style/rodape.css?v=10.0">
    <link rel="stylesheet" href="style/componentes.css?v=10.0">
    <link rel="stylesheet" href="style/tabelas.css?v=10.0">
    <link rel="stylesheet" href="style/consultas.css?v=10.0">
</head>
<body class="bg-pizzaria">
    <?php include 'Menu1.php'; ?>

    <div class="main-wrapper">
        <div class="table-card-container">
            <div class="table-header-flex">
                <div class="title-with-logo">
                    <img src="Categorias.png" alt="Logo Categorias" class="section-logo">
                    <h2>Tabela de Categorias</h2>
                </div>
                <span class="badge-count"><?= $total_cat ?> categoria(s) cadastrada(s)</span>
            </div>

            <div class="table-actions-bar">
                <a href="form_cad_categoria.php" class="btn-dominos btn-primary">+ Nova Categoria</a>
            </div>

            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th class="col-id-wide">ID</th>
                            <th class="col-nome">Nome</th>
                            <th>Descrição</th>
                            <th class="col-acoes">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_cat > 0): ?>
                            <?php while ($linha = mysqli_fetch_assoc($resultado)): ?>
                                <tr>
                                    <td><strong>#<?= $linha['id'] ?></strong></td>
                                    <td><span class="category-name"><?= htmlspecialchars($linha['nome']) ?></span></td>
                                    <td><span class="category-desc"><?= htmlspecialchars($linha['descricao'] ?: 'Sem descrição') ?></span></td>
                                    <td class="cell-center">
                                        <a href="editar.php?tabela=categoria&id=<?= $linha['id'] ?>" class="btn-action-edit">Editar</a>
                                        <a href="excluir.php?tabela=categoria&id=<?= $linha['id'] ?>" class="btn-action-danger" onclick="return confirm('Deseja realmente excluir esta categoria?');">Excluir</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="empty-table-msg">
                                    Nenhuma categoria cadastrada no momento.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>