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
$total_ped = ($resultado) ? mysqli_num_rows($resultado) : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Pedidos - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css?v=8.0">
    <style>
        /* Estilo para alinhar e exibir com destaque a logo de pedidos */
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
                    <img src="Pedidos.png" alt="Logo Pedidos" class="section-logo">
                    <h2>Tabela de Pedidos</h2>
                </div>
                <span class="badge-count"><?= $total_ped ?> registro(s) cadastrado(s)</span>
            </div>

            <div class="table-actions-bar" style="margin-top: 15px; margin-bottom: 30px;">
                <a href="form_cad_pedido.php" class="btn-dominos btn-primary">+ Novo Pedido</a>
            </div>

            <div class="table-responsive">
                <table class="modern-table">
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
                            <th style="width: 120px; text-align: center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_ped > 0): ?>
                            <?php while ($linha = mysqli_fetch_assoc($resultado)): ?>
                                <tr>
                                    <td><strong>#<?= $linha['id'] ?></strong></td>
                                    <td><span class="category-name"><?= htmlspecialchars($linha['cliente_nome']) ?></span></td>
                                    <td><span class="category-desc"><?= htmlspecialchars($linha['produto_nome'] ?: 'Item avulso') ?></span></td>
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
                                        <span style="background-color: <?= $bg ?>; color: <?= $color ?>; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block;">
                                            <?= htmlspecialchars($status) ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="excluir.php?tabela=pedido&id=<?= $linha['id'] ?>" class="btn-action-danger" onclick="return confirm('Deseja realmente cancelar/excluir este pedido?');">Excluir</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="empty-table-msg">
                                    Nenhum pedido registrado no momento.
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