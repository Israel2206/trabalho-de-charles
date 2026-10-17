<?php
include "conexao.php";

$sql = "SELECT id, nome, descricao FROM categoria ORDER BY id DESC";
$resultado = mysqli_query($conexao, $sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Tabela de Categorias - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'Menu1.php'; ?>

    <div class="container-large">
        <h1>Tabela de Categorias</h1>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <a href="form_cad_categoria.php" class="btn btn-novo">+ Nova Categoria</a>
            <span style="font-size: 14px; color: #6b7280;">Total: <?= mysqli_num_rows($resultado) ?> categoria(s)</span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th style="width: 250px;">Nome</th>
                        <th>Descrição</th>
                        <th style="width: 100px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($linha = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td><?= $linha['id'] ?></td>
                                <td><strong><?= htmlspecialchars($linha['nome']) ?></strong></td>
                                <td><?= htmlspecialchars($linha['descricao'] ?: 'Sem descrição') ?></td>
                                <td>
                                    <a href="excluir.php?tabela=categoria&id=<?= $linha['id'] ?>" class="btn btn-danger" onclick="return confirm('Deseja realmente excluir esta categoria?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px; color: #9ca3af;">
                                Nenhuma categoria cadastrada no momento.
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
