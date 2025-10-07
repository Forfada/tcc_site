<?php
include("../config.php");
include(INIT);
include(DBAPI);
session_start();
clear_messages();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = trim($_POST['numero'] ?? '');
    $nova_senha = trim($_POST['nova_senha'] ?? '');

    if (!empty($numero) && !empty($nova_senha)) {
        $senha_criptografada = cri($nova_senha);

        try {
            $conn = open_database();

            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE u_num = :numero");
            $stmt->bindParam(':numero', $numero);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $update = $conn->prepare("UPDATE usuarios SET u_senha = :senha WHERE u_num = :numero");
                $update->bindParam(':senha', $senha_criptografada);
                $update->bindParam(':numero', $numero);
                $update->execute();

                $_SESSION['message'] = "Senha alterada com sucesso!";
                $_SESSION['type'] = "success";

                // Redireciona de acordo com o login
                if (isset($_SESSION['nome'])) {
                    header("Location: ../index.php");
                } else {
                    header("Location: login.php");
                }
                exit;
            } else {
                $_SESSION['message'] = "Número não encontrado.";
                $_SESSION['type'] = "warning";
                header("Location: alterar_senha.php");
                exit;
            }

        } catch (PDOException $e) {
            $_SESSION['message'] = "Erro no banco: " . $e->getMessage();
            $_SESSION['type'] = "danger";
            header("Location: alterar_senha.php");
            exit;
        }
    } else {
        $_SESSION['message'] = "Preencha todos os campos.";
        $_SESSION['type'] = "warning";
        header("Location: alterar_senha.php");
        exit;
    }
}
?>

    <div class="senha-container">
        <div class="senha-left">
            <div class="cadastro-card">
                <h2 class="mb-4 pc">Alterar Senha</h2>

            <?php if (!empty($_SESSION['message'])) : ?>
                <div class="alert alert-<?php echo $_SESSION['type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php clear_messages(); ?>
            <?php endif; ?>

            <form action="alterar_senha.php" method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control form2 telefone" name="numero" id="numero" placeholder="Seu número" required>
                    <label for="numero">Número</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control form2" name="nova_senha" id="nova_senha" placeholder="Nova senha" minlength="8" required>
                    <label for="nova_senha">Nova Senha</label>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-regis">
                        <i class="fa-solid fa-lock me-2"></i> Alterar Senha
                    </button>
                </div>

                <?php if (!isset($_SESSION['nome'])): ?>
                    <a href="login.php" class="btn btn-outline-light w-100 mb-2">
                        <i class="fa-solid fa-arrow-left me-2"></i> Voltar para o login
                    </a>
                <?php endif; ?>

                <a href="<?php echo BASEURL; ?>" class="btn btn-outline-light w-100">
                    <i class="fa-solid fa-house me-2"></i> Voltar para a página principal
                </a>
            </form>
        </div>
    </div>

    <div class="/senha-right"></div>
</div>
<script src="../js/formatar.js"></script>