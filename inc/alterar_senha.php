<?php
include("../config.php");
include(INIT);
include(DBAPI);
require_once(ABSPATH . 'inc/mail.php'); // helper
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (!empty($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['message'] = "Email inválido.";
            $_SESSION['type'] = "warning";
            header("Location: alterar_senha.php");
            exit;
        }

            try {
                $conn = open_database();
                $stmt = $conn->prepare("SELECT id, u_user FROM usuarios WHERE u_email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Gerar código de verificação
                    $codigo = rand(100000, 999999);
                    $_SESSION['reset_senha'] = [
                        'email' => $email,
                        'codigo' => $codigo,
                        'user_id' => $user['id'],
                        'expires' => time() + 3600 // 1 hora para usar o código
                    ];

                    // Enviar email com o código
                    $subject = 'Código de Verificação - Alteração de Senha';
                    $body = '<p>Olá ' . htmlspecialchars($user['u_user']) . ',</p>';
                    $body .= '<p>Você solicitou a alteração de sua senha. Use o código abaixo para confirmar:</p>';
                    $body .= '<h2 style="font-size: 24px; background: #f8f9fa; padding: 10px; text-align: center;">' . $codigo . '</h2>';
                    $body .= '<p>Este código é válido por 1 hora.</p>';
                    $body .= '<p>Se você não solicitou esta alteração, ignore este e-mail.</p>';

                    $sent = send_email($email, $subject, $body, strip_tags($body));
                    if (!$sent) {
                        $_SESSION['message'] = "Erro ao enviar o email. Tente novamente.";
                        $_SESSION['type'] = "danger";
                        header("Location: alterar_senha.php");
                    } else {
                        $_SESSION['message'] = "Enviamos um código de verificação para seu email. Caso não tenha recebido, verifique sua caixa de spam.";
                        $_SESSION['type'] = "info";
                        header("Location: verificar_reset_senha.php");
                    }
                    exit;
                } else {
                    $_SESSION['message'] = "Email não encontrado.";
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

<!-- HTML da página -->
<div class="senha-container">
    <div class="senha-left">
        <div class="cadastro-card">
            <h2 class="mb-4 pc">Alterar Senha</h2>

            <?php include("alert.php"); ?>

            <form action="alterar_senha.php" method="POST">
                <div class="form-floating mb-3">
                    <input type="email" class="form-control form2" name="email" id="email" placeholder="Seu email" required>
                    <label for="email">Email</label>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-regis">
                        <i class="fa-solid fa-paper-plane me-2"></i> Enviar Código
                    </button>
                </div>

                <a href="login.php" class="btn btn-outline-light w-100 mb-2">
                    <i class="fa-solid fa-arrow-left me-2"></i> Voltar para o login
                </a>
                <a href="<?php echo BASEURL; ?>" class="btn btn-outline-light w-100">
                    <i class="fa-solid fa-house me-2"></i> Voltar para a página principal
                </a>
            </form>
        </div>
    </div>

    <div class="senha-right"></div>
</div>

<script src="../js/formatar.js"></script>
<script src="<?php echo BASEURL; ?>js/bootstrap/bootstrap.bundle.min.js"></script>
<style>
    .senha-container {
        width: 100%;
        min-height: 100vh;
        display: flex;
        align-items: stretch;
    }

    .senha-left {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        background: linear-gradient(135deg, #73213d, #9c2952);
    }

    .senha-right {
        flex: 1;
        background: url('../img/altsen2.jpg') center/cover no-repeat;
    }

    @media (max-width: 992px) {
        .senha-right {
            display: none;
        }
        
        .senha-left {
            flex: 1;
        }
        
        .cadastro-card {
            max-width: 450px;
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .senha-left {
            padding: 1rem;
        }
    }
</style>
