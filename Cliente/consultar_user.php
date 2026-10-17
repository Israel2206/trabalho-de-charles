<?php
include "conexao.php";

$sql = "SELECT id, nome, email, telefone, endereco, datacad, nivel_acesso FROM cliente ORDER BY id DESC";
$resultado = mysqli_query($conexao, $sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Tabela de Clientes - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'Menu1.php'; ?>

    <div class="container-large">
        <h1>Tabela de Clientes</h1>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <a href="form_cad_user.php" class="btn btn-novo">+ Novo Cliente</a>
            <span style="font-size: 14px; color: #6b7280;">Total: <?= mysqli_num_rows($resultado) ?> cliente(s)</span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Endereço</th>
                        <th>Cadastro</th>
                        <th>Nível</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($linha = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td><?= $linha['id'] ?></td>
                                <td><strong><?= htmlspecialchars($linha['nome']) ?></strong></td>
                                <td><?= htmlspecialchars($linha['email']) ?></td>
                                <td><?= htmlspecialchars($linha['telefone'] ?: '-') ?></td>
                                <td><?= htmlspecialchars($linha['endereco'] ?: '-') ?></td>
                                <td><?= date('d/m/Y', strtotime($linha['datacad'])) ?></td>
                                <td>
                                    <?php if ($linha['nivel_acesso'] == 2): ?>
                                        <span style="background-color: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 4px; font-size: 12px;">Admin</span>
                                    <?php else: ?>
                                        <span style="background-color: #e0f2fe; color: #075985; padding: 2px 6px; border-radius: 4px; font-size: 12px;">Cliente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="excluir.php?tabela=cliente&id=<?= $linha['id'] ?>" class="btn btn-danger" onclick="return confirm('Deseja realmente excluir este cliente?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 20px; color: #9ca3af;">
                                Nenhum cliente cadastrado no momento.
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
