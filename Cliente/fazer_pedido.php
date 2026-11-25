<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "conexao.php";

$mensagem = "";
$sucesso = false;
$resumo_pagamento = "";

$cliente_logado = null;
if (!empty($_SESSION["usuario_email"])) {
    $email_sessao = trim($_SESSION["usuario_email"]);
    $stmt_cli = mysqli_prepare($conexao, "SELECT id, nome, email FROM cliente WHERE email = ? LIMIT 1");
    if ($stmt_cli) {
        mysqli_stmt_bind_param($stmt_cli, "s", $email_sessao);
        mysqli_stmt_execute($stmt_cli);
        $res_cli = mysqli_stmt_get_result($stmt_cli);
        if ($res_cli && $row_cli = mysqli_fetch_assoc($res_cli)) {
            $cliente_logado = $row_cli;
        }
    }
}

$formas_pagamento = [
    "Pix",
    "Cartão de Débito",
    "Cartão de Crédito",
    "Dinheiro"
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_cliente = $cliente_logado ? (int) $cliente_logado["id"] : 0;
    $forma_pagamento = isset($_POST["forma_pagamento"]) ? trim($_POST["forma_pagamento"]) : "";
    if (!in_array($forma_pagamento, $formas_pagamento, true)) {
        $forma_pagamento = "Pix";
    }

    $ids_produtos = isset($_POST["id_produto"]) && is_array($_POST["id_produto"]) ? $_POST["id_produto"] : [];
    $quantidades  = isset($_POST["quantidade"]) && is_array($_POST["quantidade"]) ? $_POST["quantidade"] : [];

    // Agrupa itens repetidos somando as quantidades
    $itens_solicitados = [];
    foreach ($ids_produtos as $indice => $id_prod_raw) {
        $id_p = intval($id_prod_raw);
        $qtd_p = isset($quantidades[$indice]) ? intval($quantidades[$indice]) : 0;
        if ($id_p > 0 && $qtd_p > 0) {
            if (!isset($itens_solicitados[$id_p])) {
                $itens_solicitados[$id_p] = 0;
            }
            $itens_solicitados[$id_p] += $qtd_p;
        }
    }

    $status = "Pendente";
    $data_pedido = date("Y-m-d H:i:s");

    if ($id_cliente <= 0) {
        $mensagem = "Você precisa se cadastrar para fazer um pedido.";
    } elseif (empty($itens_solicitados)) {
        $mensagem = "Adicione pelo menos uma pizza ou bebida com quantidade válida ao seu pedido.";
    } else {
        mysqli_begin_transaction($conexao);

        try {
            $itens_validados = [];
            $valor_total = 0.0;

            foreach ($itens_solicitados as $id_p => $qtd_p) {
                $sql_prod = "SELECT nome, preco, estoque FROM produto WHERE id = ? FOR UPDATE";
                $stmt_prod = mysqli_prepare($conexao, $sql_prod);
                if (!$stmt_prod) {
                    throw new Exception("Erro ao consultar produto no cardápio.");
                }
                mysqli_stmt_bind_param($stmt_prod, "i", $id_p);
                mysqli_stmt_execute($stmt_prod);
                $res_prod = mysqli_stmt_get_result($stmt_prod);

                if (!$res_prod || mysqli_num_rows($res_prod) === 0) {
                    throw new Exception("Um dos produtos selecionados não foi encontrado na tabela de produtos.");
                }

                $prod = mysqli_fetch_assoc($res_prod);
                $nome_produto = $prod["nome"];
                $preco_unitario = (float) $prod["preco"];
                $estoque_atual = (int) $prod["estoque"];

                if ($estoque_atual < $qtd_p) {
                    throw new Exception("Estoque insuficiente para " . $nome_produto . " (disponível: " . $estoque_atual . " un., solicitado: " . $qtd_p . " un.).");
                }

                $subtotal = $preco_unitario * $qtd_p;
                $valor_total += $subtotal;

                $itens_validados[] = [
                    "id_produto"     => $id_p,
                    "nome"           => $nome_produto,
                    "quantidade"     => $qtd_p,
                    "preco_unitario" => $preco_unitario,
                    "subtotal"       => $subtotal
                ];
            }

            // Verifica se a coluna forma_pagamento existe na tabela pedido
            $tem_coluna_pagamento = false;
            $check_col = @mysqli_query($conexao, "SHOW COLUMNS FROM pedido LIKE 'forma_pagamento'");
            if ($check_col && mysqli_num_rows($check_col) > 0) {
                $tem_coluna_pagamento = true;
            }

            if ($tem_coluna_pagamento) {
                $sql_pedido = "INSERT INTO pedido (data_pedido, status, valor_total, id_cliente, forma_pagamento) VALUES (?, ?, ?, ?, ?)";
                $stmt_pedido = mysqli_prepare($conexao, $sql_pedido);
                if (!$stmt_pedido) {
                    throw new Exception("Erro ao preparar registro do pedido.");
                }
                mysqli_stmt_bind_param($stmt_pedido, "ssdis", $data_pedido, $status, $valor_total, $id_cliente, $forma_pagamento);
            } else {
                $sql_pedido = "INSERT INTO pedido (data_pedido, status, valor_total, id_cliente) VALUES (?, ?, ?, ?)";
                $stmt_pedido = mysqli_prepare($conexao, $sql_pedido);
                if (!$stmt_pedido) {
                    throw new Exception("Erro ao preparar registro do pedido.");
                }
                mysqli_stmt_bind_param($stmt_pedido, "ssdi", $data_pedido, $status, $valor_total, $id_cliente);
            }

            if (!mysqli_stmt_execute($stmt_pedido)) {
                throw new Exception("Erro ao registrar o pedido.");
            }

            $id_pedido = mysqli_insert_id($conexao);

            $sql_item = "INSERT INTO item_pedido (id_pedido, id_produto, quantidade, preco_unitario) VALUES (?, ?, ?, ?)";
            $stmt_item = mysqli_prepare($conexao, $sql_item);
            if (!$stmt_item) {
                throw new Exception("Erro ao preparar registro dos itens do pedido.");
            }

            $sql_estoque = "UPDATE produto SET estoque = estoque - ? WHERE id = ? AND estoque >= ?";
            $stmt_estoque = mysqli_prepare($conexao, $sql_estoque);
            if (!$stmt_estoque) {
                throw new Exception("Erro ao preparar atualização de estoque.");
            }

            $partes_resumo = [];
            foreach ($itens_validados as $item) {
                mysqli_stmt_bind_param(
                    $stmt_item,
                    "iiid",
                    $id_pedido,
                    $item["id_produto"],
                    $item["quantidade"],
                    $item["preco_unitario"]
                );
                if (!mysqli_stmt_execute($stmt_item)) {
                    throw new Exception("Erro ao salvar o item " . $item["nome"] . ".");
                }

                mysqli_stmt_bind_param(
                    $stmt_estoque,
                    "iii",
                    $item["quantidade"],
                    $item["id_produto"],
                    $item["quantidade"]
                );
                if (!mysqli_stmt_execute($stmt_estoque) || mysqli_stmt_affected_rows($stmt_estoque) !== 1) {
                    throw new Exception("Não foi possível atualizar o estoque de " . $item["nome"] . ".");
                }

                $partes_resumo[] = $item["quantidade"] . "x " . $item["nome"];
            }

            mysqli_commit($conexao);

            if (function_exists("sincronizar_bd_sql")) {
                sincronizar_bd_sql($conexao);
            }

            $sucesso = true;
            $resumo_pagamento = $forma_pagamento;
            $mensagem = "Pedido #" . $id_pedido . " realizado com sucesso! Itens: " . implode(", ", $partes_resumo) . " | Pagamento: " . $forma_pagamento . " | Total: R$ " . number_format($valor_total, 2, ",", ".");
        } catch (Throwable $e) {
            mysqli_rollback($conexao);
            $mensagem = $e instanceof mysqli_sql_exception
                ? "Ocorreu um erro ao processar o pedido. Verifique os dados e tente novamente."
                : $e->getMessage();
        }
    }
}

$res_produtos = mysqli_query($conexao, "SELECT id, nome, preco, estoque FROM produto ORDER BY nome ASC");
$produto_preselecionado = isset($_GET["id_produto"]) ? intval($_GET["id_produto"]) : 0;
$sabor_busca = isset($_GET["sabor"]) ? trim($_GET["sabor"]) : "";

$lista_produtos = [];
if ($res_produtos && mysqli_num_rows($res_produtos) > 0) {
    while ($p = mysqli_fetch_assoc($res_produtos)) {
        if ($produto_preselecionado === 0 && $sabor_busca !== "" && stripos($p["nome"], $sabor_busca) !== false) {
            $produto_preselecionado = (int) $p["id"];
        }
        $lista_produtos[] = $p;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faça seu Pedido - Pizzaria da Chapada</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/base.css?v=25.0">
    <link rel="stylesheet" href="style/rodape.css?v=25.0">
    <link rel="stylesheet" href="style/vitrine.css?v=26.0">
    <style>
        .itens-pedido-lista {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .item-pedido-linha {
            display: grid;
            grid-template-columns: 1fr 110px auto;
            gap: 10px;
            align-items: center;
            background-color: #fafaf9;
            border: 1px solid #e7e5e4;
            border-radius: 12px;
            padding: 10px;
        }
        .btn-add-item {
            align-self: flex-start;
            padding: 9px 16px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            color: #991b1b;
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .btn-add-item:hover {
            background-color: #fde68a;
        }
        .btn-remover-item {
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            color: #991b1b;
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            cursor: pointer;
        }
        .btn-remover-item:hover {
            background-color: #fee2e2;
        }
        .lista-resumo-itens {
            margin: 6px 0 0;
            padding-left: 18px;
            font-size: 13px;
            color: #292524;
        }
        @media (max-width: 640px) {
            .item-pedido-linha {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="cliente-body bg-pizzaria">

    <header class="cliente-topbar">
        <a href="index1.php" class="cliente-brand">
            <img src="logoCircularPizzariaChapada.png" alt="Pizzaria da Chapada">
        </a>

        <nav class="cliente-menu">
            <a href="index1.php" class="cliente-link">Início</a>
            <a href="index1.php#top-pizzas" class="cliente-link">Top Pizzas</a>
            <a href="fazer_pedido.php" class="cliente-cta">Faça seu Pedido</a>
            <?php if ($cliente_logado): ?>
                <?php if (($_SESSION["tipo_usuario"] ?? "") === "administrador"): ?>
                    <a href="index.php" class="cliente-btn-sec">Painel Admin</a>
                <?php endif; ?>
                <a href="logout.php" class="cliente-btn-login">Sair (<?= htmlspecialchars(explode(" ", trim($cliente_logado["nome"]))[0], ENT_QUOTES, "UTF-8") ?>)</a>
            <?php else: ?>
                <a href="cadastro_cliente.php" class="cliente-btn-sec">Cadastro</a>
                <a href="login.php" class="cliente-btn-login">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="cliente-main">
        <section class="pedido-cliente-section">
            <div class="pedido-cliente-card">
                <div class="pedido-cliente-topo">
                    <span class="pedido-selo">Pizzaria da Chapada</span>
                    <h1>Faça seu Pedido</h1>
                    <p>Monte seu pedido com várias pizzas, bebidas ou outros produtos cadastrados e escolha a forma de pagamento.</p>
                </div>

                <?php if ($mensagem !== ""): ?>
                    <div class="<?= $sucesso ? 'pedido-alerta-sucesso' : 'pedido-alerta-erro' ?>">
                        <?= htmlspecialchars($mensagem, ENT_QUOTES, "UTF-8") ?>
                    </div>
                <?php endif; ?>

                <?php if (!$cliente_logado): ?>
                    <div class="pedido-alerta-erro">
                        Você precisa se cadastrar para fazer um pedido.
                        <div class="pedido-acoes" style="margin-top: 14px;">
                            <a href="cadastro_cliente.php" class="btn-pedido-confirmar" style="text-align:center; text-decoration:none;">Cadastrar Cliente</a>
                            <a href="login.php?destino=fazer_pedido.php" class="btn-pedido-voltar">Fazer Login</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="pedido-alerta-sucesso">
                        Pedido em nome de: <strong><?= htmlspecialchars($cliente_logado["nome"], ENT_QUOTES, "UTF-8") ?></strong>
                    </div>

                    <form action="fazer_pedido.php" method="POST" class="pedido-form-grid" id="formPedidoMultiplo">
                        <div class="pedido-campo">
                            <label>Pizzas, Bebidas e Produtos *</label>

                            <div id="containerItens" class="itens-pedido-lista">
                                <div class="item-pedido-linha">
                                    <select name="id_produto[]" class="select-produto-item" required onchange="atualizarResumoPedido()">
                                        <option value="" data-preco="0" data-estoque="0" data-nome="">Selecione uma pizza ou bebida...</option>
                                        <?php foreach ($lista_produtos as $p): ?>
                                            <?php
                                            $disponivel = (int) $p["estoque"] > 0;
                                            $sel_prod = ((int) $p["id"] === $produto_preselecionado) ? "selected" : "";
                                            ?>
                                            <option
                                                value="<?= (int) $p["id"] ?>"
                                                data-preco="<?= htmlspecialchars((string) $p["preco"], ENT_QUOTES, "UTF-8") ?>"
                                                data-estoque="<?= (int) $p["estoque"] ?>"
                                                data-nome="<?= htmlspecialchars($p["nome"], ENT_QUOTES, "UTF-8") ?>"
                                                <?= $sel_prod ?>
                                                <?= !$disponivel ? "disabled" : "" ?>
                                            >
                                                <?= htmlspecialchars($p["nome"], ENT_QUOTES, "UTF-8") ?> - R$ <?= number_format((float) $p["preco"], 2, ",", ".") ?>
                                                <?= $disponivel ? " (" . (int) $p["estoque"] . " disp.)" : " (Sem estoque)" ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <input
                                        type="number"
                                        name="quantidade[]"
                                        class="input-qtd-item"
                                        min="1"
                                        value="1"
                                        aria-label="Quantidade"
                                        required
                                        oninput="atualizarResumoPedido()"
                                    >

                                    <button type="button" class="btn-remover-item" onclick="removerLinhaItem(this)" title="Remover item">
                                        ✕
                                    </button>
                                </div>
                            </div>

                            <button type="button" class="btn-add-item" onclick="adicionarLinhaItem()">
                                + Adicionar outra pizza ou bebida
                            </button>
                        </div>

                        <div class="pedido-campo">
                            <label for="forma_pagamento">Forma de Pagamento *</label>
                            <select name="forma_pagamento" id="forma_pagamento" required onchange="atualizarResumoPedido()">
                                <?php foreach ($formas_pagamento as $fp): ?>
                                    <?php $sel_fp = (isset($_POST["forma_pagamento"]) && $_POST["forma_pagamento"] === $fp) ? "selected" : ""; ?>
                                    <option value="<?= htmlspecialchars($fp, ENT_QUOTES, "UTF-8") ?>" <?= $sel_fp ?>>
                                        <?= htmlspecialchars($fp, ENT_QUOTES, "UTF-8") ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="pedido-resumo-box">
                            <span class="resumo-rotulo">Valor Total do Pedido</span>
                            <strong id="valorEstimadoTexto" class="resumo-valor">R$ 0,00</strong>
                            <small id="estoqueInfoTexto" class="resumo-estoque">Pagamento selecionado: Pix</small>
                            <ul id="listaResumoItens" class="lista-resumo-itens"></ul>
                        </div>

                        <div class="pedido-acoes">
                            <button type="submit" class="btn-pedido-confirmar">Confirmar Pedido</button>
                            <a href="index1.php" class="btn-pedido-voltar">Voltar para a Vitrine</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include 'rodape.php'; ?>

    <template id="templateLinhaItem">
        <div class="item-pedido-linha">
            <select name="id_produto[]" class="select-produto-item" required onchange="atualizarResumoPedido()">
                <option value="" data-preco="0" data-estoque="0" data-nome="">Selecione uma pizza ou bebida...</option>
                <?php foreach ($lista_produtos as $p): ?>
                    <?php $disponivel = (int) $p["estoque"] > 0; ?>
                    <option
                        value="<?= (int) $p["id"] ?>"
                        data-preco="<?= htmlspecialchars((string) $p["preco"], ENT_QUOTES, "UTF-8") ?>"
                        data-estoque="<?= (int) $p["estoque"] ?>"
                        data-nome="<?= htmlspecialchars($p["nome"], ENT_QUOTES, "UTF-8") ?>"
                        <?= !$disponivel ? "disabled" : "" ?>
                    >
                        <?= htmlspecialchars($p["nome"], ENT_QUOTES, "UTF-8") ?> - R$ <?= number_format((float) $p["preco"], 2, ",", ".") ?>
                        <?= $disponivel ? " (" . (int) $p["estoque"] . " disp.)" : " (Sem estoque)" ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input
                type="number"
                name="quantidade[]"
                class="input-qtd-item"
                min="1"
                value="1"
                aria-label="Quantidade"
                required
                oninput="atualizarResumoPedido()"
            >

            <button type="button" class="btn-remover-item" onclick="removerLinhaItem(this)" title="Remover item">
                ✕
            </button>
        </div>
    </template>

    <script>
        function adicionarLinhaItem() {
            const container = document.getElementById("containerItens");
            const template = document.getElementById("templateLinhaItem");
            if (!container || !template) return;

            const clone = template.content.cloneNode(true);
            container.appendChild(clone);
            atualizarResumoPedido();
        }

        function removerLinhaItem(botao) {
            const container = document.getElementById("containerItens");
            if (!container) return;

            const linhas = container.querySelectorAll(".item-pedido-linha");
            if (linhas.length <= 1) {
                const select = linhas[0].querySelector(".select-produto-item");
                const input = linhas[0].querySelector(".input-qtd-item");
                if (select) select.value = "";
                if (input) input.value = 1;
                atualizarResumoPedido();
                return;
            }

            const linhaAtual = botao.closest(".item-pedido-linha");
            if (linhaAtual) {
                linhaAtual.remove();
                atualizarResumoPedido();
            }
        }

        function atualizarResumoPedido() {
            const textoValor = document.getElementById("valorEstimadoTexto");
            const textoInfo = document.getElementById("estoqueInfoTexto");
            const listaResumo = document.getElementById("listaResumoItens");
            const selectPagamento = document.getElementById("forma_pagamento");
            const linhas = document.querySelectorAll("#containerItens .item-pedido-linha");

            if (!textoValor || !textoInfo || !listaResumo) return;

            let totalGeral = 0;
            let avisosEstoque = [];
            let resumoLinhas = [];

            linhas.forEach((linha) => {
                const selectProd = linha.querySelector(".select-produto-item");
                const inputQtd = linha.querySelector(".input-qtd-item");
                if (!selectProd || !inputQtd) return;

                const option = selectProd.options[selectProd.selectedIndex];
                const preco = option ? parseFloat(option.getAttribute("data-preco") || "0") : 0;
                const estoque = option ? parseInt(option.getAttribute("data-estoque") || "0", 10) : 0;
                const nome = option ? (option.getAttribute("data-nome") || "") : "";
                const qtd = Math.max(1, parseInt(inputQtd.value || "1", 10));

                if (selectProd.value !== "" && preco > 0) {
                    const subtotal = preco * qtd;
                    totalGeral += subtotal;

                    if (qtd > estoque) {
                        avisosEstoque.push(nome + ": disp. " + estoque + " un.");
                    }

                    resumoLinhas.push(
                        qtd + "x " + nome + " (R$ " + subtotal.toFixed(2).replace(".", ",") + ")"
                    );
                }
            });

            textoValor.textContent = "R$ " + totalGeral.toFixed(2).replace(".", ",");

            const metodoPag = selectPagamento ? selectPagamento.value : "Pix";
            if (avisosEstoque.length > 0) {
                textoInfo.textContent = "Estoque excedido em: " + avisosEstoque.join(" | ");
            } else {
                textoInfo.textContent = "Pagamento: " + metodoPag + (totalGeral > 0 ? " • Estoque conferido" : " • Selecione itens para calcular");
            }

            listaResumo.innerHTML = "";
            resumoLinhas.forEach((textoItem) => {
                const li = document.createElement("li");
                li.textContent = textoItem;
                listaResumo.appendChild(li);
            });
        }

        atualizarResumoPedido();
    </script>
</body>
</html>