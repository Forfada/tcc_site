<?php
if (!isset($_SESSION)) session_start();
if (!empty($_SESSION['message'])) :
    $type = $_SESSION['type'] ?? 'info';
?>
<div class="alert alert-<?php echo $type; ?> alert-dismissible fade show floating-alert" role="alert">
    <?php echo $_SESSION['message']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<style>
    /* ALERT FLUTUANTE CENTRALIZADO */
    .floating-alert {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1050;
        min-width: 300px;
        max-width: 500px;
        box-shadow: 0 0 15px rgba(0,0,0,0.3);
        border-radius: 8px;
    }
</style>

<script>
    // Remove mensagem da sessão após exibir
    <?php
    $_SESSION['message'] = null;
    $_SESSION['type'] = null;
    ?>
</script>
<?php endif; ?>
