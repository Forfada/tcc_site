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

<div class="login-container">
    <div class="login-left">
        <div class="login-card">
            <h2 class="mb-4 pz">Bem-vinda à Lunaris</h2>

            <!-- ALERT centralizado -->
            <?php include("alert.php"); ?>

            <form action="valida.php" method="post">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control form1 telefone" name="login" id="log" placeholder="Telefone" required>
                    <label for="log">Telefone</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control form1" name="password" placeholder="Senha" minlength="8" required>
                    <label for="pass">Senha</label>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                    <label class="form-check-label" for="remember">Manter conectado</label>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-conect">
                        <i class="fa-solid fa-user-check me-2"></i> Conectar
                    </button>
                </div>

                <div class="mt-3 mb-2">
                    <a href="alterar_senha.php" class="text-decoration-none text-blue">
                        Esqueceu sua senha? Redefinir
                    </a>
                </div>

                <p class="text-muted">Não tem uma conta?</p>
                <a href="cadastro.php" class="btn btn-outline-dark w-100 mb-2">
                    <i class="fa-solid fa-user-plus me-2"></i> Criar conta
                </a>
                <a href="<?php echo BASEURL; ?>index.php" class="btn btn-outline-dark w-100">
                    <i class="fa-solid fa-house me-2"></i> Voltar para a página principal
                </a>
            </form>
        </div>
    </div>

    <div class="login-right"></div>
</div>

<script src="../js/formatar.js"></script>
<script src="<?php echo BASEURL; ?>js/bootstrap/bootstrap.bundle.min.js"></script>
