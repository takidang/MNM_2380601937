<?php
require_once 'app/controllers/ApiController.php';
require_once 'app/models/UserModel.php';

class UserApiController extends ApiController
{
    private $userModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->userModel = new UserModel($db);
    }

    // GET /api/user — Admin: danh sách tất cả users
    public function index(): void
    {
        $this->requireAdmin();
        $users  = $this->userModel->getAllUsers();
        $result = [];
        foreach ($users as $u) {
            $result[] = $this->formatUser($u);
        }
        $this->json($result);
    }

    // GET /api/user/{id} — Admin: chi tiết user
    public function show($id): void
    {
        $this->requireAdmin();
        $user = $this->userModel->findById($id);
        if (!$user) $this->json(['error' => 'Không tìm thấy user'], 404);
        $this->json($this->formatUser($user));
    }

    // PUT /api/user/{id} — Admin: cập nhật role/status
    public function update($id): void
    {
        $this->requireAdmin();
        $user = $this->userModel->findById($id);
        if (!$user) $this->json(['error' => 'Không tìm thấy user'], 404);

        $data = $this->getBody();
        if (isset($data['status'])) {
            if (!in_array($data['status'], ['active', 'locked'])) {
                $this->json(['error' => 'Status không hợp lệ (active|locked)'], 400);
            }
            $this->userModel->setStatus($id, $data['status']);
        }
        if (isset($data['role'])) {
            if (!in_array($data['role'], ['admin', 'user'])) {
                $this->json(['error' => 'Role không hợp lệ (admin|user)'], 400);
            }
            $this->userModel->setRole($id, $data['role']);
        }

        $this->json(['message' => 'Cập nhật user thành công']);
    }

    // DELETE /api/user/{id} — Admin: xóa user
    public function destroy($id): void
    {
        $payload = $this->requireAdmin();
        if ((int)$id === (int)$payload['sub']) {
            $this->json(['error' => 'Không thể xóa tài khoản của chính mình'], 400);
        }
        $user = $this->userModel->findById($id);
        if (!$user) $this->json(['error' => 'Không tìm thấy user'], 404);
        $this->userModel->deleteUser($id);
        $this->json(['message' => 'Xóa user thành công']);
    }

    // GET /api/user/profile — Xem profile của mình
    public function profile(): void
    {
        $payload = $this->requireAuth();
        $user    = $this->userModel->findById($payload['sub']);
        if (!$user) $this->json(['error' => 'Không tìm thấy user'], 404);
        $this->json($this->formatUser($user));
    }

    // POST /api/user/avatar — Upload avatar
    public function avatar(): void
    {
        $payload   = $this->requireAuth();
        $uploadDir = __DIR__ . '/../../public/images/avatars/';

        if (empty($_FILES['avatar']['tmp_name']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['error' => 'Không có file hoặc upload lỗi'], 400);
        }

        $file    = $_FILES['avatar'];
        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($ext, $allowed)) $this->json(['error' => 'Chỉ chấp nhận ảnh JPG, PNG, WebP, GIF'], 400);
        if ($file['size'] > 2 * 1024 * 1024) $this->json(['error' => 'Ảnh không được quá 2MB'], 400);
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        // Xóa avatar cũ
        $user = $this->userModel->findById($payload['sub']);
        if ($user && $user['avatar']) {
            $old = $uploadDir . $user['avatar'];
            if (file_exists($old)) unlink($old);
        }

        $filename = 'avatar_' . $payload['sub'] . '_' . time() . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $this->json(['error' => 'Lỗi lưu file'], 500);
        }

        $this->userModel->updateAvatar($payload['sub'], $filename);
        $this->json(['message' => 'Cập nhật avatar thành công', 'avatar' => $filename]);
    }

    // PUT /api/user/profile — Cập nhật profile của mình
    public function updateProfile(): void
    {
        $payload  = $this->requireAuth();
        $data     = $this->getBody();
        $fullname = trim($data['fullname'] ?? '');
        $email    = trim($data['email']    ?? '');

        $errors = [];
        if (!$fullname) $errors[] = 'Họ tên không được rỗng';
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ';
        if ($errors) $this->json(['errors' => $errors], 400);

        // Kiểm tra email trùng với user khác
        $existing = $this->userModel->findByEmail($email);
        if ($existing && (int)$existing['id'] !== (int)$payload['sub']) {
            $this->json(['error' => 'Email đã được sử dụng'], 400);
        }

        $this->userModel->updateProfile($payload['sub'], $fullname, $email);
        $this->json(['message' => 'Cập nhật hồ sơ thành công']);
    }

    private function formatUser(array $u): array
    {
        return [
            'id'          => (int)$u['id'],
            'username'    => $u['username'],
            'fullname'    => $u['fullname'],
            'email'       => $u['email'],
            'role'        => $u['role'],
            'status'      => $u['status'],
            'is_verified' => (bool)$u['is_verified'],
            'avatar'      => $u['avatar'],
            'created_at'  => $u['created_at'],
        ];
    }
}
