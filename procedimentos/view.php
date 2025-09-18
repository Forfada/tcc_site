<?php
    include 'functions.php';
    view($_GET["id"]);

    include(INIT);
    include(HEADER_TEMPLATE);
?>

<section class="procedimentos section-light section-cor3 py-5" id="view_procedimentos">
   <h5><?php echo $proc['p_nome']; ?></h5>
</section>
<?php include(FOOTER_TEMPLATE); ?>