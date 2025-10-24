<?php
include("../config.php");
require_once(DBAPI);
session_start();

$token = trim($_GET['token'] ?? $_POST['token'] ?? '');

if (empty($token)) {
    $_SESSION['message'] = "Token inválido.";
    $_SESSION['type'] = 'danger';
    header('Location: alterar_senha.php');
    exit;
}

try {
    $bd = open_database();
    $bd->exec("USE " . DB_NAME);

    $stmt = $bd->prepare("SELECT pr.id as pr_id, pr.user_id, pr.new_password_hash, pr.expires_at, u.u_email, u.u_user FROM password_resets pr JOIN usuarios u ON pr.user_id = u.id WHERE pr.token = :token LIMIT 1");
    $stmt->execute([':token' => $token]);

    if ($stmt->rowCount() === 0) {
        $_SESSION['message'] = "Token inválido ou já usado.";
        $_SESSION['type'] = 'danger';
        header('Location: alterar_senha.php');
        exit;
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $expires = strtotime($row['expires_at']);
    if ($expires < time()) {
        // expired: remove it
        $del = $bd->prepare("DELETE FROM password_resets WHERE id = :id");
        $del->execute([':id' => $row['pr_id']]);
        $_SESSION['message'] = "Token expirado. Solicite novamente a alteração de senha.";
        $_SESSION['type'] = 'warning';
        header('Location: alterar_senha.php');
        exit;
    }

    // OK: aplicar nova senha
    $update = $bd->prepare("UPDATE usuarios SET u_senha = :hash WHERE id = :uid");
    $update->execute([':hash' => $row['new_password_hash'], ':uid' => $row['user_id']]);

    // remover o reset
    $del = $bd->prepare("DELETE FROM password_resets WHERE id = :id");
    $del->execute([':id' => $row['pr_id']]);

    $_SESSION['message'] = "Senha alterada com sucesso. Faça login com sua nova senha.";
    $_SESSION['type'] = 'success';
    header('Location: login.php');
    exit;

} catch (Exception $e) {
    $_SESSION['message'] = "Erro: " . $e->getMessage();
    $_SESSION['type'] = 'danger';
    header('Location: alterar_senha.php');
    exit;
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Verificar alteração de senha</title>
    <link rel="stylesheet" href="<?php echo BASEURL; ?>css/bootstrap/bootstrap.min.css">
</head>
<body>
<div class="container" style="max-width:480px; margin-top:6rem;">
    <h3>Verificação de Alteração de Senha</h3>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['type']; ?>"><?php echo $_SESSION['message']; ?></div>
    <?php endif; ?>

    <p>Enviamos um código para <strong><?php echo htmlspecialchars($req['email']); ?></strong>. Digite-o abaixo para confirmar a alteração da senha.</p>

    <form method="post">
        <div class="mb-3">
            <input type="text" name="codigo" class="form-control" placeholder="Código" required>
        </div>
        <div class="d-grid">
            <button class="btn btn-primary">Confirmar</button>
        </div>
    </form>

    <p class="mt-3"><a href="alterar_senha.php">Voltar</a></p>
</div>
</body>
</html>
