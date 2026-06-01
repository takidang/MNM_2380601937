<?php
require_once 'app/config/database.php';
require_once 'app/models/UserModel.php';
require_once 'app/helpers/SessionHelper.php';

class UserController
{
    private $userModel;

    public function __construct()
    {
        // Toàn bộ trang quản lý người dùng chỉ dành cho admin
        SessionHelper::requireAdmin();

        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new UserModel($db);
    }

    public function index()
    {
        $this->list();
    }

    public function list()
    {
        $users = $this->userModel->getAllUsers();
        include 'app/views/user/list.php';
    }

    // Khóa tài khoản
    public function lock($id)
    {
        if ($id == SessionHelper::id()) {
            SessionHelper::setFlash('error', 'Bạn không thể tự khóa tài khoản của mình.');
        } else {
            $this->userModel->setStatus($id, 'locked');
            $this->userModel->clearRememberToken($id); // hủy phiên ghi nhớ của họ
            SessionHelper::setFlash('success', 'Đã khóa tài khoản #' . (int)$id . '.');
        }
        header('Location: /User/list');
        exit();
    }

    // Mở khóa tài khoản
    public function unlock($id)
    {
        $this->userModel->setStatus($id, 'active');
        SessionHelper::setFlash('success', 'Đã mở khóa tài khoản #' . (int)$id . '.');
        header('Location: /User/list');
        exit();
    }

    // Đổi vai trò (admin <-> user)
    public function role($id)
    {
        if ($id == SessionHelper::id()) {
            SessionHelper::setFlash('error', 'Bạn không thể tự đổi vai trò của chính mình.');
            header('Location: /User/list');
            exit();
        }
        $newRole = ($_POST['role'] ?? '') === 'admin' ? 'admin' : 'user';
        $this->userModel->setRole($id, $newRole);
        SessionHelper::setFlash('success', 'Đã cập nhật vai trò tài khoản #' . (int)$id . ' thành ' . $newRole . '.');
        header('Location: /User/list');
        exit();
    }

    // Xóa tài khoản
    public function delete($id)
    {
        if ($id == SessionHelper::id()) {
            SessionHelper::setFlash('error', 'Bạn không thể tự xóa tài khoản của mình.');
        } else {
            $this->userModel->deleteUser($id);
            SessionHelper::setFlash('success', 'Đã xóa tài khoản #' . (int)$id . '.');
        }
        header('Location: /User/list');
        exit();
    }

    // Thêm tài khoản mới (admin tạo trực tiếp, đánh dấu đã xác thực)
    public function add()
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $fullname = trim($_POST['fullname'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role     = ($_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';

            if (strlen($username) < 3) $errors[] = 'Tên đăng nhập tối thiểu 3 ký tự.';
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ.';
            if (strlen($password) < 6) $errors[] = 'Mật khẩu tối thiểu 6 ký tự.';
            if (empty($errors) && $this->userModel->findByUsername($username)) $errors[] = 'Tên đăng nhập đã tồn tại.';
            if (empty($errors) && $email !== '' && $this->userModel->findByEmail($email)) $errors[] = 'Email đã tồn tại.';

            if (empty($errors)) {
                $this->userModel->create($username, $password, $fullname, $email ?: null, $role, null, 1);
                SessionHelper::setFlash('success', 'Đã tạo tài khoản "' . htmlspecialchars($username) . '".');
                header('Location: /User/list');
                exit();
            }
        }
        include 'app/views/user/add.php';
    }
}
