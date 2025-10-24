<?php
include("../config.php");
include(INIT);
include(DBAPI);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se existe processo de reset de senha em andamento
if (empty($_SESSION['reset_senha'])) {
    $_SESSION['message'] = "Nenhum processo de alteração de senha em andamento.";
    $_SESSION['type'] = 'warning';
    header('Location: alterar_senha.php');
    exit;
}

$reset = $_SESSION['reset_senha'];

// Verifica se o código expirou
if (time() > $reset['expires']) {
    unset($_SESSION['reset_senha']);
    $_SESSION['message'] = "O código expirou. Solicite um novo código.";
    $_SESSION['type'] = 'warning';
    header('Location: alterar_senha.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo'] ?? '');
    $nova_senha = trim($_POST['nova_senha'] ?? '');

    if (empty($codigo) || empty($nova_senha)) {
        $_SESSION['message'] = "Preencha todos os campos.";
        $_SESSION['type'] = 'warning';
        header('Location: verificar_reset_senha.php');
        exit;
    }

    if ($codigo != $reset['codigo']) {
        $_SESSION['message'] = "Código incorreto.";
        $_SESSION['type'] = 'danger';
        header('Location: verificar_reset_senha.php');
        exit;
    }

    try {
        $conn = open_database();
        // Hash da nova senha usando a função cri()
        $senha_hash = cri($nova_senha);
        
        $stmt = $conn->prepare("UPDATE usuarios SET u_senha = :senha WHERE id = :id");
        $stmt->execute([
            ':senha' => $senha_hash,
            ':id' => $reset['user_id']
        ]);

        // Limpa a sessão de reset
        unset($_SESSION['reset_senha']);
        
        $_SESSION['message'] = "Senha alterada com sucesso!";
        $_SESSION['type'] = 'success';
        header('Location: login.php');
        exit;

    } catch (Exception $e) {
        $_SESSION['message'] = "Erro ao alterar senha: " . $e->getMessage();
        $_SESSION['type'] = 'danger';
        header('Location: verificar_reset_senha.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Verificar Código - Reset de Senha</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>css/style.css">
</head>
<body>

<div class="senha-container">
    <div class="senha-left">
        <div class="cadastro-card">
            <h2 class="mb-4 pc">Verificar Código</h2>

            <?php include("alert.php"); ?>

            <form action="verificar_reset_senha.php" method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control form2" name="codigo" id="codigo" 
                           placeholder="Código de verificação" required pattern="[0-9]{6}" 
                           title="Digite o código de 6 dígitos">
                    <label for="codigo">Código de Verificação</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control form2" name="nova_senha" 
                           id="nova_senha" placeholder="Nova senha" minlength="8" required>
                    <label for="nova_senha">Nova Senha</label>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-regis">
                        <i class="fa-solid fa-key me-2"></i> Confirmar Nova Senha
                    </button>
                </div>

                <a href="alterar_senha.php" class="btn btn-outline-light w-100 mb-2">
                    <i class="fa-solid fa-arrow-left me-2"></i> Voltar
                </a>
            </form>
        </div>
    </div>
    <div class="senha-right"></div>
</div>

<script src="<?php echo BASEURL; ?>js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>