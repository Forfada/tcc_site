<?php 
include("../config.php");
include(INIT);
session_start();
?>

<style>
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
    }
</style>

<div class="cadastro-container">
    <div class="cadastro-left">
        <div class="cadastro-card">
            <h2 class="mb-4 pc">Crie sua conta</h2>

            <!-- ALERT centralizado -->
            <?php include("alert.php"); ?>

            <form action="valida_cadastro.php" method="post">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control form2 nome" name="nome" id="nome" placeholder="Nome completo" required pattern="[A-Za-zÀ-ÿ\s]+">
                    <label for="nome">Nome completo</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control form2 telefone" name="numero" id="numero" placeholder="Telefone" required>
                    <label for="numero">Telefone</label>
                </div>

                <div class="form-floating mb-4">
                    <input type="password" class="form-control form2" name="senha" id="senha" placeholder="Senha" minlength="8" required>
                    <label for="senha">Senha</label>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-regis">
                        <i class="fa-solid fa-user-plus me-2"></i> Cadastrar
                    </button>
                </div>

                <p class="text-light">Já tem uma conta?</p>
                <a href="login.php" class="btn btn-outline-light w-100 mb-2">
                    <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Fazer login
                </a>

                <a href="<?php echo BASEURL; ?>index.php" class="btn btn-outline-light w-100">
                    <i class="fa-solid fa-house me-2"></i> Voltar para a página principal
                </a>
            </form>
        </div>
    </div>

    <div class="cadastro-right"></div>
</div>

<script src="../js/formatar.js"></script>
<script src="<?php echo BASEURL; ?>js/bootstrap/bootstrap.bundle.min.js"></script>
