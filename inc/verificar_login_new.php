<?php
include("../config.php");
require_once(DBAPI);
session_start();

if (empty($_SESSION['login_2fa'])) {
    $_SESSION['message'] = "Nenhum processo de login em andamento.";
    $_SESSION['type'] = 'warning';
    header('Location: login.php');
    exit;
}

$login = &$_SESSION['login_2fa'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo'] ?? '');

    if ($codigo == $login['codigo']) {
        $_SESSION['id'] = $login['dados']['id'];
        $_SESSION['nome'] = $login['dados']['u_user'];
        $_SESSION['user'] = $login['dados']['u_email'];
        $_SESSION['foto'] = $login['dados']['foto'];

        if ($login['lembrar']) {
            require_once("cookie_handler.php");
            $token = bin2hex(random_bytes(32));
            try {
                $db = open_database();
                $stmt = $db->prepare("UPDATE usuarios SET auth_token = :token WHERE id = :id");
                $stmt->execute([':token' => $token, ':id' => $login['dados']['id']]);
                CookieHandler::setLoginCookie($login['dados']['id'], $token);
            } catch (Exception $e) {
                // Falha ao salvar token - continuar sem "lembrar"
            }
        }

        unset($_SESSION['login_2fa']);
        header('Location: ' . BASEURL);
        exit;
    } else {
        $_SESSION['message'] = "Código inválido.";
        $_SESSION['type'] = 'danger';
    }
}

$pageTitle = 'Verificar Login';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle; ?> - Lunaris</title>
    <link rel="stylesheet" href="<?php echo BASEURL; ?>css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>css/fontawesome/all.min.css">
    <link rel="icon" type="image/x-icon" href="<?php echo BASEURL; ?>img/icone.png">
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #73213d, #9c2952);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .verification-container {
            width: 100%;
            max-width: 450px;
            margin: 20px;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .verification-title {
            color: #73213d;
            text-align: center;
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
        }
        .verification-input {
            text-align: center;
            letter-spacing: 0.5em;
            font-size: 1.5rem;
        }
        .verification-message {
            text-align: center;
            margin-bottom: 2rem;
        }
        .btn-verificar {
            width: 100%;
            padding: 12px;
            background: #73213d;
            color: white;
            border: none;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-verificar:hover {
            background: #9c2952;
            transform: translateY(-2px);
            color: white;
        }
        .back-link {
            display: block;
            text-align: center;
            color: #73213d;
            text-decoration: none;
            margin-top: 1rem;
            transition: color 0.3s ease;
        }
        .back-link:hover {
            color: #9c2952;
        }
        @media (max-width: 480px) {
            .verification-container {
                margin: 15px;
                padding: 1.5rem;
            }
            .verification-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <h3 class="verification-title">Verificação de Login</h3>

        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['type']; ?>"><?php echo $_SESSION['message']; ?></div>
        <?php endif; ?>

        <p class="verification-message">
            Enviamos um código para <strong><?php echo htmlspecialchars($login['email']); ?></strong>.<br>
            Digite-o abaixo para concluir o acesso. Caso não tenha recebido, verifique sua caixa de spam.
        </p>

        <form method="post" class="needs-validation" novalidate>
            <div class="form-group mb-4">
                <input type="text" name="codigo" class="form-control verification-input" maxlength="6" pattern="[0-9]{6}" placeholder="000000" required autocomplete="off">
                <div class="invalid-feedback">
                    Por favor, digite o código de 6 dígitos.
                </div>
            </div>

            <button type="submit" class="btn btn-verificar">Verificar e Acessar</button>
        </form>

        <a href="login.php" class="back-link">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <script src="<?php echo BASEURL; ?>js/bootstrap/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

        // Auto-focus and format code input
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.querySelector('input[name="codigo"]');
            input.focus();
            
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
            });
        });
    </script>
</body>
</html>