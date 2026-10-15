<style>
    .menu-ifba {
        background-color: #FBBF24;
        padding: 10px 15px;
        font-family: Arial, sans-serif;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px;
    }

    .menu-ifba .logo {
        color: white;
        font-size: 18px;
        font-weight: bold;
        text-decoration: none;
        padding: 10px 15px;
        margin-right: 10px;
    }

    .menu-ifba .item-menu {
        position: relative;
    }

    .menu-ifba .item-menu > a {
        display: block;
        color: white;
        text-decoration: none;
        padding: 12px 14px;
        font-size: 14px;
        border-radius: 4px;
    }

    .menu-ifba .item-menu > a:hover {
        background-color: #12370c;
    }

    .menu-ifba .submenu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        min-width: 170px;
        background-color: white;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        border-radius: 4px;
        z-index: 9999;
    }

    .menu-ifba .submenu a {
        display: block;
        padding: 11px 15px;
        color: #333;
        text-decoration: none;
        font-size: 14px;
    }

    .menu-ifba .submenu a:hover {
        background-color: #f5e5e6;
        color:  #006633;
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
    <a href="index.php" class="logo">Pizzaria da Chapada</a>

    <div class="item-menu">
        <a href="#">Cadastro &#9662;</a>
        <div class="submenu">
            <a href="form_cad_user.php">Cadratro cliente</a>
            <a href="form_cad_pedido">Cadastro de Pedido</a>
            <a href="form_cad_endereco.php">Cadastro Endereço</a>
            <a href="form_cad_produto.php">Cad Produto</a>
        </div>
    </div>

    <div class="item-menu">
        <a href="#">Editar &#9662;</a>
        <div class="submenu">
            <a href="editar_user.php">Cad User</a>
            <a href="editar_livre.php">Cad Livre</a>
            <a href="editar_end.php">Cad End</a>
            <a href="editar_venda.php">Cad Venda</a>
        </div>
    </div>

    <div class="item-menu">
        <a href="#">Consultar &#9662;</a>
        <div class="submenu">
            <a href="consultar_user.php">Cad User</a>
            <a href="consultar_livre.php">Cad Livre</a>
            <a href="consultar_end.php">Cad End</a>
            <a href="consultar_venda.php">Cad Venda</a>
        </div>
    </div>

    <div class="item-menu">
        <a href="#">Excluir &#9662;</a>
        <div class="submenu">
            <a href="excluir_user.php">Cad User</a>
            <a href="excluir_livre.php">Cad Livre</a>
            <a href="excluir_end.php">Cad End</a>
            <a href="excluir_venda.php">Cad Venda</a>
        </div>
    </div>
</nav>