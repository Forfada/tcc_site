<!DOCTYPE html>
<html lang="pt-BR">
    
<?php include(INIT); ?>

<body data-bs-spy="scroll" data-bs-target="#mainNavbar" data-bs-offset="80" tabindex="0">

<?php if (!isset($_SESSION)) session_start(); ?>

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
                        <a class="nav-link ms-4 me-4" href="<?php echo BASEURL; ?>#agendamento">Agendamento</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ms-4 me-4" href="<?php echo BASEURL; ?>#contato">Contato</a>
                    </li>
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
                <a href="#" class="btn btn-outline-light w-100 text-start"><i class="fa-solid fa-user-clock"></i>  Histórico</a>
                <button class="btn btn-outline-warning w-100 text-start mt-2" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="fa-solid fa-trash-can"></i>  Excluir conta</button>
            </div>

            <hr class="border-light">

            <div>
                <p class="mb-1 fw-semibold text-uppercase small">Sessão</p>
                <button class="btn btn-outline-danger w-100 text-start" data-bs-toggle="modal" data-bs-target="#confirmLogoutModal"><i class="fa-solid fa-person-walking-arrow-right"></i>  Sair</button>
            </div>

        </div>
    </div>

    <!-- Modal: Confirmar Exclusão de Conta -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="confirmDeleteLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    Deseja mesmo excluir sua conta?
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="<?= BASEURL ?>inc/excluir_conta.php" class="btn btn-danger">Sim, excluir</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Confirmar Logout -->
    <div class="modal fade" id="confirmLogoutModal" tabindex="-1" aria-labelledby="confirmLogoutLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="confirmLogoutLabel">Confirmar Logout</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    Deseja mesmo sair da sua conta?
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="<?= BASEURL ?>inc/logout.php" class="btn btn-danger">Sim, sair</a>
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
            if (section) {
                const rect = section.getBoundingClientRect();
                if (rect.top - offset <= 0 && rect.bottom > offset) {
                    logo.src = sections[i].logo;
                    if (sections[i].navbarClass) {
                        navbar.classList.add(sections[i].navbarClass);
                    } else {
                        navbar.classList.remove('scrolled');
                    }
                    found = true;
                    break;
                }
            }
        }
        if (!found) {
            logo.src = '<?php echo rtrim(BASEURL, '/'); ?>/img/logoc.png';
            navbar.classList.remove('scrolled');
        }
    });

    // Forçar fechamento do navbar collapse ao clicar em links ou botão
    document.addEventListener("DOMContentLoaded", function () {
        const navbarCollapse = document.querySelector('.navbar-collapse');
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (navbarCollapse.classList.contains('show')) {
                    navbarToggler.click();
                }
            });
        });
    });

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
            logo.src = 'img/logoc.png';
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

