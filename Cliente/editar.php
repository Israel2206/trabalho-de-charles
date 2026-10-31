<?php
include "conexao.php";

$tabela = isset($_GET['tabela']) ? $_GET['tabela'] : (isset($_POST['tabela']) ? $_POST['tabela'] : '');
$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);

$tabelas_validas = [
    'cliente'   => ['titulo' => 'Editar Cliente', 'retorno' => 'consultar_user.php'],
    'categoria' => ['titulo' => 'Editar Categoria', 'retorno' => 'consultar_categoria.php'],
    'produto'   => ['titulo' => 'Editar Produto', 'retorno' => 'consultar_produto.php'],
    'pedido'    => ['titulo' => 'Editar Pedido', 'retorno' => 'consultar_pedido.php']
];

if (!array_key_exists($tabela, $tabelas_validas) || $id <= 0) {
    die("Ação ou registro inválido.");
}

$info_tabela = $tabelas_validas[$tabela];
$mensagem = "";
$sucesso = false;

// Processa a atualização dos dados (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($tabela === 'categoria') {
            $nome = trim($_POST['nome'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            if (empty($nome)) throw new Exception("O nome da categoria é obrigatório.");

            $stmt = mysqli_prepare($conexao, "UPDATE categoria SET nome = ?, descricao = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $nome, $descricao, $id);
            mysqli_stmt_execute($stmt);

        } elseif ($tabela === 'cliente') {
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');
            $endereco = trim($_POST['endereco'] ?? '');
            $nivel = intval($_POST['nivel'] ?? 1);
            if (empty($nome)) throw new Exception("O nome do cliente é obrigatório.");

            $email_val = !empty($email) ? $email : null;
            $stmt = mysqli_prepare($conexao, "UPDATE cliente SET nome = ?, email = ?, telefone = ?, endereco = ?, nivel_acesso = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssssii", $nome, $email_val, $telefone, $endereco, $nivel, $id);
            mysqli_stmt_execute($stmt);

        } elseif ($tabela === 'produto') {
            $nome = trim($_POST['nome'] ?? '');
            $preco = floatval(str_replace(',', '.', $_POST['preco'] ?? 0));
            $estoque = intval($_POST['estoque'] ?? 0);
            $id_categoria = intval($_POST['id_categoria'] ?? 0);
            if (empty($nome) || $preco <= 0 || $id_categoria <= 0) {
                throw new Exception("Preencha todos os campos do produto corretamente.");
            }

            $stmt = mysqli_prepare($conexao, "UPDATE produto SET nome = ?, preco = ?, estoque = ?, id_categoria = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "sdiii", $nome, $preco, $estoque, $id_categoria, $id);
            mysqli_stmt_execute($stmt);

        } elseif ($tabela === 'pedido') {
            $status = trim($_POST['status'] ?? 'Pendente');
            $stmt = mysqli_prepare($conexao, "UPDATE pedido SET status = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $status, $id);
            mysqli_stmt_execute($stmt);
        }

        // Sincroniza imediatamente a alteração no BD.sql
        sincronizar_bd_sql($conexao);

        $sucesso = true;
        $mensagem = "Registro atualizado com sucesso no banco de dados e persistido no BD.sql!";

    } catch (Throwable $e) {
        $mensagem = "Erro ao atualizar: " . $e->getMessage();
    }
}

// Carrega os dados atuais do registro
$stmt_get = mysqli_prepare($conexao, "SELECT * FROM `$tabela` WHERE id = ?");
mysqli_stmt_bind_param($stmt_get, "i", $id);
mysqli_stmt_execute($stmt_get);
$res_reg = mysqli_stmt_get_result($stmt_get);
$registro = mysqli_fetch_assoc($res_reg);

if (!$registro && !$sucesso) {
    die("Registro não encontrado no banco de dados.");
}

// Se for edição de produto, carrega categorias
$categorias_lista = [];
if ($tabela === 'produto') {
    $res_c = mysqli_query($conexao, "SELECT id, nome FROM categoria ORDER BY nome ASC");
    while ($c = mysqli_fetch_assoc($res_c)) {
        $categorias_lista[] = $c;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($info_tabela['titulo']) ?> - Pizzaria da Chapada</title>
    <link rel="stylesheet" href="style.css?v=8.0">
</head>
<body class="bg-pizzaria">
    <?php include 'Menu1.php'; ?>

    <div class="split-container">
        <div class="split-art">
            <div class="art-text">
                <h2>Atualização de Dados</h2>
                <p>Todas as alterações salvas aqui são gravadas no banco e no BD.sql!</p>
            </div>
            <img src="Pizzaria-da-ChapadaLogin.png" alt="Pizzaria da Chapada Logo" class="art-image">
        </div>

        <div class="split-form-card">
            <h2><?= htmlspecialchars($info_tabela['titulo']) ?> #<?= $id ?></h2>

            <?php if (!empty($mensagem)): ?>
                <div style="background-color: <?= $sucesso ? '#d1fae5' : '#fee2e2' ?>; color: <?= $sucesso ? '#065f46' : '#991b1b' ?>; border: 1.5px solid <?= $sucesso ? '#a7f3d0' : '#fecaca' ?>; padding: 12px; border-radius: 10px; font-weight: 600; margin-bottom: 20px; font-size: 14px; text-align: center;">
                    <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form action="editar.php?tabela=<?= urlencode($tabela) ?>&id=<?= $id ?>" method="POST" class="modern-form">
                <input type="hidden" name="tabela" value="<?= htmlspecialchars($tabela) ?>">
                <input type="hidden" name="id" value="<?= $id ?>">

                <?php if ($tabela === 'categoria'): ?>
                    <div class="form-group">
                        <label>Nome da Categoria *</label>
                        <input type="text" name="nome" value="<?= htmlspecialchars($registro['nome'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="descricao" rows="3" style="width: 100%; padding: 12px 14px; border: 1.5px solid #fcd34d; border-radius: 10px; font-size: 14px; font-family: 'Poppins', sans-serif; background-color: #fffdf5; color: #1f2937; resize: none; box-sizing: border-box;"><?= htmlspecialchars($registro['descricao'] ?? '') ?></textarea>
                    </div>

                <?php elseif ($tabela === 'cliente'): ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nome Completo *</label>
                            <input type="text" name="nome" value="<?= htmlspecialchars($registro['nome'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>E-mail</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($registro['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Telefone</label>
                            <input type="text" name="telefone" value="<?= htmlspecialchars($registro['telefone'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Nível de Acesso</label>
                            <select name="nivel">
                                <option value="1" <?= ((int)($registro['nivel_acesso'] ?? 1) === 1) ? 'selected' : '' ?>>Cliente (Padrão)</option>
                                <option value="2" <?= ((int)($registro['nivel_acesso'] ?? 1) === 2) ? 'selected' : '' ?>>Administrador</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Endereço de Entrega</label>
                        <input type="text" name="endereco" value="<?= htmlspecialchars($registro['endereco'] ?? '') ?>">
                    </div>

                <?php elseif ($tabela === 'produto'): ?>
                    <div class="form-group">
                        <label>Nome do Produto / Pizza *</label>
                        <input type="text" name="nome" value="<?= htmlspecialchars($registro['nome'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Categoria *</label>
                        <select name="id_categoria" required>
                            <?php foreach ($categorias_lista as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ((int)$cat['id'] === (int)($registro['id_categoria'] ?? 0)) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Preço (R$) *</label>
                            <input type="number" name="preco" step="0.01" min="0" value="<?= htmlspecialchars($registro['preco'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Estoque *</label>
                            <input type="number" name="estoque" min="0" value="<?= htmlspecialchars($registro['estoque'] ?? '') ?>" required>
                        </div>
                    </div>

                <?php elseif ($tabela === 'pedido'): ?>
                    <div class="form-group">
                        <label>Status do Pedido *</label>
                        <select name="status" required>
                            <?php
                            $opcoes_status = ['Pendente', 'Em preparo', 'Saiu para entrega', 'Entregue'];
                            foreach ($opcoes_status as $st): ?>
                                <option value="<?= $st ?>" <?= (($registro['status'] ?? '') === $st) ? 'selected' : '' ?>><?= $st ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="form-buttons" style="margin-top: 20px;">
                    <button type="submit" class="btn-dominos btn-primary btn-full">Salvar Alterações</button>
                    <a href="<?= htmlspecialchars($info_tabela['retorno']) ?>" class="btn-dominos btn-outline btn-full" style="display: block; text-align: center; box-sizing: border-box;">Voltar para Tabela</a>
                </div>
            </form>
        </div>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>
