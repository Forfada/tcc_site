<?php
include("../config.php");
require_once(DBAPI);
require_once("cookie_handler.php");
// start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(ABSPATH . 'inc/mail.php');

try {
    $bd = open_database();
    $bd->exec("USE " . DB_NAME);

    $usuario = trim($_POST['email'] ?? '');
    $senha = trim($_POST['password'] ?? '');

    if (empty($usuario) || empty($senha)) {
        throw new Exception("Preencha todos os campos.");
    }

    $senha = cri($senha);

    $stmt = $bd->prepare("SELECT id, u_user, u_email, u_senha, foto FROM usuarios WHERE u_email = :usuario AND u_senha = :senha");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':senha', $senha);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // usuário e senha corretos -> gerar código de verificação enviado por email
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        $codigo = rand(100000, 999999);

        // salvar dados temporariamente na sessão para verificação
        $_SESSION['login_2fa'] = [
            'id' => $dados['id'],
            'nome' => $dados['u_user'],
            'email' => $dados['u_email'],
            'foto' => $dados['foto'],
            'senha_hash' => $dados['u_senha'],
            'codigo' => $codigo,
            'remember' => (isset($_POST['remember']) && $_POST['remember'] == '1') ? 1 : 0
        ];

        // enviar email com o código usando helper (PHPMailer)
        $subject = 'Código de acesso';
        $message = "Olá {$dados['u_user']},\n\nSeu código de acesso é: {$codigo}\n\nSe não foi você, ignore esta mensagem.";
        $alt = strip_tags($message);

        $sent = false;
        try {
            $sent = send_email($dados['u_email'], $subject, $message, $alt);
        } catch (Exception $e) {
            $sent = false;
            error_log("send_email() exception: " . $e->getMessage());
        }

        if ($sent) {
            $_SESSION['message'] = "Código enviado para o seu email.";
            $_SESSION['type'] = 'info';
            header('Location: verificar_login.php');
            exit;
        } else {
            // Em produção, não expor detalhes operacionais. Registrar erro em log e voltar ao login.
            error_log("send_email() falhou ao enviar código de login para: {$dados['u_email']}");
            $_SESSION['message'] = "Não foi possível enviar o e-mail de verificação. Tente novamente mais tarde.";
            $_SESSION['type'] = 'danger';
            header('Location: login.php');
            exit;
        }
    } else {
        $_SESSION['message'] = "Usuário ou senha incorretos.";
        $_SESSION['type'] = "danger";
        header("Location: login.php");
        exit;
    }

} catch (Exception $e) {
    $_SESSION['message'] = "Erro: " . $e->getMessage();
    $_SESSION['type'] = "danger";
    header("Location: login.php");
    exit;
}
?>
