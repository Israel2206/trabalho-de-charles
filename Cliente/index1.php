<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "conexao.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizzaria da Chapada</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/base.css?v=27.0">
    <link rel="stylesheet" href="style/rodape.css?v=27.0">
    <link rel="stylesheet" href="style/vitrine.css?v=27.0">
</head>
<body class="cliente-body bg-pizzaria">

    <!-- Menu exclusivo da experiência do cliente -->
    <header class="cliente-topbar">
        <a href="index1.php" class="cliente-brand">
            <img src="logoCircularPizzariaChapada.png" alt="Pizzaria da Chapada">
        </a>

        <nav class="cliente-menu">
            <a href="#top-pizzas" class="cliente-link">Top Pizzas</a>
            <a href="fazer_pedido.php" class="cliente-cta">Faça seu Pedido</a>
            <?php if (!empty($_SESSION["usuario_nome"])): ?>
                <?php if (($_SESSION["tipo_usuario"] ?? "") === "administrador"): ?>
                    <a href="index.php" class="cliente-btn-sec">Painel Admin</a>
                <?php endif; ?>
                <a href="logout.php" class="cliente-btn-login">Sair (<?= htmlspecialchars(explode(" ", trim($_SESSION["usuario_nome"]))[0], ENT_QUOTES, "UTF-8") ?>)</a>
            <?php else: ?>
                <a href="cadastro_cliente.php" class="cliente-btn-sec">Cadastro</a>
                <a href="login.php" class="cliente-btn-login">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="cliente-main">

        <!-- Banner único com transição deslizante entre post1.png e post2.png -->
        <section class="cliente-banner-box" aria-label="Destaques e Promoções">
            <div class="cliente-banner-trilho" id="bannerTrilho">
                <div class="cliente-banner-item">
                    <a href="fazer_pedido.php">
                        <img src="post1.png" alt="Terça em Dobro é na Pizzaria da Chapada">
                    </a>
                </div>
                <div class="cliente-banner-item">
                    <a href="fazer_pedido.php">
                        <img src="post2.png" alt="Peça sua pizza agora na Pizzaria da Chapada">
                    </a>
                </div>
            </div>

            <button type="button" class="cliente-banner-seta esq" onclick="trocarBanner(-1)" aria-label="Banner anterior">&#10094;</button>
            <button type="button" class="cliente-banner-seta dir" onclick="trocarBanner(1)" aria-label="Próximo banner">&#10095;</button>

            <div class="cliente-banner-pontos">
                <button type="button" class="ponto ativo" onclick="irParaBanner(0)" aria-label="Banner 1"></button>
                <button type="button" class="ponto" onclick="irParaBanner(1)" aria-label="Banner 2"></button>
            </div>
        </section>

        <!-- Seção #Top Pizzas mais pedidas -->
        <section id="top-pizzas" class="top-pedidas-wrap">
            <header class="top-pedidas-topo">
                <h2>#Top Pizzas mais pedidas</h2>
                <p>Queremos te ajudar a escolher a sua pizza de hoje.</p>
            </header>

            <div class="top-pedidas-lista">
                <a href="fazer_pedido.php?sabor=Calabresa+Paulistania" class="top-pedida-card">
                    <div class="top-pedida-disco">
                        <img src="CalabresaPaulistania.png" alt="Calabresa Paulistânia">
                    </div>
                    <span class="top-pedida-titulo">Calabresa Paulistânia</span>
                </a>

                <a href="fazer_pedido.php?sabor=Portuguesa" class="top-pedida-card">
                    <div class="top-pedida-disco">
                        <img src="Portuguesa.png" alt="Portuguesa">
                    </div>
                    <span class="top-pedida-titulo">Portuguesa</span>
                </a>

                <a href="fazer_pedido.php?sabor=Pepperoni" class="top-pedida-card">
                    <div class="top-pedida-disco">
                        <img src="pepperoni.png" alt="Pepperoni">
                    </div>
                    <span class="top-pedida-titulo">Pepperoni</span>
                </a>

                <a href="fazer_pedido.php?sabor=Pizza+com+Catupiry" class="top-pedida-card">
                    <div class="top-pedida-disco">
                        <img src="pizza-com-catupiry.png" alt="Frango com Catupiry">
                    </div>
                    <span class="top-pedida-titulo">Frango com Catupiry</span>
                </a>
            </div>
        </section>

    </main>

    <!-- Mesmo rodapé usado na gestão do administrador -->
    <?php include 'rodape.php'; ?>

    <script>
        const trilho = document.getElementById('bannerTrilho');
        const pontos = document.querySelectorAll('.cliente-banner-pontos .ponto');
        const totalBanners = 2;
        let indiceBanner = 0;
        let intervaloAuto;

        function atualizarBanner() {
            trilho.style.transform = `translateX(-${indiceBanner * 100}%)`;
            pontos.forEach((ponto, i) => {
                ponto.classList.toggle('ativo', i === indiceBanner);
            });
        }

        function trocarBanner(passo) {
            indiceBanner = (indiceBanner + passo + totalBanners) % totalBanners;
            atualizarBanner();
            reiniciarAutoPlay();
        }

        function irParaBanner(indice) {
            indiceBanner = indice;
            atualizarBanner();
            reiniciarAutoPlay();
        }

        function iniciarAutoPlay() {
            clearInterval(intervaloAuto);
            intervaloAuto = setInterval(() => {
                indiceBanner = (indiceBanner + 1) % totalBanners;
                atualizarBanner();
            }, 5000);
        }

        function reiniciarAutoPlay() {
            iniciarAutoPlay();
        }

        iniciarAutoPlay();
    </script>
</body>
</html>