<?php
require_once 'app/config/database.php';
require_once 'app/models/UserModel.php';
require_once 'app/helpers/SessionHelper.php';

class ProfileController
{
    private $userModel;
    private $avatarDir = 'public/images/avatars/';

    public function __construct()
    {
        // Chỉ người đã đăng nhập mới xem/sửa được hồ sơ của mình
        SessionHelper::requireLogin();

        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new UserModel($db);

        if (!is_dir($this->avatarDir)) mkdir($this->avatarDir, 0755, true);
    }

    public function index()
    {
        $user = $this->userModel->findById(SessionHelper::id());
        $errors = [];
        $success = SessionHelper::getFlash('success');
        include 'app/views/profile/index.php';
    }

    // Cập nhật họ tên, email và ảnh đại diện
    public function update()
    {
        $user = $this->userModel->findById(SessionHelper::id());
        $errors = [];
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $email    = trim($_POST['email'] ?? '');

            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ.';
            }
            // Email phải là duy nhất (trừ chính mình)
            if (empty($errors) && $email !== '') {
                $other = $this->userModel->findByEmail($email);
                if ($other && $other['id'] != $user['id']) {
                    $errors[] = 'Email đã được tài khoản khác sử dụng.';
                }
            }

            // Xử lý upload avatar (nếu có)
            $newAvatar = $user['avatar'];
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $type = mime_content_type($_FILES['avatar']['tmp_name']);
                if (!in_array($type, $allowed)) {
                    $errors[] = 'Ảnh đại diện chỉ chấp nhận JPG, PNG, GIF, WEBP.';
                } elseif ($_FILES['avatar']['size'] > 3 * 1024 * 1024) {
                    $errors[] = 'Ảnh đại diện không được vượt quá 3MB.';
                } else {
                    if ($newAvatar && file_exists($this->avatarDir . $newAvatar)) {
                        @unlink($this->avatarDir . $newAvatar);
                    }
                    $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                    $newAvatar = 'avt_' . $user['id'] . '_' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['avatar']['tmp_name'], $this->avatarDir . $newAvatar);
                }
            }

            if (empty($errors)) {
                $emailChanged = ($email !== ($user['email'] ?? ''));
                $this->userModel->updateProfile($user['id'], $fullname, $email);
                if ($newAvatar !== $user['avatar']) {
                    $this->userModel->updateAvatar($user['id'], $newAvatar);
                }

                // Đổi email → cần xác thực lại
                $reverifyLink = null;
                if ($emailChanged && $email !== '') {
                    require_once 'app/helpers/MailHelper.php';
                    $token = bin2hex(random_bytes(32));
                    $this->userModel->setVerifyToken($user['id'], $token);
                }

                // Cập nhật session theo dữ liệu mới
                $fresh = $this->userModel->findById($user['id']);
                SessionHelper::update($fresh);

                SessionHelper::setFlash('success', 'Cập nhật hồ sơ thành công.'
                    . ($emailChanged && $email !== '' ? ' Vui lòng xác thực lại email mới.' : ''));
                header('Location: /Profile');
                exit();
            }

            // Có lỗi → giữ dữ liệu vừa nhập để hiển thị lại
            $user['fullname'] = $fullname;
            $user['email']    = $email;
        }

        include 'app/views/profile/index.php';
    }
}
