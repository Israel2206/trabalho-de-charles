<?php
include "conexao.php";

// Consulta os produtos trazendo o nome da categoria associada via LEFT JOIN
$sql = "SELECT p.id, p.nome, p.preco, p.estoque, c.nome AS categoria_nome 
        FROM produto p 
        LEFT JOIN categoria c ON p.id_categoria = c.id 
        ORDER BY p.id DESC";
$resultado = mysqli_query($conexao, $sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Tabela de Produtos - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'Menu1.php'; ?>

    <div class="container-large">
        <h1>Tabela de Produtos</h1>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <a href="form_cad_produto.php" class="btn btn-novo">+ Novo Produto</a>
            <span style="font-size: 14px; color: #6b7280;">Total: <?= mysqli_num_rows($resultado) ?> produto(s)</span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Produto / Pizza</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th style="width: 100px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($linha = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td><?= $linha['id'] ?></td>
                                <td><strong><?= htmlspecialchars($linha['nome']) ?></strong></td>
                                <td>
                                    <span style="background-color: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
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
                                <td>
                                    <a href="excluir.php?tabela=produto&id=<?= $linha['id'] ?>" class="btn btn-danger" onclick="return confirm('Deseja realmente excluir este produto?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px; color: #9ca3af;">
                                Nenhum produto cadastrado no momento.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>
