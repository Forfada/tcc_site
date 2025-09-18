<?php
    include 'functions.php';
    view($_GET["id"]);

    include(INIT);
    include(HEADER_TEMPLATE);
?>

<section class="procedimentos section-light section-cor3 py-5" id="view_procedimentos">
   <h5><?php echo $proc['p_nome']; ?></h5>
    <div class="col-12 col-md-7 text-md-end mt-2 mt-md-0">
        <a class="buttonc" href="edit.php?id=<?php echo $proc['id']; ?>" style="text-decoration: none;"><i class="fa fa-plus"></i> Editar Procedimento</a>
         <a href="#" class="buttonc" style="text-decoration: none;"
        data-bs-toggle="modal" data-bs-target="#delete-proc-modal" data-procedimentos="<?php echo $proc['id'];?>">
            <i class="fa fa-trash  me-2"></i> Excluir
        </a>
    </div> 
</section>
<?php include("modal.php"); ?>
<?php include(FOOTER_TEMPLATE); ?>