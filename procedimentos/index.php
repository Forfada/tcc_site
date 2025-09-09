<?php
	include 'functions.php';
    include(INIT);
    include(HEADER_TEMPLATE);
?>


<section class="procedimentos section-light section-cor3" id="procedimentos">
    <div>
        <h2 class="txt1">Conheça nossos procedimentos de Embelezamento e Autocuidado.</h2>
        <?php foreach ($procedimentos as $proc): ?>
            <div class="card mb-3" style="max-width: 540px;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="imagens/<?php echo htmlspecialchars($proc['p_foto']); ?>" class="img-fluid rounded-start" alt="<?php echo htmlspecialchars($proc['p_nome']); ?>">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo ($proc['p_nome']); ?></h5>
                            <p class="card-text"><?php echo ($proc['p_descricao']); ?></p>
                            <p class="card-text"><strong>Duração:</strong> <?php echo ($proc['p_duracao']); ?></p>
                            <p class="card-text"><strong>Valor:</strong> R$ <?php echo number_format($proc['p_valor'], 2, ',', '.'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</section>

<?php include(FOOTER_TEMPLATE); ?>