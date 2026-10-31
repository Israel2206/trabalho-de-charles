<?php
include "conexao.php";

// Consulta os produtos trazendo o nome da categoria associada via LEFT JOIN
$sql = "SELECT p.id, p.nome, p.preco, p.estoque, c.nome AS categoria_nome 
        FROM produto p 
        LEFT JOIN categoria c ON p.id_categoria = c.id 
        ORDER BY p.id DESC";
$resultado = mysqli_query($conexao, $sql);
$total_prod = ($resultado) ? mysqli_num_rows($resultado) : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Produtos - Pizzaria da Chapada</title>
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
                    <img src="Produtos.png" alt="Logo Produtos" class="section-logo">
                    <h2>Tabela de Produtos</h2>
                </div>
                <span class="badge-count"><?= $total_prod ?> produto(s) cadastrado(s)</span>
            </div>

            <div class="table-actions-bar">
                <a href="form_cad_produto.php" class="btn-dominos btn-primary">+ Novo Produto</a>
            </div>

            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th class="col-id">ID</th>
                            <th>Produto / Pizza</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                            <th>Estoque</th>
                            <th class="col-acoes">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_prod > 0): ?>
                            <?php while ($linha = mysqli_fetch_assoc($resultado)): ?>
                                <tr>
                                    <td><strong>#<?= $linha['id'] ?></strong></td>
                                    <td><span class="category-name"><?= htmlspecialchars($linha['nome']) ?></span></td>
                                    <td>
                                        <span class="badge-categoria">
                                            <?= htmlspecialchars($linha['categoria_nome'] ?: 'Sem categoria') ?>
                                        </span>
                                    </td>
                                    <td><strong>R$ <?= number_format($linha['preco'], 2, ',', '.') ?></strong></td>
                                    <td>
                                        <?php if ($linha['estoque'] > 0): ?>
                                            <span class="estoque-ok"><?= $linha['estoque'] ?> un.</span>
                                        <?php else: ?>
                                            <span class="estoque-esgotado">Esgotado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="cell-actions">
                                        <a href="editar.php?tabela=produto&id=<?= $linha['id'] ?>" class="btn-action-edit">Editar</a>
                                        <a href="excluir.php?tabela=produto&id=<?= $linha['id'] ?>" class="btn-action-danger" onclick="return confirm('Deseja realmente excluir este produto?');">Excluir</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="empty-table-msg">
                                    Nenhum produto cadastrado no momento.
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