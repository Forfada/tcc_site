<?php
    include 'functions.php';
    include(INIT);
    include(HEADER_TEMPLATE);
    index();
?>  

<style>
    .proc-card {
        transition: box-shadow 0.2s, transform 0.2s;
        border-radius: 20x;
        border: none;
        box-shadow: 0 2px 16px rgba(0, 0, 0, 0.07);
        background: #fff;
        width: 100%;
        align-items: center;
    }
    .proc-card:hover {
        box-shadow: 0 6px 32px rgba(0, 0, 0, 0.21);
        transform: translateY(-4px) scale(1.02);
    }
    .proc-img {
        border-radius: 20px 0 0 20px;
        object-fit: cover;
        height: 100%;
        min-height: 180px;
        max-height: 228px;
        width: 100%;
        background: #f8f9fa;
    }
    .proc-title {
        color: var(--cor2);
        font-weight: 700;
        font-size: 1.25rem;
    }
    .proc-price {
        color: var(--cor2);
        font-weight: 600;
        font-size: 1.1rem;
    }
    .proc-duration {
        font-size: 0.98rem;
        color: #fff;
    }
    @media (max-width: 767px) {
        .proc-img {
            position: relative;
            height: 380px;
            width: 100%;
            border-radius: 20px;
        }
        .proc-card {
            flex-direction: column !important;
            display: flex;
            border-radius: 25px;
            width: 85%;
            padding: 10px 14px;
            align-items: center;
            margin: 0 auto;
        }
         .row.g-4 {
            justify-content: center;
            align-items: center;
            display: flex;
            flex-direction: column;
        }
    }
</style>

<section class="procedimentos section-light section-cor3 py-5" id="procedimentos">
    <div class="container mt-5" style="margin-top: 6rem !important;">
        <h2 class="txt1 mb-1 text-center">Conheça nossos serviços!</h2>
        <p class="txt4 text-center mb-2"> Conheça nossos procedimentos de Embelezamento e Autocuidado.</p> 
        
        <form name="filtro" action="index.php" method="post">
            <div class="row align-items-center"> 
                <div class="form-group col-12 col-md-5 mb-2 mb-md-0">
                    <div class="input-group">
                        <input type="text" class="form-control" maxlength="50" name="proc" placeholder="Procurar Procedimento...">
                    <button type="submit" class="btn text-white" style="background-color: var(--cor7)"><i class="fa-solid fa-magnifying-glass"></i> Consultar</button>
                    </div>
                </div>
                <div class="col-12 col-md-7 text-md-end mt-2 mt-md-0">
                    <?php if (isset($_SESSION['user']) && $_SESSION['user'] === 'admin'): ?>
                        <a class="buttonc" href="add.php" style="text-decoration: none"><i class="fa fa-plus"></i> Adicionar Procedimento</a>
                    <?php endif; ?>
                </div> 
            </div>
		</form>

        <hr>
       
            <div class="row g-4">
                <?php if ($procedimentos) : ?>
                <?php foreach ($procedimentos as $proc): ?>
                    <div class="col-lg-12 mx-auto">
                        <a href="view.php?id=<?php echo $proc['id']; ?>" style="text-decoration: none; color: inherit;">
                            <div class="card proc-card flex-row align-items-stretch h-100">
                                <div class="col-md-3 d-md-block p-0">
                                    <img src="imagens/<?php echo ($proc['p_foto']); ?>"
                                        class="proc-img"
                                        alt="<?php echo ($proc['p_nome']); ?>">
                                </div>
                                <div class="col-12 col-md-8">
                                    <div class="card-body">
                                        <h5 class="proc-title"><?php echo ($proc['p_nome']); ?></h5>
                                        <p class="description"><?php echo ($proc['p_descricao']); ?></p>
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