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
        // concluir login: setar sessão e, se remember, setar cookie e token
        $_SESSION['id'] = $login['id'];
        $_SESSION['nome'] = $login['nome'];
        $_SESSION['user'] = $login['email'];
        $_SESSION['foto'] = $login['foto'];

        // se remember foi solicitado, gerar token e salvar no BD/cookie
        if (!empty($login['remember'])) {
            $token = bin2hex(random_bytes(32));
            try {
                $bd = open_database();
                $bd->exec("USE " . DB_NAME);
                $stmt = $bd->prepare("UPDATE usuarios SET auth_token = :token WHERE id = :id");
                $stmt->execute([':token' => $token, ':id' => $login['id']]);
                require_once('cookie_handler.php');
                CookieHandler::setLoginCookie($login['id'], $token);
                close_database($bd);
            } catch (Exception $e) {
                // se falhar, continuar sem cookie
            }
        }

        unset($_SESSION['login_2fa']);
        $_SESSION['message'] = "Bem-vindo(a) " . $_SESSION['nome'] . "!";
        $_SESSION['type'] = 'info';
        header('Location: ' . BASEURL . 'index.php');
        exit;
    } else {
        $_SESSION['message'] = "Código incorreto.";
        $_SESSION['type'] = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar Login</title>
    <link rel="stylesheet" href="<?php echo BASEURL; ?>css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>css/fontawesome/all.min.css">
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
    </style>
</head>
<body>
<div class="verification-container">
    <div class="verification-card">
        <h3>Verificação de Login</h3>

        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['type']; ?>"><?php echo $_SESSION['message']; ?></div>
        <?php endif; ?>

        <p>Enviamos um código para <strong><?php echo htmlspecialchars($login['email']); ?></strong>. Digite-o abaixo para concluir o acesso.</p>

        <form method="post">
            <div class="mb-3">
                <input type="text" name="codigo" class="form-control" placeholder="Código" required>
            </div>
            <div class="d-grid">
                <button class="btn btn-primary">Verificar</button>
            </div>
        </form>

        <p class="mt-3"><a href="login.php">Voltar</a></p>
    </div>
</div>
</body>
</html>
