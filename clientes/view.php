<?php
    include 'functions.php';
    view($_GET["id"]);
    include(INIT);
    include(HEADER_TEMPLATE);
?>

<section class="clientes section-light section-cor3 py-5" id="clientes">
            <div><?php echo htmlspecialchars($cli['cli_nome']); ?></div>
            <div><?php echo nl2br(htmlspecialchars($cli['cli_cpf'])); ?></div>
            <div class="txt2"><?php echo nl2br(htmlspecialchars($cli['cli_num'])); ?></div>
            <div><span>Valor:</span> <?php echo$cli['cli_nasc']; ?></div>
</section>

<?php include(FOOTER_TEMPLATE); ?>