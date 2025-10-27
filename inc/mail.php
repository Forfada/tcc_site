<?php
// inc/mail.php
// ensure session is started (use session_status guard to avoid warnings)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Try to include Composer autoloader so PHPMailer classes are available
$autoloadA = defined('ABSPATH') ? ABSPATH . 'vendor/autoload.php' : __DIR__ . '/../vendor/autoload.php';
$autoloadB = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadA)) {
    require_once $autoloadA;
} elseif (file_exists($autoloadB)) {
    require_once $autoloadB;
}

// Import PHPMailer classes at the top of the file
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * send_email
 * @param string $to
 * @param string $subject
 * @param string $body HTML body preferred
 * @param string $altBody plain-text fallback
 * @return bool true if sent (or simulated), false otherwise
 */
function send_email($to, $subject, $body, $altBody = '') {
    // Basic validation
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    try {
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';

        // Recipients
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);

    // Enviar o email
    return $mail->send();

    } catch (Exception $e) {
        // Log error if needed
        error_log("Erro ao enviar email via PHPMailer: " . $e->getMessage());

        // Fallback para mail() nativo
        $headers = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        return @mail($to, $subject, $body, $headers);
    }
}