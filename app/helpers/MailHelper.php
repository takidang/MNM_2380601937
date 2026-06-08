<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'app/helpers/phpmailer/Exception.php';
require_once 'app/helpers/phpmailer/PHPMailer.php';
require_once 'app/helpers/phpmailer/SMTP.php';

class MailHelper
{
    const DEV_MODE   = false;
    const FROM_EMAIL = 'taidang87555@gmail.com';
    const FROM_NAME  = 'TECH-SPECTRUM';
    const STORAGE    = 'storage/mails/';

    public static function send($to, $subject, $htmlBody)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'taidang87555@gmail.com';
            $mail->Password   = 'mssjcrgfcokjkajp';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(self::FROM_EMAIL, self::FROM_NAME);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Lưu file để debug nếu gửi thất bại
            if (!is_dir(self::STORAGE)) {
                @mkdir(self::STORAGE, 0755, true);
            }
            $file = self::STORAGE . date('Ymd_His') . '_failed_' . preg_replace('/[^a-z0-9]/i', '_', $to) . '.html';
            @file_put_contents($file, "<!-- ERROR: {$mail->ErrorInfo} -->\n" . $htmlBody);
            return false;
        }
    }

    public static function baseUrl()
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host;
    }
}
