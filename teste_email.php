<?php
require_once 'config.php';
require_once ABSPATH . 'inc/mail.php';

// Coloque aqui o email que você verificou no Mailgun
$email_teste = "pedrofunceca@gmail.com";

$subject = "Teste de Email - Lunaris";
$body = "
<h1>Teste de Email do Sistema Lunaris</h1>
<p>Se você está vendo este email, a configuração do Mailgun está funcionando!</p>
<p>Data/Hora do teste: " . date('d/m/Y H:i:s') . "</p>
";

try {
    if (send_email($email_teste, $subject, $body)) {
        echo "✅ Email enviado com sucesso! Verifique sua caixa de entrada.";
    } else {
        echo "❌ Erro ao enviar email. Verifique os logs do PHP para mais detalhes.";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
    error_log("Erro no teste de email: " . $e->getMessage());
}