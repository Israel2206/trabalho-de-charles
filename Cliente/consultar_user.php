<?php
include "conexao.php";

// Captura o filtro selecionado via GET ('cliente' ou 'admin'). Se não houver, exibe todos por padrão.
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : '';

// Monta a consulta SQL dinamicamente conforme o botão pressionado
if ($filtro === 'cliente') {
    // Filtra apenas clientes (nível de acesso diferente de 2)
    $sql = "SELECT id, nome, email, telefone, endereco, datacad, nivel_acesso
            FROM cliente 
            WHERE nivel_acesso != 2 
            ORDER BY id DESC";
} elseif ($filtro === 'admin') {
    // Filtra apenas administradores (nível de acesso igual a 2)
    $sql = "SELECT id, nome, email, telefone, endereco, datacad, nivel_acesso
            FROM cliente 
            WHERE nivel_acesso = 2 
            ORDER BY id DESC";
} else {
    // Exibe todos os registros caso nenhum filtro específico esteja ativo
    $sql = "SELECT id, nome, email, telefone, endereco, datacad, nivel_acesso
            FROM cliente
            ORDER BY id DESC";
}

$resultado = mysqli_query($conexao, $sql);
$total_clientes = ($resultado) ? mysqli_num_rows($resultado) : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tabela de Clientes - Pizzaria da Chapada</title>

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

            <!-- Cabeçalho da tabela -->
            <div class="table-header-flex">

                <div class="title-with-logo">
                    <img
                        src="LogoClientes.png"
                        alt="Logo Clientes"
                        class="section-logo"
                    >

                    <h2>Tabela de Clientes</h2>
                </div>

                <span class="badge-count">
                    <?= $total_clientes ?>
                    registro(s) encontrado(s)
                </span>

            </div>

            <!-- Barra de Ações e os Dois Botões de Filtro -->
            <div class="table-actions-bar table-actions-bar--toolbar">
                <a
                    href="form_cad_user.php"
                    class="btn-dominos btn-primary"
                >
                    + Novo Cliente
                </a>

                <!-- Os 2 botões solicitados (Cliente e Administrador) -->
                <div class="filter-pills">
                    <a href="consultar_user.php" class="btn-pill <?= ($filtro === '') ? 'active' : '' ?>">Ver Todos</a>
                    <a href="?filtro=cliente" class="btn-pill <?= ($filtro === 'cliente') ? 'active' : '' ?>">Cliente</a>
                    <a href="?filtro=admin" class="btn-pill <?= ($filtro === 'admin') ? 'active' : '' ?>">Administrador</a>
                </div>
            </div>

            <!-- Tabela de clientes -->
            <div class="table-responsive">
                <table class="modern-table">

                    <thead>
                        <tr>
                            <th class="col-id">ID</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>Endereço</th>
                            <th>Cadastro</th>
                            <th>Nível de acesso</th>
                            <th class="col-acoes">Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if ($total_clientes > 0): ?>

                            <?php while ($linha = mysqli_fetch_assoc($resultado)): ?>

                                <tr>
                                    <!-- ID -->
                                    <td>
                                        <strong>#<?= (int) $linha['id'] ?></strong>
                                    </td>

                                    <!-- Nome -->
                                    <td>
                                        <span class="category-name">
                                            <?= htmlspecialchars(
                                                $linha['nome'] ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>
                                    </td>

                                    <!-- E-mail -->
                                    <td class="client-email">
                                        <?= htmlspecialchars(
                                            $linha['email'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <!-- Telefone -->
                                    <td class="client-phone">
                                        <?= htmlspecialchars(
                                            $linha['telefone'] ?: '-',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <!-- Endereço -->
                                    <td class="client-address">
                                        <?= htmlspecialchars(
                                            $linha['endereco'] ?: '-',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <!-- Data de cadastro -->
                                    <td>
                                        <?php
                                        if (!empty($linha['datacad'])) {
                                            $timestamp = strtotime($linha['datacad']);

                                            echo $timestamp !== false
                                                ? date('d/m/Y', $timestamp)
                                                : '-';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>

                                    <!-- Nível de acesso -->
                                    <td>
                                        <?php if ((int) $linha['nivel_acesso'] === 2): ?>

                                            <span class="access-badge access-admin">
                                                Admin
                                            </span>

                                        <?php else: ?>

                                            <span class="access-badge access-client">
                                                Cliente
                                            </span>

                                        <?php endif; ?>
                                    </td>

                                    <!-- Ações -->
                                    <td class="cell-actions">
                                        <a href="editar.php?tabela=cliente&id=<?= (int) $linha['id'] ?>" class="btn-action-edit">Editar</a>
                                        <a
                                            href="excluir.php?tabela=cliente&id=<?= (int) $linha['id'] ?>"
                                            class="btn-action-danger"
                                            onclick="return confirm('Deseja realmente excluir este cliente?');"
                                        >
                                            Excluir
                                        </a>
                                    </td>
                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="8" class="empty-table-msg">
                                    Nenhum registro encontrado para esta categoria.
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