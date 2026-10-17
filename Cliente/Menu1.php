<style>
    .menu-ifba {
        background-color: #FBBF24;
        padding: 10px 20px;
        font-family: Arial, sans-serif;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px;
    }

    .menu-ifba .logo {
        color: #78350f;
        font-size: 20px;
        font-weight: bold;
        text-decoration: none;
        padding: 8px 15px;
        margin-right: 15px;
    }

    .menu-ifba .logo:hover {
        opacity: 0.85;
    }

    .menu-ifba .item-menu {
        position: relative;
    }

    .menu-ifba .item-menu > a {
        display: block;
        color: #78350f;
        font-weight: 600;
        text-decoration: none;
        padding: 10px 14px;
        font-size: 14px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }

    .menu-ifba .item-menu > a:hover {
        background-color: #f59e0b;
        color: #ffffff;
    }

    .menu-ifba .submenu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        min-width: 190px;
        background-color: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 6px;
        z-index: 9999;
        overflow: hidden;
    }

    .menu-ifba .submenu a {
        display: block;
        padding: 10px 15px;
        color: #374151;
        text-decoration: none;
        font-size: 14px;
        border-bottom: 1px solid #f3f4f6;
        transition: background-color 0.2s, color 0.2s;
    }

    .menu-ifba .submenu a:hover {
        background-color: #fef3c7;
        color: #b45309;
        font-weight: bold;
    }

    .menu-ifba .item-menu:hover .submenu,
    .menu-ifba .item-menu:focus-within .submenu {
        display: block;
    }

    @media (max-width: 600px) {
        .menu-ifba {
            display: block;
        }

        .menu-ifba .logo {
            display: block;
        }

        .menu-ifba .item-menu > a {
            padding: 10px 15px;
        }

        .menu-ifba .submenu {
            position: static;
            box-shadow: none;
            margin-left: 15px;
        }
    }
</style>

<nav class="menu-ifba">
    <a href="index.php" class="logo">🍕 Pizzaria da Chapada</a>

    <div class="item-menu">
        <a href="#">Cadastro &#9662;</a>
        <div class="submenu">
            <a href="form_cad_user.php">Cadastrar Cliente</a>
            <a href="form_cad_categoria.php">Cadastrar Categoria</a>
            <a href="form_cad_produto.php">Cadastrar Produto</a>
            <a href="form_cad_pedido.php">Cadastrar Pedido</a>
        </div>
    </div>

    <div class="item-menu">
        <a href="#">Consultar Tabelas &#9662;</a>
        <div class="submenu">
            <a href="consultar_user.php">Tabela Clientes</a>
            <a href="consultar_categoria.php">Tabela Categorias</a>
            <a href="consultar_produto.php">Tabela Produtos</a>
            <a href="consultar_pedido.php">Tabela Pedidos</a>
        </div>
    </div>
</nav>