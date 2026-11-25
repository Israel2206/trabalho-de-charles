<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "conexao.php";

$erro_login = "";
$destino_padrao = (isset($_GET["destino"]) && $_GET["destino"] === "fazer_pedido.php")
    ? "fazer_pedido.php"
    : "fazer_pedido.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Limpa qualquer sessão anterior antes de autenticar
    $_SESSION = [];

    $usuario_ou_email = isset($_POST["identificador"]) ? trim($_POST["identificador"]) : "";
    $senha            = isset($_POST["senha"]) ? trim($_POST["senha"]) : "";
    $destino_post     = isset($_POST["destino"]) ? trim($_POST["destino"]) : $destino_padrao;
    $tipo_entrada     = (isset($_POST["modo_acesso"]) && $_POST["modo_acesso"] === "admin")
        ? "admin"
        : "cliente";

    if ($usuario_ou_email !== "" && $senha !== "") {
        $stmt = mysqli_prepare($conexao, "SELECT * FROM cliente WHERE email = ? OR nome = ? LIMIT 1");
        $usuario_encontrado = null;

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $usuario_ou_email, $usuario_ou_email);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res && $row = mysqli_fetch_assoc($res)) {
                $usuario_encontrado = $row;
            }
        }

        $senha_valida = false;
        if ($usuario_encontrado && isset($usuario_encontrado["senha"])) {
            $senha_bd = (string) $usuario_encontrado["senha"];
            if (password_verify($senha, $senha_bd) || hash_equals($senha_bd, $senha)) {
                $senha_valida = true;
            }
        }

        if ($usuario_encontrado && $senha_valida) {
            $nivel_bruto = "";
            foreach (["nivel_acesso", "nivel", "tipo", "perfil"] as $coluna_nivel) {
                if (isset($usuario_encontrado[$coluna_nivel]) && trim((string) $usuario_encontrado[$coluna_nivel]) !== "") {
                    $nivel_bruto = strtolower(trim((string) $usuario_encontrado[$coluna_nivel]));
                    break;
                }
            }

            // No sistema, nível 2 ou 'admin'/'administrador' é administrador; nível 1 ou 'cliente' é cliente
            $eh_admin = in_array($nivel_bruto, ["2", "admin", "administrador", "gestor"], true);

            if ($tipo_entrada === "admin" && !$eh_admin) {
                $erro_login = "Acesso negado: esta conta não possui permissão de administrador.";
            } else {
                $_SESSION["usuario_id"]    = (int) $usuario_encontrado["id"];
                $_SESSION["usuario_nome"]  = $usuario_encontrado["nome"];
                $_SESSION["usuario_email"] = $usuario_encontrado["email"] ?? "";
                $_SESSION["nivel_acesso"]  = $eh_admin ? "Administrador" : "Cliente";
                $_SESSION["tipo_usuario"]  = $eh_admin ? "administrador" : "cliente";

                if ($tipo_entrada === "admin" && $eh_admin) {
                    header("Location: index.php");
                    exit;
                }

                $permitidos_cliente = ["fazer_pedido.php", "index1.php"];
                $redirecionar_cliente = in_array($destino_post, $permitidos_cliente, true)
                    ? $destino_post
                    : "fazer_pedido.php";
                header("Location: " . $redirecionar_cliente);
                exit;
            }
        } else {
            $erro_login = "E-mail/nome ou senha inválidos.";
        }
    } else {
        $erro_login = "Preencha seu e-mail ou nome e a senha para entrar.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pizzaria da Chapada</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/base.css?v=22.0">
    <link rel="stylesheet" href="style/rodape.css?v=22.0">
    <link rel="stylesheet" href="style/vitrine.css?v=22.0">
</head>
<body class="cliente-body">

    <header class="cliente-topbar">
        <a href="index1.php" class="cliente-brand">
            <img src="logoCircularPizzariaChapada.png" alt="Pizzaria da Chapada">
        </a>

        <nav class="cliente-menu">
            <a href="index1.php" class="cliente-link">Início</a>
            <a href="index1.php#top-pizzas" class="cliente-link">Top Pizzas</a>
            <a href="fazer_pedido.php" class="cliente-cta">Faça seu Pedido</a>
            <a href="cadastro_cliente.php" class="cliente-btn-sec">Cadastro</a>
            <a href="login.php" class="cliente-btn-login">Login</a>
        </nav>
    </header>

    <main class="cliente-main">
        <section class="pedido-cliente-section">
            <div class="pedido-cliente-card">
                <div class="pedido-cliente-topo">
                    <span class="pedido-selo">Acesso ao Sistema</span>
                    <h1>Entrar</h1>
                    <p>Informe seu e-mail ou nome e sua senha para acessar sua área.</p>
                </div>

                <?php if ($erro_login !== ""): ?>
                    <div class="pedido-alerta-erro">
                        <?= htmlspecialchars($erro_login, ENT_QUOTES, "UTF-8") ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST" class="pedido-form-grid" id="formLogin">
                    <input type="hidden" name="destino" value="<?= htmlspecialchars($destino_padrao, ENT_QUOTES, "UTF-8") ?>">
                    <input type="hidden" name="modo_acesso" id="modoAcessoInput" value="cliente">

                    <div class="pedido-campo">
                        <label for="identificador">E-mail ou Nome *</label>
                        <input
                            type="text"
                            name="identificador"
                            id="identificador"
                            placeholder="Digite seu e-mail ou nome"
                            value="<?= isset($_POST["identificador"]) ? htmlspecialchars($_POST["identificador"], ENT_QUOTES, "UTF-8") : "" ?>"
                            required
                        >
                    </div>

                    <div class="pedido-campo">
                        <label for="senha">Senha *</label>
                        <input
                            type="password"
                            name="senha"
                            id="senha"
                            placeholder="Digite sua senha"
                            required
                        >
                    </div>

                    <div class="pedido-acoes" style="flex-wrap: wrap;">
                        <button type="submit" onclick="document.getElementById('modoAcessoInput').value='cliente'" class="btn-pedido-confirmar">
                            Entrar
                        </button>
                        <button type="submit" onclick="document.getElementById('modoAcessoInput').value='admin'" class="btn-pedido-voltar">
                            Entrar Acesso à Administração
                        </button>
                    </div>

                    <div style="text-align: center; margin-top: 6px;">
                        <a href="cadastro_cliente.php" class="pedido-ajuda" style="text-decoration: none;">
                            Ainda não tem conta? <strong>Criar Cadastro</strong>
                        </a>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <?php include 'rodape.php'; ?>
</body>
</html>