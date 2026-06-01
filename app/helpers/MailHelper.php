<?php
/**
 * MailHelper
 * ----------
 * Gửi email đơn giản. Trên hosting thật sẽ dùng hàm mail() của PHP.
 * Để TIỆN KIỂM THỬ ở local (Laragon thường không gửi được mail thật),
 * mọi email cũng được lưu thành file HTML trong thư mục storage/mails/
 * để bạn mở ra và bấm vào link xác thực / đặt lại mật khẩu.
 */
class MailHelper
{
    /** Bật ở môi trường dev: trả link trực tiếp cho người dùng xem trên màn hình. */
    const DEV_MODE = true;

    const FROM = 'no-reply@techspectrum.test';
    const STORAGE = 'storage/mails/';

    /**
     * @return bool Đã cố gửi (qua mail() hoặc lưu file) thành công hay không.
     */
    public static function send($to, $subject, $htmlBody)
    {
        // 1) Cố gửi mail thật (có thể không hoạt động ở local)
        $sent = false;
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . self::FROM . "\r\n";
        if (function_exists('mail')) {
            $sent = @mail($to, $subject, $htmlBody, $headers);
        }

        // 2) Luôn lưu một bản ra file để kiểm thử
        if (!is_dir(self::STORAGE)) {
            @mkdir(self::STORAGE, 0755, true);
        }
        $file = self::STORAGE . date('Ymd_His') . '_' . preg_replace('/[^a-z0-9]/i', '_', $to) . '.html';
        @file_put_contents(
            $file,
            "<!-- To: {$to} | Subject: {$subject} | sent_via_mail(): " . ($sent ? 'yes' : 'no') . " -->\n" . $htmlBody
        );

        return $sent || file_exists($file);
    }

    /** Tạo URL tuyệt đối dựa trên domain hiện tại. */
    public static function baseUrl()
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host;
    }
}
