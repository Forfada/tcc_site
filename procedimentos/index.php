<?php
    include 'functions.php';
    include(INIT);
    include(HEADER_TEMPLATE);
?>

<style>
    .proc-card {
        transition: box-shadow 0.2s, transform 0.2s;
        border-radius: 20x;
        border: none;
        box-shadow: 0 2px 16px rgba(0,0,0,0.07);
        background: #fff;
        width: 100%;
    }
    .proc-card:hover {
        box-shadow: 0 6px 32px rgba(0,0,0,0.13);
        transform: translateY(-4px) scale(1.02);
    }
    .proc-img {
        border-radius: 20px 0 0 20px;
        object-fit: cover;
        height: 100%;
        min-height: 180px;
        max-height: 200px;
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
            border-radius: 20px 20px 0 0;
            max-height: 180px;
        }
        .proc-card {
            flex-direction: column !important;
        }
    }
</style>

<section class="procedimentos section-light section-cor3 py-5" id="procedimentos">
    <div class="container mt-5" style="margin-top: 6rem !important;">
        <h2 class="txt1 mb-5 text-center">Conheça nossos procedimentos de Embelezamento e Autocuidado.</h2>
        <div class="row g-4">
            <?php foreach ($procedimentos as $proc): ?>
                <div class="col-12">
                    <div class="card proc-card flex-row align-items-stretch h-100">
                        <div class="col-4 d-none d-md-block p-0">
                            <img src="imagens/<?php echo ($proc['p_foto']); ?>"
                                 class="proc-img"
                                 alt="<?php echo ($proc['p_nome']); ?>">
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="card-body">
                                <h5 class="proc-title"><?php echo ($proc['p_nome']); ?></h5>
                                <p class="description"><?php echo ($proc['p_descricao']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include(FOOTER_TEMPLATE); ?>