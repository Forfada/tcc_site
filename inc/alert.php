<?php
if (!isset($_SESSION)) session_start();

// prefer local $message/$type (set during same request) or fallback para sessão
$flash = $message ?? ($_SESSION['message'] ?? null);
$flash_type = $type ?? ($_SESSION['type'] ?? 'info');

if (!empty($flash)):
?>
<div id="floating-alert" class="alert alert-<?php echo htmlspecialchars($flash_type); ?> alert-dismissible fade show floating-alert" role="alert">
    <?php echo $flash; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<style>
/* ALERT FLUTUANTE CENTRALIZADO (top será ajustado via JS para ficar abaixo do header) */
.floating-alert {
    position: fixed;
    top: auto;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1060;
    min-width: 300px;
    max-width: 500px;
    box-shadow: 0 0 15px rgba(0,0,0,0.3);
    border-radius: 8px;
    margin: 0 12px;
    word-break: break-word;
}
</style>

<script>
(function(){
    try {
        var alertEl = document.getElementById('floating-alert');
        if (!alertEl) return;
        // localizar header/nav mais provável e medir sua altura
        var header = document.querySelector('header, .navbar, #mainNavbar, .site-header');
        var headerHeight = header ? Math.ceil(header.getBoundingClientRect().height) : 64;
        // garantir espaço mínimo
        if (headerHeight < 40) headerHeight = 64;
        alertEl.style.top = (headerHeight + 12) + 'px';
        // pequeno ajuste ao rolar/resizar
        var adjust = function(){
            var h = header ? Math.ceil(header.getBoundingClientRect().height) : headerHeight;
            alertEl.style.top = (h + 12) + 'px';
        };
        window.addEventListener('resize', adjust);
        window.addEventListener('scroll', adjust);
    } catch(e) {
        console && console.warn && console.warn('Erro ao posicionar alert:', e);
    }
})();
</script>

<script>
    // limpa sessão (se houver) após renderizar
    <?php
    unset($_SESSION['message'], $_SESSION['type']);
    ?>
</script>
<?php endif; ?>
