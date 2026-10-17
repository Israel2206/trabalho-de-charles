<?php
include "conexao.php";

$sql = "SELECT p.id, p.data_pedido, p.status, p.valor_total, c.nome AS cliente_nome,
               pr.nome AS produto_nome, ip.quantidade, ip.preco_unitario
        FROM pedido p
        INNER JOIN cliente c ON p.id_cliente = c.id
        LEFT JOIN item_pedido ip ON p.id = ip.id_pedido
        LEFT JOIN produto pr ON ip.id_produto = pr.id
        ORDER BY p.id DESC";

$resultado = mysqli_query($conexao, $sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Tabela de Pedidos - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'Menu1.php'; ?>

    <div class="container-large">
        <h1>Tabela de Pedidos</h1>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <a href="form_cad_pedido.php" class="btn btn-novo">+ Novo Pedido</a>
            <span style="font-size: 14px; color: #6b7280;">Total: <?= mysqli_num_rows($resultado) ?> registro(s)</span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 70px;">Pedido</th>
                        <th>Cliente</th>
                        <th>Item / Pizza</th>
                        <th style="width: 70px;">Qtd</th>
                        <th>Preço Unit.</th>
                        <th>Valor Total</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th style="width: 90px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($linha = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td><strong>#<?= $linha['id'] ?></strong></td>
                                <td><?= htmlspecialchars($linha['cliente_nome']) ?></td>
                                <td><?= htmlspecialchars($linha['produto_nome'] ?: 'Item avulso') ?></td>
                                <td><?= $linha['quantidade'] ?: 1 ?></td>
                                <td>R$ <?= number_format($linha['preco_unitario'] ?: 0, 2, ',', '.') ?></td>
                                <td><strong style="color: #d97706;">R$ <?= number_format($linha['valor_total'], 2, ',', '.') ?></strong></td>
                                <td><?= date('d/m/Y', strtotime($linha['data_pedido'])) ?></td>
                                <td>
                                    <?php
                                    $status = $linha['status'];
                                    $bg = '#e5e7eb'; $color = '#374151';
                                    if ($status == 'Pendente') { $bg = '#fef3c7'; $color = '#92400e'; }
                                    elseif ($status == 'Em preparo') { $bg = '#dbeafe'; $color = '#1e40af'; }
                                    elseif ($status == 'Saiu para entrega') { $bg = '#e0e7ff'; $color = '#3730a3'; }
                                    elseif ($status == 'Entregue') { $bg = '#d1fae5'; $color = '#065f46'; }
                                    ?>
                                    <span style="background-color: <?= $bg ?>; color: <?= $color ?>; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                                        <?= htmlspecialchars($status) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="excluir.php?tabela=pedido&id=<?= $linha['id'] ?>" class="btn btn-danger" onclick="return confirm('Deseja realmente cancelar/excluir este pedido?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 20px; color: #9ca3af;">
                                Nenhum pedido registrado no momento.
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
