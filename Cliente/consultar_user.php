<?php
include "conexao.php";

// Consulta os clientes do mais recente para o mais antigo
$sql = "SELECT id, nome, email, telefone, endereco, datacad, nivel_acesso
        FROM cliente
        ORDER BY id DESC";

$resultado = mysqli_query($conexao, $sql);
$total_clientes = ($resultado) ? mysqli_num_rows($resultado) : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tabela de Clientes - Pizzaria da Chapada</title>

    <link rel="stylesheet" href="style.css?v=7.0">

    <style>
        /* Cabeçalho com logo */
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

        /* Identificação do nível de acesso */
        .access-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .access-admin {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .access-client {
            background-color: #e0f2fe;
            color: #075985;
            border: 1px solid #bae6fd;
        }

        /* Evita que os dados longos quebrem o layout */
        .client-email,
        .client-address {
            overflow-wrap: anywhere;
        }

        .client-phone {
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .title-with-logo {
                gap: 10px;
            }

            .section-logo {
                width: 55px;
                height: 55px;
            }
        }
    </style>
</head>

<body class="bg-pizzaria">

    <?php include 'Menu1.php'; ?>

    <div class="main-wrapper">
        <div class="table-card-container">

            <!-- Cabeçalho da tabela -->
            <div class="table-header-flex" style="margin-bottom: 25px;">

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
                    cliente(s) cadastrado(s)
                </span>

            </div>

            <!-- Botão de cadastro -->
            <div
                class="table-actions-bar"
                style="margin-top: 15px; margin-bottom: 30px;"
            >
                <a
                    href="form_cad_user.php"
                    class="btn-dominos btn-primary"
                >
                    + Novo Cliente
                </a>
            </div>

            <!-- Tabela de clientes -->
            <div class="table-responsive">
                <table class="modern-table">

                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>Endereço</th>
                            <th>Cadastro</th>
                            <th>Nível de acesso</th>
                            <th style="width: 120px; text-align: center;">
                                Ações
                            </th>
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
                                    <td style="text-align: center;">
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
                                    Nenhum cliente cadastrado no momento.
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