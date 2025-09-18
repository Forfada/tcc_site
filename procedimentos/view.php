<?php
    include 'functions.php';
    view($_GET["id"]);

    include(INIT);
    include(HEADER_TEMPLATE);
?>

<section class="procedimentos section-light section-cor3 py-5" id="view_procedimentos">
   <h5><?php echo $proc['p_nome']; ?></h5>
    <div class="col-12 col-md-7 text-md-end mt-2 mt-md-0">
        <a class="buttonc" href="edit.php" style="text-decoration: none;"><i class="fa fa-plus"></i> Editar Procedimento</a>
         <a class="buttonc" href="delete.php" style="text-decoration: none;"><i class="fa fa-plus"></i> Excluir Procedimento</a>
    </div> 
    
</section>
<?php include(FOOTER_TEMPLATE); ?>