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
            
            <!-- Cabeçalho -->
            <div class="table-header-flex">
                <div class="title-with-logo">
                    <img src="Pedidos.png" alt="Logo Pedidos" class="section-logo">
                    <h2>Tabela de Pedidos</h2>
                </div>
                <span class="badge-count"><?= $total_ped ?> registro(s) cadastrado(s)</span>
            </div>

            <!-- Barra de Ações, Filtros, Botão Média e Pesquisa -->
            <div class="table-actions-bar table-actions-bar--toolbar">
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
                    <h3>Relatório de Faturamento e Ganhos (Entregues)</h3>
                    <p>Valores calculados com base nos registros do calendário do sistema.</p>
                    <div class="media-stats">
                        <div class="media-stat">
                            <span class="media-stat-label">Ganho Última Semana</span>
                            <strong class="media-stat-value">R$ <?= number_format($ganho_semanal, 2, ',', '.') ?></strong>
                        </div>
                        <div class="media-stat">
                            <span class="media-stat-label">Ganho Mensal Atual</span>
                            <strong class="media-stat-value">R$ <?= number_format($ganho_mensal, 2, ',', '.') ?></strong>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tabela de Pedidos -->
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th class="col-id">Pedido</th>
                            <th>Cliente</th>
                            <th>Item / Pizza</th>
                            <th class="col-qtd">Qtd</th>
                            <th>Preço Unit.</th>
                            <th>Valor Total</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th class="col-acoes">Ações</th>
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
                                    <td><strong class="valor-total">R$ <?= number_format($linha['valor_total'], 2, ',', '.') ?></strong></td>
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
                                        $status_classes = [
                                            'Pendente' => 'status-pendente',
                                            'Em preparo' => 'status-preparo',
                                            'Saiu para entrega' => 'status-entrega',
                                            'Entregue' => 'status-entregue',
                                        ];
                                        $status_classe = $status_classes[$status] ?? '';
                                        ?>
                                        <span class="status-badge <?= $status_classe ?>">
                                            <?= htmlspecialchars($status) ?>
                                        </span>
                                    </td>
                                    <td class="cell-actions">
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