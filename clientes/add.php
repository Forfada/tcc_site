<?php 
    include('functions.php'); 

    // somente administrador pode acessar a área de clientes
    if (!function_exists('is_admin') || !is_admin()) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['message'] = "Você não pode acessar essa funcionalidade.";
        $_SESSION['type'] = "danger";
        header("Location: " . BASEURL . "index.php");
        exit;
    }

    add();
    include(HEADER_TEMPLATE);
    include(INIT);
?>
<style>
    .form-group{
        --bs-gutter-x: 1rem !important;
        width: 100%;
    }
    label { font-size: 1rem; }
    @media (max-width: 767px) {
        .form { flex-direction: column; display: flex; gap: 0; }
        .form-group { width: 100%; margin-left: 0; margin-top: 0.5rem; }
        .d-flex { flex-wrap: wrap; }
        .col-md-5.d-flex { flex-direction: row; flex-wrap: nowrap; width: 100%; gap: 0.5rem; }
    }
</style>

<section class="clientes section-light section-cor3 py-5" id="clientes">
    <div class="container mt-5" style="margin-top: 6rem !important;">
        <h2 class="txt1 mb-1 text-center">Novo Cliente</h2>
        <p class="txt4 text-center mb-2">Cadastre um novo Cliente à sua Clínica.</p>
        <hr>

        <form action="add.php" method="post" id="formAdd">
            <div class="row justify-content-center">
                <div class="col-md-5 d-flex justify-content-center gap-2 form">
                    <div class="form-group mb-3">
                        <label for="cli_nome">Nome</label>
                        <input type="text" class="form-control nome" id="cli_nome" name="cli[cli_nome]" required>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-1 d-flex justify-content-center gap-2 form">
                    <div class="form-group mb-3">
                        <label for="cli_idade">Idade</label>
                        <input type="number" class="form-control text-center" id="cli_idade" name="cli[cli_idade]" required>
                    </div>
                </div>
                <div class="col-md-4 d-flex justify-content-center gap-2 form">
                    <div class="form-group mb-3">
                        <label for="cli_cpf">CPF</label>
                        <input type="text" class="form-control text-center cpf" id="cli_cpf" name="cli[cli_cpf]" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="cli_num">Número de Telefone</label>
                        <input type="tel" class="form-control text-center telefone" id="cli_num" name="cli[cli_num]" required>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-5 d-flex justify-content-center gap-2 form">
                    <div class="form-group mb-3">
                        <label for="cli_nasc">Data de Nascimento</label>
                        <input type="date" class="form-control text-center" id="cli_nasc" name="cli[cli_nasc]" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="cli_obs">Observação</label>
                        <textarea type="text" class="form-control" id="cli_obs" name="cli[cli_obs]" rows=1 required></textarea>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div id="actions" class="col-md-6 mt-3">
                    <div class="d-flex justify-content-center gap-2">
                        <button type="submit" class="buttonc"><i class="fa-solid fa-check"></i> Salvar</button>
                        <a href="index.php" class="buttona" style="text-decoration: none;"><i class="fa-solid fa-arrow-rotate-left"></i> Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Scripts -->
<script src="../js/formatar.js"></script>
<script>
document.getElementById('formAdd').addEventListener('submit', function() {
    const telInput = document.getElementById('cli_num');
    const cpfInput = document.getElementById('cli_cpf');

    // Remove formatação antes de enviar
    if(telInput) telInput.value = telInput.value.replace(/\D/g,'');
    if(cpfInput) cpfInput.value = cpfInput.value.replace(/\D/g,'');
});
</script>

<?php include(FOOTER_TEMPLATE); ?>
