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
    <link rel="stylesheet" href="style.css?v=7.0">
    <style>
        /* Estilo para alinhar e exibir com destaque a logo de produtos */
        .title-with-logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .section-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            border-radius: 50%;
            background: #fff;
            padding: 3px;
            border: 2.5px solid #fcd34d;
            box-shadow: 0 4px 12px rgba(120, 53, 15, 0.2);
        }
    </style>
</head>
<body class="bg-pizzaria">
    <?php include 'Menu1.php'; ?>

    <div class="main-wrapper">
        <div class="table-card-container">
            <div class="table-header-flex" style="margin-bottom: 25px;">
                <div class="title-with-logo">
                    <img src="Produtos.png" alt="Logo Produtos" class="section-logo">
                    <h2>Tabela de Produtos</h2>
                </div>
                <span class="badge-count"><?= $total_prod ?> produto(s) cadastrado(s)</span>
            </div>

            <div class="table-actions-bar" style="margin-top: 15px; margin-bottom: 30px;">
                <a href="form_cad_produto.php" class="btn-dominos btn-primary">+ Novo Produto</a>
            </div>

            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Produto / Pizza</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                            <th>Estoque</th>
                            <th style="width: 120px; text-align: center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_prod > 0): ?>
                            <?php while ($linha = mysqli_fetch_assoc($resultado)): ?>
                                <tr>
                                    <td><strong>#<?= $linha['id'] ?></strong></td>
                                    <td><span class="category-name"><?= htmlspecialchars($linha['nome']) ?></span></td>
                                    <td>
                                        <span style="background-color: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; border: 1px solid #fde68a;">
                                            <?= htmlspecialchars($linha['categoria_nome'] ?: 'Sem categoria') ?>
                                        </span>
                                    </td>
                                    <td><strong>R$ <?= number_format($linha['preco'], 2, ',', '.') ?></strong></td>
                                    <td>
                                        <?php if ($linha['estoque'] > 0): ?>
                                            <span style="color: #059669; font-weight: 600;"><?= $linha['estoque'] ?> un.</span>
                                        <?php else: ?>
                                            <span style="color: #dc2626; font-weight: 600;">Esgotado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
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