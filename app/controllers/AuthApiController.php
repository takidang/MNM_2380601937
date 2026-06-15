<?php
require_once 'app/controllers/ApiController.php';
require_once 'app/models/UserModel.php';
require_once 'app/helpers/JwtHelper.php';

class AuthApiController extends ApiController
{
    private $userModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->userModel = new UserModel($db);
    }

    // POST /api/auth/login
    public function login(): void
    {
        $data     = $this->getBody();
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        if (!$username || !$password) {
            $this->json(['error' => 'Username và password không được rỗng'], 400);
        }

        $user = $this->userModel->findByUsername($username);
        if (!$user || !password_verify($password, $user['password'])) {
            $this->json(['error' => 'Sai username hoặc password'], 401);
        }

        if ($user['status'] === 'locked') {
            $this->json(['error' => 'Tài khoản đã bị khóa'], 403);
        }

        $token = JwtHelper::generate((int)$user['id'], $user['role'], $user['username']);

        $this->json([
            'message' => 'Đăng nhập thành công',
            'token'   => $token,
            'user'    => [
                'id'       => (int)$user['id'],
                'username' => $user['username'],
                'fullname' => $user['fullname'],
                'email'    => $user['email'],
                'role'     => $user['role'],
            ],
        ]);
    }

    // POST /api/auth/register
    public function register(): void
    {
        $data     = $this->getBody();
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';
        $fullname = trim($data['fullname'] ?? '');
        $email    = trim($data['email'] ?? '');

        $errors = [];
        if (!$username)                            $errors[] = 'Username không được rỗng';
        if (strlen($password) < 6)                 $errors[] = 'Password phải ít nhất 6 ký tự';
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ';
        if ($this->userModel->findByUsername($username)) $errors[] = 'Username đã tồn tại';
        if ($this->userModel->findByEmail($email))        $errors[] = 'Email đã tồn tại';

        if ($errors) $this->json(['errors' => $errors], 400);

        $userId = $this->userModel->create($username, $password, $fullname, $email, 'user', null, 1);
        if (!$userId) $this->json(['error' => 'Đăng ký thất bại'], 500);

        $token = JwtHelper::generate((int)$userId, 'user', $username);
        $this->json([
            'message' => 'Đăng ký thành công',
            'token'   => $token,
            'user'    => [
                'id'       => (int)$userId,
                'username' => $username,
                'fullname' => $fullname,
                'email'    => $email,
                'role'     => 'user',
            ],
        ], 201);
    }

    // GET /api/auth/me
    public function me(): void
    {
        $payload = $this->requireAuth();
        $user    = $this->userModel->findById($payload['sub']);
        if (!$user) $this->json(['error' => 'Không tìm thấy user'], 404);

        $this->json([
            'id'          => (int)$user['id'],
            'username'    => $user['username'],
            'fullname'    => $user['fullname'],
            'email'       => $user['email'],
            'role'        => $user['role'],
            'status'      => $user['status'],
            'is_verified' => (bool)$user['is_verified'],
            'avatar'      => $user['avatar'],
            'created_at'  => $user['created_at'],
        ]);
    }

    // POST /api/auth/changePassword
    public function changePassword(): void
    {
        $payload     = $this->requireAuth();
        $data        = $this->getBody();
        $oldPassword = $data['old_password'] ?? '';
        $newPassword = $data['new_password'] ?? '';

        if (!$oldPassword || !$newPassword) {
            $this->json(['error' => 'Vui lòng nhập mật khẩu cũ và mới'], 400);
        }
        if (strlen($newPassword) < 6) {
            $this->json(['error' => 'Mật khẩu mới phải ít nhất 6 ký tự'], 400);
        }

        $user = $this->userModel->findById($payload['sub']);
        if (!$user || !password_verify($oldPassword, $user['password'])) {
            $this->json(['error' => 'Mật khẩu cũ không đúng'], 400);
        }

        $this->userModel->updatePassword($payload['sub'], $newPassword);
        $this->json(['message' => 'Đổi mật khẩu thành công']);
    }

    // POST /api/auth/forgotPassword
    public function forgotPassword(): void
    {
        $data  = $this->getBody();
        $email = trim($data['email'] ?? '');
        if (!$email) $this->json(['error' => 'Email không được rỗng'], 400);

        $user = $this->userModel->findByEmail($email);
        // Luôn trả về success để không lộ thông tin
        if ($user) {
            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600);
            $this->userModel->setResetToken($user['id'], $token, $expires);
            // Trong môi trường thực sẽ gửi email, ở đây trả về token để test
        }

        $this->json([
            'message' => 'Nếu email tồn tại, link đặt lại mật khẩu đã được gửi',
            'debug_token' => $token ?? null,
        ]);
    }

    // POST /api/auth/logout
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['cart']   = [];
        $_SESSION['coupon'] = null;
        session_destroy();
        $this->json(['message' => 'Đăng xuất thành công']);
    }

    // POST /api/auth/resetPassword
    public function resetPassword(): void
    {
        $data        = $this->getBody();
        $token       = $data['token'] ?? '';
        $newPassword = $data['new_password'] ?? '';

        if (!$token || !$newPassword) {
            $this->json(['error' => 'Token và mật khẩu mới không được rỗng'], 400);
        }
        if (strlen($newPassword) < 6) {
            $this->json(['error' => 'Mật khẩu mới phải ít nhất 6 ký tự'], 400);
        }

        $user = $this->userModel->findByResetToken($token);
        if (!$user) $this->json(['error' => 'Token không hợp lệ hoặc đã hết hạn'], 400);

        if (strtotime($user['reset_expires']) < time()) {
            $this->json(['error' => 'Token đã hết hạn'], 400);
        }

        $this->userModel->updatePassword($user['id'], $newPassword);
        $this->userModel->clearResetToken($user['id']);
        $this->json(['message' => 'Đặt lại mật khẩu thành công']);
    }
}
