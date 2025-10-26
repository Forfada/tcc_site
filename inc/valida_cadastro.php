<?php
include("../config.php");
require_once(DBAPI);
session_start();

try {
    $bd = open_database();
    $bd->exec("USE " . DB_NAME);

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if ($nome === '' || $email === '' || $senha === '') {
        throw new Exception("Todos os campos são obrigatórios.");
    }

    // valida formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Erro: Email inválido.";
        $_SESSION['type'] = "danger";
        header("Location: cadastro.php");
        exit();
    }

    // Verifica duplicidade
    $stmt = $bd->prepare("SELECT COUNT(*) FROM usuarios WHERE u_email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->fetchColumn() > 0) {
        $_SESSION['message'] = "Erro: Este email já está cadastrado.";
        $_SESSION['type'] = "danger";
        header("Location: cadastro.php");
        exit();
    }

    // Gerar código de verificação e salvar em sessão temporária
    $codigo = rand(100000, 999999);
    $_SESSION['cadastro'] = [
        'nome' => $nome,
        'email' => $email,
        'senha' => $senha,
        'codigo' => $codigo
    ];

    // Enviar código por email usando helper (PHPMailer via inc/mail.php)
    require_once(ABSPATH . 'inc/mail.php');
    $subject = 'Seu código de verificação';
    $body = "<p>Olá " . htmlspecialchars($nome, ENT_QUOTES) . ",</p>" .
            "<p>Seu código de verificação é: <strong>" . $codigo . "</strong></p>" .
            "<p>Se você não solicitou este cadastro, ignore esta mensagem.</p>";
    $altBody = "Olá {$nome}\n\nSeu código de verificação é: {$codigo}\n\nSe você não solicitou este cadastro, ignore esta mensagem.";

    $sent = false;
    try {
        $sent = send_email($email, $subject, $body, $altBody);
    } catch (Exception $e) {
        $sent = false;
    }

    if ($sent) {
        $_SESSION['message'] = "Código de verificação enviado para o seu email.";
    } else {
        // fallback para ambiente de desenvolvimento/testes: mostrar código na sessão (não seguro em produção)
        $_SESSION['message'] = "Código enviado para o email $email (simulado: $codigo)";
    }
    $_SESSION['type'] = 'info';

    header('Location: verificar_cadastro.php');
    exit();

} catch (Exception $e) {
    $_SESSION['message'] = "Erro: " . $e->getMessage();
    $_SESSION['type'] = "danger";
    header("Location: cadastro.php");
    exit();
}
?><?php
require_once(ABSPATH . 'inc/mail.php');
$sent = send_email($email, $subject, $body, $altBody);
if (!$sent) {
    // fallback behavior, por ex.: guardar o token na sessão para testes
    $_SESSION['message'] = "E-mail não enviado via SMTP; verifique a configuração (simulated).";
    $_SESSION['type'] = 'info';
}
?>
