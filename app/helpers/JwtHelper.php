<?php

class JwtHelper
{
    private static $secret = 'TECH_SPECTRUM_JWT_SECRET_KEY_2024';
    private static $algo   = 'HS256';
    private static $expiry = 7200; // 2 giờ

    public static function generate(int $userId, string $role, string $username): string
    {
        $header  = self::base64url(json_encode(['alg' => self::$algo, 'typ' => 'JWT']));
        $payload = self::base64url(json_encode([
            'sub'      => $userId,
            'username' => $username,
            'role'     => $role,
            'iat'      => time(),
            'exp'      => time() + self::$expiry,
        ]));
        $sig = self::base64url(hash_hmac('sha256', "$header.$payload", self::$secret, true));
        return "$header.$payload.$sig";
    }

    public static function validate(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;

        [$header, $payload, $sig] = $parts;
        $expected = self::base64url(hash_hmac('sha256', "$header.$payload", self::$secret, true));
        if (!hash_equals($expected, $sig)) return null;

        $data = json_decode(self::base64urlDecode($payload), true);
        if (!$data || !isset($data['exp']) || $data['exp'] < time()) return null;

        return $data;
    }

    private static function base64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64urlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
