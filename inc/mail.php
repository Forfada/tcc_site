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

        // Server settings — only configure SMTP if constants are defined
        $mail->CharSet = 'UTF-8';
        if (defined('SMTP_HOST') && SMTP_HOST) {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            if (defined('SMTP_USER')) $mail->Username = SMTP_USER;
            if (defined('SMTP_PASS')) $mail->Password = SMTP_PASS;
            // SMTPSecure: allow 'tls' or 'ssl' strings in config, default to STARTTLS
            if (defined('SMTP_SECURE') && SMTP_SECURE) {
                $secure = strtolower(SMTP_SECURE);
                if ($secure === 'ssl') {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                } else {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                }
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mail->Port = defined('SMTP_PORT') ? SMTP_PORT : 587;
        } else {
            // No SMTP configured — PHPMailer will fallback to mail() when send() is called
            // We intentionally avoid calling isSMTP() so PHPMailer uses the mailer selected at runtime.
            error_log("mail.php: SMTP_HOST not defined — using mail() fallback for sending to {$to}");
        }

    // Recipients
    // Use configured MAIL_FROM / MAIL_FROM_NAME when available, otherwise safe defaults
    $fromEmail = defined('MAIL_FROM') && filter_var(MAIL_FROM, FILTER_VALIDATE_EMAIL) ? MAIL_FROM : 'no-reply@localhost';
    $fromName = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'Lunaris';
    $mail->setFrom($fromEmail, $fromName);
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
    $headers = "From: " . (defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : $fromName) . " <" . (defined('MAIL_FROM') ? MAIL_FROM : $fromEmail) . ">\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        return @mail($to, $subject, $body, $headers);
    }
}