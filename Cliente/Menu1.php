<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style/menu.css?v=11.0">

<nav class="menu-ifba">
    <div class="logo-container">
        <a href="index.php">
            <img src="logoCircularPizzariaChapada.png" alt="Logo Pizzaria da Chapada" class="logo-img">
        </a>
    </div>

    <div class="nav-links">
        <div class="item-menu">
            <a href="#">Cadastro &#9662;</a>
            <div class="submenu">
                <a href="form_cad_user.php">Cadastrar Cliente</a>
                <a href="form_cad_ingrediente.php">Cadastrar Ingrediente</a>
                <a href="form_cad_categoria.php">Cadastrar Categoria</a>
                <a href="form_cad_produto.php">Cadastrar Produto</a>
                <a href="form_cad_pedido.php">Cadastrar Pedido</a>
            </div>
        </div>

        <div class="item-menu">
            <a href="#">Consultar Tabelas &#9662;</a>
            <div class="submenu">
                <a href="consultar_user.php">Tabela Clientes</a>
                <a href="consultar_ingrediente.php">Tabela Ingrediente</a>
                <a href="consultar_categoria.php">Tabela Categorias</a>
                <a href="consultar_produto.php">Tabela Produtos</a>
                <a href="consultar_pedido.php">Tabela Pedidos</a>
            </div>
        </div>

        <!-- Botões de Cadastro/Login ou Vitrine/Sair -->
        <div class="auth-links">
            <a href="index1.php" class="btn-menu-cadastro">Vitrine</a>
            <?php if (!empty($_SESSION["usuario_nome"])): ?>
                <a href="logout.php" class="btn-menu-login">Sair</a>
            <?php else: ?>
                <a href="login.php" class="btn-menu-login">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>