<?php
include "conexao.php";

// Captura os filtros e a busca enviados via GET
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'todos';
$busca = isset($_GET['busca']) ? mysqli_real_escape_string($conexao, trim($_GET['busca'])) : '';

// Consulta base com os JOINs corretos do seu banco
$sql = "SELECT p.id, p.data_pedido, p.status, p.valor_total, c.nome AS cliente_nome,
               pr.nome AS produto_nome, ip.quantidade, ip.preco_unitario
        FROM pedido p
        INNER JOIN cliente c ON p.id_cliente = c.id
        LEFT JOIN item_pedido ip ON p.id = ip.id_pedido
        LEFT JOIN produto pr ON ip.id_produto = pr.id
        WHERE 1=1";

// Lógica estrita para o botão "Mais Vendidos" (apenas status Entregue)
if ($filtro === 'mais_vendidos') {
    $sql .= " AND p.status = 'Entregue'";
}

// Lógica da barra de pesquisa com suporte a data no formato 15/10/2026
if (!empty($busca)) {
    $busca_sql = $busca;
    // Se o usuário digitar no formato DD/MM/AAAA, converte para AAAA-MM-DD para buscar no banco
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $busca, $matches)) {
        $busca_sql = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
    }

    $sql .= " AND (c.nome LIKE '%$busca%' OR pr.nome LIKE '%$busca%' OR p.data_pedido LIKE '%$busca_sql%' OR p.valor_total LIKE '%$busca%')";
}

// Ordenação dos registros
if ($filtro === 'mais_vendidos') {
    // Ordena pelas maiores quantidades descrescentes e ID decrescente
    $sql .= " ORDER BY ip.quantidade DESC, p.id DESC";
} else {
    // Ordem padrão do mais recente para o mais antigo
    $sql .= " ORDER BY p.id DESC";
}

$resultado = mysqli_query($conexao, $sql);
$total_ped = ($resultado) ? mysqli_num_rows($resultado) : 0;

// Cálculo das Médias / Ganhos Semanais e Mensais (caso o botão "Media" seja acionado)
$ganho_semanal = 0;
$ganho_mensal = 0;

if ($filtro === 'media') {
    // Ganho dos últimos 7 dias (baseado no calendário) para pedidos Entregues
    $sql_semana = "SELECT SUM(valor_total) as total FROM pedido WHERE status = 'Entregue' AND data_pedido >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    $res_semana = mysqli_query($conexao, $sql_semana);
    if ($res_semana) {
        $row_semana = mysqli_fetch_assoc($res_semana);
        $ganho_semanal = $row_semana['total'] ?? 0;
    }

    // Ganho do mês atual para pedidos Entregues
    $sql_mes = "SELECT SUM(valor_total) as total FROM pedido WHERE status = 'Entregue' AND MONTH(data_pedido) = MONTH(CURDATE()) AND YEAR(data_pedido) = YEAR(CURDATE())";
    $res_mes = mysqli_query($conexao, $sql_mes);
    if ($res_mes) {
        $row_mes = mysqli_fetch_assoc($res_mes);
        $ganho_mensal = $row_mes['total'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Pedidos - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css?v=8.2">
    <style>
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
        /* Estilo dos Botões de Filtro em Pílula */
        .filter-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn-pill {
            background-color: #f3f4f6;
            color: #4b5563;
            border: 1px solid #d1d5db;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-pill:hover {
            background-color: #e5e7eb;
        }
        .btn-pill.active {
            background-color: #d97706;
            color: #ffffff;
            border-color: #b45309;
        }
        /* Barra de Pesquisa */
        .search-box {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .search-input {
            padding: 8px 14px;
            border: 1px solid #d1d5db;
            border-radius: 20px;
            font-size: 13px;
            outline: none;
            width: 230px;
        }
        .search-btn {
            background-color: #78350f;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        /* Card de Relatório de Média */
        .media-card-box {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 1.5px solid #f59e0b;
            padding: 25px;
            border-radius: 16px;
            text-align: center;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(120, 53, 15, 0.1);
        }
    </style>
</head>
<body class="bg-pizzaria">
    <?php include 'Menu1.php'; ?>

    <div class="main-wrapper">
        <div class="table-card-container">
            
            <!-- Cabeçalho -->
            <div class="table-header-flex" style="margin-bottom: 25px;">
                <div class="title-with-logo">
                    <img src="Pedidos.png" alt="Logo Pedidos" class="section-logo">
                    <h2>Tabela de Pedidos</h2>
                </div>
                <span class="badge-count"><?= $total_ped ?> registro(s) cadastrado(s)</span>
            </div>

            <!-- Barra de Ações, Filtros, Botão Média e Pesquisa -->
            <div class="table-actions-bar" style="margin-top: 15px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <a href="form_cad_pedido.php" class="btn-dominos btn-primary">+ Novo Pedido</a>
                
                <!-- Botões de Filtro -->
                <div class="filter-pills">
                    <a href="consultar_pedido.php" class="btn-pill <?= ($filtro === 'todos') ? 'active' : '' ?>">Todos</a>
                    <a href="?filtro=mais_vendidos" class="btn-pill <?= ($filtro === 'mais_vendidos') ? 'active' : '' ?>"> Mais Vendidos</a>
                    <a href="?filtro=media" class="btn-pill <?= ($filtro === 'media') ? 'active' : '' ?>"> Média</a>
                </div>

                <!-- Barra de Pesquisa -->
                <form method="GET" action="consultar_pedido.php" class="search-box">
                    <input type="hidden" name="filtro" value="<?= htmlspecialchars($filtro) ?>">
                    <input type="text" name="busca" class="search-input" placeholder="Pesquisar cliente, pizza, data..." value="<?= htmlspecialchars($busca) ?>">
                    <button type="submit" class="search-btn">Buscar</button>
                </form>
            </div>

            <!-- EXIBIÇÃO DO PAINEL DE MÉDIA SE O BOTÃO FOR CLICADO -->
            <?php if ($filtro === 'media'): ?>
                <div class="media-card-box">
                    <h3 style="color: #78350f; margin-bottom: 8px; font-size: 19px;">Relatório de Faturamento e Ganhos (Entregues)</h3>
                    <p style="color: #92400e; font-size: 14px; margin-bottom: 15px;">Valores calculados com base nos registros do calendário do sistema.</p>
                    <div style="display: flex; justify-content: center; gap: 30px; flex-wrap: wrap;">
                        <div style="background: #fff; padding: 15px 25px; border-radius: 12px; border: 1.5px solid #fcd34d;">
                            <span style="display: block; font-size: 12px; color: #78350f; font-weight: 700; text-transform: uppercase;">Ganho Última Semana</span>
                            <strong style="font-size: 22px; color: #065f46;">R$ <?= number_format($ganho_semanal, 2, ',', '.') ?></strong>
                        </div>
                        <div style="background: #fff; padding: 15px 25px; border-radius: 12px; border: 1.5px solid #fcd34d;">
                            <span style="display: block; font-size: 12px; color: #78350f; font-weight: 700; text-transform: uppercase;">Ganho Mensal Atual</span>
                            <strong style="font-size: 22px; color: #065f46;">R$ <?= number_format($ganho_mensal, 2, ',', '.') ?></strong>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tabela de Pedidos -->
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
                                    <td><strong>#<?= (int)$linha['id'] ?></strong></td>
                                    <td><span class="category-name"><?= htmlspecialchars($linha['cliente_nome']) ?></span></td>
                                    <td><span class="category-desc"><?= htmlspecialchars($linha['produto_nome'] ?: 'Item avulso') ?></span></td>
                                    <td><?= (int)($linha['quantidade'] ?: 1) ?></td>
                                    <td>R$ <?= number_format($linha['preco_unitario'] ?: 0, 2, ',', '.') ?></td>
                                    <td><strong style="color: #d97706;">R$ <?= number_format($linha['valor_total'], 2, ',', '.') ?></strong></td>
                                    <td>
                                        <?php 
                                        if (!empty($linha['data_pedido'])) {
                                            $timestamp = strtotime($linha['data_pedido']);
                                            echo $timestamp !== false ? date('d/m/Y', $timestamp) : htmlspecialchars($linha['data_pedido']);
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
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
                                    <td style="text-align: center; white-space: nowrap;">
                                        <a href="editar.php?tabela=pedido&id=<?= (int)$linha['id'] ?>" class="btn-action-edit">Editar</a>
                                        <a href="excluir.php?tabela=pedido&id=<?= (int)$linha['id'] ?>" class="btn-action-danger" onclick="return confirm('Deseja realmente cancelar/excluir este pedido?');">Excluir</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="empty-table-msg">
                                    Nenhum pedido encontrado para esta busca ou filtro.
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