<?php
include "conexao.php";

$sql = "SELECT id, nome, descricao FROM categoria ORDER BY id DESC";
$resultado = mysqli_query($conexao, $sql);
$total_cat = ($resultado) ? mysqli_num_rows($resultado) : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Categorias - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css?v=6.2">
    <style>
        /* Estilo para alinhar e ampliar a logo de categorias */
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
                    <img src="Categorias.png" alt="Logo Categorias" class="section-logo">
                    <h2>Tabela de Categorias</h2>
                </div>
                <span class="badge-count"><?= $total_cat ?> categoria(s) cadastrada(s)</span>
            </div>

            <div class="table-actions-bar" style="margin-top: 15px; margin-bottom: 30px;">
                <a href="form_cad_categoria.php" class="btn-dominos btn-primary">+ Nova Categoria</a>
            </div>

            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th style="width: 250px;">Nome</th>
                            <th>Descrição</th>
                            <th style="width: 120px; text-align: center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_cat > 0): ?>
                            <?php while ($linha = mysqli_fetch_assoc($resultado)): ?>
                                <tr>
                                    <td><strong>#<?= $linha['id'] ?></strong></td>
                                    <td><span class="category-name"><?= htmlspecialchars($linha['nome']) ?></span></td>
                                    <td><span class="category-desc"><?= htmlspecialchars($linha['descricao'] ?: 'Sem descrição') ?></span></td>
                                    <td style="text-align: center;">
                                        <a href="excluir.php?tabela=categoria&id=<?= $linha['id'] ?>" class="btn-action-danger" onclick="return confirm('Deseja realmente excluir esta categoria?');">Excluir</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="empty-table-msg">
                                    Nenhuma categoria cadastrada no momento.
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