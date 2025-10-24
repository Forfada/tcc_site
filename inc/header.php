<!DOCTYPE html>
<html lang="pt-BR">
    
<?php include(INIT); ?>

<body data-bs-spy="scroll" data-bs-target="#mainNavbar" data-bs-offset="80" tabindex="0">

<?php 
if (!isset($_SESSION)) session_start();
require_once(ABSPATH . "inc/auto_login.php");
checkAutoLogin();
?>

<!-- ALERTA DE MENSAGEM -->
<?php if (!empty($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['type']; ?> alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x m-3" role="alert" style="z-index: 1055;">
        <?php echo $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['type']); ?>
<?php endif; ?>

<nav class="navbar fixed-top navbar-expand-lg p-3 navbar-colored" data-bs-theme="dark" id="mainNavbar">
    <div class="container-fluid">
        <!-- LOGO -->
        <a class="navbar-brand ms-4" href="<?php echo BASEURL; ?>">
            <img src="<?php echo rtrim(BASEURL, '/'); ?>/img/logoc.png" alt="Logo" class="navbar-logo" id="logo">
        </a>

        <!-- BOTÃO HAMBÚRGUER -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- LINKS  -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link ms-4 me-4" href="<?php echo BASEURL; ?>#home">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ms-4 me-4" href="<?php echo BASEURL; ?>#sobre">Sobre nós</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ms-4 me-4" href="<?php echo BASEURL; ?>procedimentos/#procedimentos">Procedimentos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ms-4 me-4" href="<?php echo BASEURL; ?>agendamentos/agendamento.php#agendamento">Agendamento</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ms-4 me-4" href="<?php echo BASEURL; ?>#contato">Contato</a>
                </li>

                <?php
                // mostra opção "Clientes" no header apenas para administrador
                if (function_exists('is_admin') && is_admin()): ?>
                    <li class="nav-item">
                        <a class="nav-link ms-4 me-4" href="<?php echo BASEURL; ?>clientes/index.php">Clientes</a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- LOGIN OU AVATAR -->
            <div class="ms-auto me-5">
                <?php if (isset($_SESSION['nome'])): ?>
                    <div class="user-avatar" id="userAvatar" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasRight" aria-controls="offcanvasRight" style="cursor:pointer;">
                        <img src="<?= BASEURL ?>img/avatars/<?= $_SESSION['foto'] ?>" alt="Avatar do usuário" width="40" height="40" class="rounded-circle">
                    </div>
                <?php else: ?>
                    <a href="<?= BASEURL ?>inc/login.php" class="btn custom-login-btn">
                        <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Offcanvas fora do collapse -->
<?php if (isset($_SESSION['nome'])): ?>
    <div class="offcanvas offcanvas-end text-white" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel" style="background-color: var(--cor2);">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title d-flex align-items-center gap-2" id="offcanvasRightLabel">
                <img src="<?= BASEURL ?>img/avatars/<?= $_SESSION['foto'] ?>" alt="Avatar do usuário" width="40" height="40" class="rounded-circle">
                <?= htmlspecialchars($_SESSION['nome']); ?>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column gap-3">

            <div>
                <p class="mb-1 fw-semibold text-uppercase small">Conta</p>
                <a href="<?= BASEURL ?>inc/alterar_senha.php" class="btn btn-outline-light w-100 text-start"><i class="fa-solid fa-lock"></i> Alterar senha</a>

                <?php
                // Mostrar links diferentes quando o usuário for administrador:
                // - se for admin: exibir os dois links administrativos (futuros + histórico admin) e Clientes
                // - se não for admin: exibir somente o histórico do próprio usuário
                if (function_exists('is_admin') && is_admin()): ?>
                    <a href="<?= BASEURL ?>agendamentos/admin_upcoming.php" class="btn btn-outline-light w-100 text-start"><i class="fa-solid fa-calendar-days"></i> Agendamentos (Futuros)</a>
                    <a href="<?= BASEURL ?>agendamentos/admin_historico.php" class="btn btn-outline-light w-100 text-start"><i class="fa-solid fa-file-lines"></i> Histórico (Admin)</a>
                    <a href="<?= BASEURL ?>clientes/index.php" class="btn btn-outline-light w-100 text-start"><i class="fa-solid fa-users"></i>  Clientes</a>
                <?php else: ?>
                    <a href="<?= BASEURL ?>agendamentos/historico.php" class="btn btn-outline-light w-100 text-start"><i class="fa-solid fa-user-clock"></i>  Histórico</a>
                <?php endif; ?>

                <button class="btn btn-outline-light w-100 text-start mt-2" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fa-solid fa-trash-can"></i>  Excluir conta</button>
            </div>

            <hr class="border-light">

            <div>
                <p class="mb-1 fw-semibold text-uppercase small">Sessão</p>
                <button class="btn btn-outline-light w-100 text-start" data-bs-toggle="modal" data-bs-target="#confirmLogoutModal"><i class="fa-solid fa-person-walking-arrow-right"></i>  Sair</button>
            </div>

        </div>
    </div>

    <!-- Modal: Confirmar Exclusão de Conta -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-card-custom text-center">
                <div class="modal-body">
                    <h3 class="modal-title-custom">Excluir Conta</h3>
                    <p class="modal-text">Deseja mesmo excluir sua conta? Esta ação não pode ser desfeita.</p>

                    <div class="modal-actions d-flex justify-content-center gap-3">
                        <button type="button" class="buttonc cancel" style="background:#ccc;color:#333" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark"></i> Cancelar
                        </button>
                        <a href="<?= BASEURL ?>inc/excluir_conta.php" class="buttonc">
                            <i class="fa-solid fa-check"></i> Sim, excluir
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Confirmar Logout -->
    <div class="modal fade" id="confirmLogoutModal" tabindex="-1" aria-labelledby="confirmLogoutLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-card-custom text-center">
                <div class="modal-body">
                    <h3 class="modal-title-custom">Sair da Conta</h3>
                    <p class="modal-text">Deseja mesmo sair da sua conta?</p>

                    <div class="modal-actions d-flex justify-content-center gap-3">
                        <button type="button" class="buttonc cancel" style="background:#ccc;color:#333" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark"></i> Cancelar
                        </button>
                        <a href="<?= BASEURL ?>inc/logout.php" class="buttonc" >
                            <i class="fa-solid fa-check"></i> Sim, sair
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Scripts -->
<script src="<?php echo BASEURL; ?>js/swiper-bundle.min.js"></script>
<script src="<?php echo BASEURL; ?>js/bootstrap/bootstrap.bundle.min.js"></script>

<script>
    // Swiper
    var swiper = new Swiper(".slide-content", {
        slidesPerView: 3,
        spaceBetween: 30,
        slidesPerGroup: 3,
        loop: true,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });

    // Parallax
    document.addEventListener("DOMContentLoaded", function () {
        const parallax = document.querySelector('.section-parallax');
        if (!parallax) return;
        function parallaxScroll() {
            const scrollY = window.scrollY;
            const offset = scrollY * 0.1;
            parallax.style.backgroundPosition = `center ${offset}px`;
            requestAnimationFrame(parallaxScroll);
        }
        requestAnimationFrame(parallaxScroll);
    });

    // Logo e navbar scroll
    window.addEventListener('scroll', function () {
        const navbar = document.querySelector('#mainNavbar');
        const logo = document.querySelector('#logo');
        const sections = [
            { selector: '.section-cor3', logo: '<?php echo rtrim(BASEURL, '/'); ?>/img/logoc.png', navbarClass: '' },
            { selector: '.section-cor4', logo: '<?php echo rtrim(BASEURL, '/'); ?>/img/logob.png', navbarClass: 'scrolled' },
        ];
        const offset = 100;
        let found = false;
        for (let i = 0; i < sections.length; i++) {
            const section = document.querySelector(sections[i].selector);
            if (!section) continue;
            const rect = section.getBoundingClientRect();
            if (rect.top <= offset && rect.bottom >= offset) {
                if (sections[i].navbarClass) {
                    navbar.classList.add(sections[i].navbarClass);
                } else {
                    navbar.classList.remove('scrolled');
                }
                logo.src = sections[i].logo;
                found = true;
                break;
            }
        }
        if (!found) {
            navbar.classList.remove('scrolled');
            logo.src = '<?php echo rtrim(BASEURL, '/'); ?>/img/logoc.png';
        }
    });

    // Forçar fechamento do navbar collapse ao clicar em links ou botão
    document.addEventListener("DOMContentLoaded", function () {
        const navbarCollapse = document.querySelector('.navbar-collapse');
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navLinks = document.querySelectorAll('.nav-link');

        // Fechar ao clicar em link
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                const collapseInstance = bootstrap.Collapse.getInstance(navbarCollapse) || new bootstrap.Collapse(navbarCollapse);
                if (navbarCollapse.classList.contains('show')) {
                    collapseInstance.hide();
                }
            });
        });

        // Alternar ao clicar no botão
        navbarToggler.addEventListener('click', () => {
            const collapseInstance = bootstrap.Collapse.getOrCreateInstance(navbarCollapse);
            collapseInstance.toggle();
        });
    });
</script>
<style>
/* Ensure modal overlay sits above footer and page content */
.modal-overlay {
  position: fixed !important;
  top: 0; left: 0; right: 0; bottom: 0;
  display: none;
  align-items: center;
  justify-content: center;
  background: rgba(0,0,0,0.5);
  z-index: 2000;
}
.modal-overlay .modal-card {
  z-index: 2001;
  position: relative;
  background: #fff;
  color: #222;
  padding: 20px 22px;
  width: 100%;
  max-width: 520px;
  border-radius: 12px;
  box-shadow: 0 12px 30px rgba(0,0,0,0.25);
  pointer-events: auto;
}
.modal-overlay .modal-card h3 { margin-top: 0; color: #73213d; }
.modal-overlay .modal-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 14px; }
.modal-overlay .modal-actions .buttonc { padding: 10px 14px; border-radius: 8px; }
.modal-overlay .modal-actions form { margin: 0; }
</style>