<?php
// footer.php
?>

<style>
    .ifba-footer {
        background-color: #D97706;
        color: #ffffff;
        font-family: Arial, Helvetica, sans-serif;
        margin-top: 40px;
        width: 100%;
    }

    .ifba-footer a {
        color: #ffffff;
        text-decoration: none;
        transition: opacity 0.2s;
    }

    .ifba-footer a:hover {
        text-decoration: underline;
        opacity: 0.85;
    }

    .ifba-footer-main {
        max-width: 1200px;
        margin: 0 auto;
        padding: 30px 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 30px;
    }

    .ifba-footer-section {
        flex: 1 1 220px;
    }

    .ifba-footer-section h3 {
        font-size: 17px;
        margin: 0 0 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #ffffff;
    }

    .ifba-footer-section p {
        font-size: 14px;
        line-height: 1.7;
        margin: 6px 0;
    }

    .ifba-footer-section ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .ifba-footer-section li {
        margin-bottom: 10px;
        font-size: 14px;
    }

    .ifba-footer-bottom {
        background-color: #1F2937;
        text-align: center;
        padding: 16px 20px;
        font-size: 13px;
        line-height: 1.6;
    }

    .ifba-footer-bottom p {
        margin: 4px 0;
    }

    @media (max-width: 600px) {
        .ifba-footer-main {
            flex-direction: column;
            gap: 20px;
            padding: 25px 20px;
        }

        .ifba-footer-section {
            flex-basis: auto;
        }

        .ifba-footer-section h3 {
            font-size: 16px;
        }
    }
</style>

<footer class="ifba-footer">

    <div class="ifba-footer-main">

        <!-- Identificação institucional -->
        <section class="ifba-footer-section">
            <h3>Pizzaria da Chapada</h3>

            <p>
                Pizzariada Chapada para você!!!
            </p>

            <p>
               Aproveita e faça seu pedido.
            </p>
        </section>

        <!-- Contato -->
        <section class="ifba-footer-section">
            <h3>Contato</h3>

            <p>
                <strong>Dono:</strong> Pitomba
            </p>

            <p>
                <strong>Endereço:</strong> Veredinha
            </p>

            <p>
                <strong>E-mail:</strong>
                <a href="mailto:contato@exemplo.com">
                    pizzariadachapada@gmail.com
                </a>
            </p>

            <p>
                <strong>Telefone:</strong> 75 9834-4321
            </p>
        </section>


    </div>

    <!-- Direitos autorais -->
    <div class="ifba-footer-bottom">
        <p>
            &copy; <?= date('Y') ?>
            Pizzaria da Chapda - Veredinha
            Todos os direitos reservados.
        </p>

        <p>
            Desenvolvido para fins institucionais e educacionais.
        </p>
    </div>

</footer>