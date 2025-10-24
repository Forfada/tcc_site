<?php
include("../config.php");
require_once(DBAPI);
require_once("cookie_handler.php");
session_start();
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

        // enviar email com o código (simulação se mail() não funcionar)
        $subject = 'Código de acesso';
        $message = "Olá {$dados['u_user']},\n\nSeu código de acesso é: {$codigo}\n\nSe não foi você, ignore esta mensagem.";
        $headers = 'From: no-reply@localhost' . "\r\n" . 'Content-Type: text/plain; charset=UTF-8';
        $mail_sent = false;
        try {
            if (function_exists('mail')) $mail_sent = mail($dados['u_email'], $subject, $message, $headers);
        } catch (Exception $e) { $mail_sent = false; }

        $sent = send_email($dados['u_email'], $subject, $message, $headers);
        if (!$sent) {
            // fallback behavior, por ex.: guardar o token na sessão para testes
            $_SESSION['message'] = "E-mail não enviado via SMTP; verifique a configuração (simulated).";
            $_SESSION['type'] = 'info';
        } else {
            $_SESSION['message'] = "Código enviado para o seu email.";
        }
        $_SESSION['type'] = 'info';

        header('Location: verificar_login.php');
        exit;
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
