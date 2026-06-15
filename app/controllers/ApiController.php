<?php
require_once 'app/helpers/JwtHelper.php';

abstract class ApiController
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    protected function json($data, int $code = 200): void
    {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function getBody(): array
    {
        $raw = file_get_contents('php://input');
        return $raw ? (json_decode($raw, true) ?? []) : [];
    }

    protected function getBearerToken(): ?string
    {
        $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s+(.+)/i', $auth, $m)) return $m[1];
        return null;
    }

    protected function requireAuth(): array
    {
        $token = $this->getBearerToken();
        if (!$token) $this->json(['error' => 'Unauthorized: token missing'], 401);
        $payload = JwtHelper::validate($token);
        if (!$payload) $this->json(['error' => 'Unauthorized: token invalid or expired'], 401);
        return $payload;
    }

    protected function requireAdmin(): array
    {
        $payload = $this->requireAuth();
        if ($payload['role'] !== 'admin') $this->json(['error' => 'Forbidden: admin only'], 403);
        return $payload;
    }

    protected function getPagination(): array
    {
        $page    = max(1, (int)($_GET['page']    ?? 1));
        $perPage = min(100, max(1, (int)($_GET['per_page'] ?? 10)));
        $offset  = ($page - 1) * $perPage;
        return ['page' => $page, 'per_page' => $perPage, 'offset' => $offset];
    }
}
