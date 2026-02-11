<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendEnquiryEmail($toEmail, $toName, $subject, $htmlBody, $textBody = '') {
    $config = require __DIR__ . '/smtp_config.php';

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = $config['encryption'];
        $mail->Port = (int)$config['port'];

        $mail->setFrom($config['from_address'], $config['from_name']);
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = $textBody ?: strip_tags($htmlBody);

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}

function sendEnquiryEmailWithAttachments($toEmail, $toName, $subject, $htmlBody, $textBody = '', array $attachments = []) {
    $config = require __DIR__ . '/smtp_config.php';

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = $config['encryption'];
        $mail->Port = (int)$config['port'];

        $mail->setFrom($config['from_address'], $config['from_name']);
        $mail->addAddress($toEmail, $toName);

        foreach ($attachments as $attachment) {
            $path = $attachment['path'] ?? '';
            $name = $attachment['name'] ?? '';
            if (!is_string($path) || $path === '' || !file_exists($path)) {
                continue;
            }
            if (is_string($name) && $name !== '') {
                $mail->addAttachment($path, $name);
            } else {
                $mail->addAttachment($path);
            }
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = $textBody ?: strip_tags($htmlBody);

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
