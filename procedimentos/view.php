<?php
    include 'functions.php';
    view($_GET["id"]);
    include(INIT);
    include(HEADER_TEMPLATE);
?>

<style>
    .view-proc-container {
        display: flex;
        flex-wrap: wrap;
        align-items: center;       /* centraliza verticalmente */
        justify-content: center;   /* centraliza horizontalmente */
        gap: 3vw;
        background: transparent;
        padding: 3.2rem 2vw 2.2rem 2vw;
        max-width: 1200px;
        margin: 4rem auto 2rem auto;
        border-bottom: 1px solid rgba(0,0,0,0.08);
    }

    /* --- Imagem --- */
    .view-proc-img {
        flex: 0 0 500px;
        max-width: 500px;
        width: 100%;
        height: 420px;             /* altura maior em desktop */
        object-fit: cover;
        border-radius: 18px;
        box-shadow: 0 2px 14px rgba(84,31,51,0.12);
        background: #fff;
    }

    /* --- Info --- */
    .view-proc-info {
        flex: 1 1 360px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;   /* alinhado à esquerda em desktops */
        gap: 1.2rem;
        min-width: 260px;
        max-width: 500px;
        width: 100%;
    }

    .view-proc-title {
        font-size: 2.3rem;
        font-weight: 700;
        color: var(--cor2);
        margin-bottom: 0.5rem;
        font-family: 'Playfair Display', serif;
        text-align: center;
        width: 100%;
    }

    .view-proc-desc {
        font-size: 1.18rem;
        color: var(--cor4);
        font-family: 'Verdana', sans-serif;
        margin-bottom: 0.8rem;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }

    /* --- Infos extras (lado a lado) --- */
    .view-proc-extra {
        display: flex;
        justify-content: space-between;
        gap: 2rem;
        font-size: 1.1rem;
        font-family: 'Verdana', sans-serif;
        color: var(--cor2);
        width: 100%;
        max-width: 500px;
        margin-bottom: 1rem;
    }

    .view-proc-extra div {
        flex: 1;
    }

    .view-proc-extra span {
        font-weight: 600;
        color: var(--cor4);
        margin-right: 4px;
    }

    /* --- Botões --- */
    .view-proc-btns {
        display: flex;
        gap: 0.7rem;
        margin-top: auto;
        justify-content: center;
        width: 100%;
        flex-wrap: wrap;
    }

    .buttonc, .button-back {
        border: none;
        font-size: 18px;
        color: var(--cor3);
        padding: 8px 18px;
        background-color: var(--cor2);
        border-radius: 8px;
        margin: 0 4px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .buttonc:hover, .button-back:hover {
        background: var(--cor4);
    }

    .button-back {
        background-color: var(--cor5);
        color: #f5eadc;
        font-weight: 500;
        font-size: 18px;
        padding: 8px 18px;
    }


    @media (max-width: 700px) {
        .view-proc-container {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .view-proc-info {
            align-items: center;
        }
        .view-proc-title {
            order: -1;
            font-size: 1.7rem;
            text-align: center;
        }
        .view-proc-img {
            width: 100%;
            max-width: 90vw;
            max-height: 45vh;
            height: auto;
            min-height: 180px;
        }
        .view-proc-desc {
            font-size: 1.3rem;
            max-width: 95vw;
            text-align: center;
        }
        .view-proc-extra {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 0.3rem;
        }
        .buttonc, .button-back {
            font-size: 18px;
            padding: 7px 12px;
        }
    }

    @media (max-width: 480px) {
        .view-proc-title {
            font-size: 1.6rem;
        }
        .view-proc-desc, .view-proc-extra {
            font-size: 1.1rem;
        }
        .view-proc-btns {
            flex-direction: column;
            gap: 0.5rem;
        }
        .buttonc, .button-back {
            width: 100%;
            justify-content: center;

        }
        .view-proc-btns{
            padding-left: 5%;
            padding-right: 5%;
            justify-content: center;
        }
    }
</style>

<section class="procedimentos section-light section-cor3 py-5" id="procedimentos">
    <div class="view-proc-container">
        <img class="view-proc-img" src="imagens/<?php echo htmlspecialchars($proc['p_foto']); ?>" alt="<?php echo htmlspecialchars($proc['p_nome']); ?>">
        
        <div class="view-proc-info">
            <div class="view-proc-title"><?php echo htmlspecialchars($proc['p_nome']); ?></div>
            <div class="view-proc-desc"><?php echo nl2br(htmlspecialchars($proc['p_descricao'])); ?></div>
            <div class=" txt2"><b>O que é?</b><br><?php echo nl2br(htmlspecialchars($proc['p_descricao2'])); ?></div>

            <div class="view-proc-extra">
                <div><span>Valor:</span> R$ <?php echo htmlspecialchars($proc['p_valor']); ?></div>
                <div><span>Duração:</span> <?php echo htmlspecialchars($proc['p_duracao']); ?></div>
            </div>

            <div class="view-proc-btns">
                <a class="buttonc" href="<?php echo BASEURL; ?>procedimentos/edit.php?id=<?php echo $proc['id']; ?>"><i class="fa-regular fa-pen-to-square"></i> Editar Procedimento</a>
                <a href="#" class="buttonc"
                    data-bs-toggle="modal" data-bs-target="#delete-proc-modal" data-procedimentos="<?php echo $proc['id']; ?>">
                    <i class="fa fa-trash"></i> Excluir
                </a>
                <a href="index.php" class="button-back"><i class="fa-solid fa-arrow-rotate-left"></i> Voltar</a>
            </div>
        </div>
    </div>
</section>

<?php include("modal.php"); ?>
<?php include(FOOTER_TEMPLATE); ?>
