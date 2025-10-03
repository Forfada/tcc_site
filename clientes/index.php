<?php
    include 'functions.php';
    include(INIT);
    include(HEADER_TEMPLATE);
    index();
?>  

<section class="clientes section-light section-cor3 py-5" id="clientes">
     <div class="container mt-5" style="margin-top: 6rem !important;">
        <form name="filtro" action="index.php" method="post">
            <div class="row align-items-center"> 
                <div class="form-group col-12 col-md-5 mb-2 mb-md-0">
                    <div class="input-group">
                        <input type="text" class="form-control" maxlength="50" name="cli" placeholder="Procurar Procedimento...">
                    <button type="submit" class="btn text-white" style="background-color: var(--cor7)"><i class="fa-solid fa-magnifying-glass"></i> Consultar</button>
                    </div>
                </div>
                <div class="col-12 col-md-7 text-md-end mt-2 mt-md-0">
                    <a class="buttonc" href="add.php" style="text-decoration: none"><i class="fa fa-plus"></i> Adicionar Procedimento</a>
                </div> 
            </div>
		</form>

        <hr>
       
            <div class="row g-4">
                <?php if ($clientes) : ?>
                <?php foreach ($clientes as $cli): ?>
                    <div class="col-lg-12 mx-auto">
                        <a href="view.php?id=<?php echo $cli['id']; ?>" style="text-decoration: none; color: inherit;">
                            <div class="card proc-card flex-row align-items-stretch h-100">
                                <div class="col-12 col-md-8">
                                    <div class="card-body">
                                        <h5 class="proc-title"><?php echo ($proc['cli_nome']); ?></h5>
                                        <button class="buttonc">Agende já!</button>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
                <?php else : ?>
                <h5 colspan="6">Nenhum procedimento encontrado.</h5>
                <?php endif; ?>
            </div>
        
    </div>
</section>

<?php include(FOOTER_TEMPLATE); ?>