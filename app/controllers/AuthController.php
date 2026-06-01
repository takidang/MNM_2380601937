<?php
require_once 'app/config/database.php';
require_once 'app/models/UserModel.php';
require_once 'app/helpers/SessionHelper.php';
require_once 'app/helpers/MailHelper.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        SessionHelper::start();
        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new UserModel($db);
    }

    public function index()
    {
        $this->login();
    }

    // ======================= ĐĂNG NHẬP =======================
    public function login()
    {
        if (SessionHelper::isLoggedIn()) {
            $this->redirectByRole();
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = !empty($_POST['remember']);

            if ($username === '' || $password === '') {
                $errors[] = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.';
            } else {
                $user = $this->userModel->authenticate($username, $password);
                if (!$user) {
                    $errors[] = 'Tên đăng nhập hoặc mật khẩu không đúng.';
                } elseif ($user['status'] === 'locked') {
                    $errors[] = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.';
                } else {
                    SessionHelper::login($user);

                    // Ghi nhớ đăng nhập: tạo token, lưu hash vào DB, gắn cookie
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        $this->userModel->setRememberToken($user['id'], $token);
                        SessionHelper::setRememberCookie($token, 30);
                    }

                    if ((int)$user['is_verified'] !== 1) {
                        SessionHelper::setFlash('error', 'Tài khoản chưa xác thực email. Vào Hồ sơ để gửi lại liên kết xác thực.');
                    }

                    $intended = $_SESSION['_intended'] ?? null;
                    unset($_SESSION['_intended']);
                    if ($intended) { header('Location: ' . $intended); exit(); }
                    $this->redirectByRole();
                }
            }
        }

        $flashError = SessionHelper::getFlash('error');
        include 'app/views/auth/login.php';
    }

    // ======================= ĐĂNG XUẤT =======================
    public function logout()
    {
        $uid = SessionHelper::id();
        if ($uid) $this->userModel->clearRememberToken($uid);
        SessionHelper::clearRememberCookie();
        SessionHelper::logout();
        SessionHelper::setFlash('success', 'Bạn đã đăng xuất.');
        header('Location: /Auth/login');
        exit();
    }

    // ======================= ĐĂNG KÝ =======================
    public function register()
    {
        if (SessionHelper::isLoggedIn()) { $this->redirectByRole(); }

        $errors = [];
        $devLink = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $fullname = trim($_POST['fullname'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirm'] ?? '';

            if (strlen($username) < 3)  $errors[] = 'Tên đăng nhập tối thiểu 3 ký tự.';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ.';
            if (strlen($password) < 6)  $errors[] = 'Mật khẩu tối thiểu 6 ký tự.';
            if ($password !== $confirm) $errors[] = 'Mật khẩu nhập lại không khớp.';
            if (empty($errors) && $this->userModel->findByUsername($username)) $errors[] = 'Tên đăng nhập đã tồn tại.';
            if (empty($errors) && $this->userModel->findByEmail($email))       $errors[] = 'Email đã được sử dụng.';

            if (empty($errors)) {
                $token  = bin2hex(random_bytes(32));
                $userId = $this->userModel->create($username, $password, $fullname, $email, 'user', $token, 0);
                if ($userId) {
                    $link = MailHelper::baseUrl() . '/Auth/verify/' . $token;
                    $this->sendVerifyEmail($email, $fullname ?: $username, $link);
                    if (MailHelper::DEV_MODE) $devLink = $link;
                    $title   = 'Đăng ký thành công!';
                    $message = 'Chúng tôi đã gửi liên kết xác thực tới email của bạn. Hãy kiểm tra hộp thư để kích hoạt tài khoản.';
                    $backUrl = '/Auth/login';
                    include 'app/views/auth/notice.php';
                    return;
                }
                $errors[] = 'Có lỗi khi tạo tài khoản. Vui lòng thử lại.';
            }
        }
        include 'app/views/auth/register.php';
    }

    // ======================= XÁC THỰC EMAIL =======================
    public function verify($token = null)
    {
        $user = $token ? $this->userModel->findByVerifyToken($token) : null;
        if (!$user) {
            $title = 'Liên kết không hợp lệ';
            $message = 'Liên kết xác thực không đúng hoặc đã được sử dụng.';
            $backUrl = '/Auth/login';
            include 'app/views/auth/notice.php';
            return;
        }
        $this->userModel->markVerified($user['id']);

        // Nếu đang đăng nhập chính tài khoản này thì cập nhật session
        if (SessionHelper::id() == $user['id']) {
            $fresh = $this->userModel->findById($user['id']);
            SessionHelper::update($fresh);
        }

        $title = 'Xác thực thành công!';
        $message = 'Email của bạn đã được xác thực. Bây giờ bạn có thể sử dụng đầy đủ tài khoản.';
        $backUrl = SessionHelper::isLoggedIn() ? '/Profile' : '/Auth/login';
        include 'app/views/auth/notice.php';
    }

    public function resendVerification()
    {
        SessionHelper::requireLogin();
        $uid = SessionHelper::id();
        $user = $this->userModel->findById($uid);
        $devLink = null;

        if ((int)$user['is_verified'] === 1) {
            SessionHelper::setFlash('success', 'Tài khoản của bạn đã được xác thực rồi.');
            header('Location: /Profile'); exit();
        }
        $token = bin2hex(random_bytes(32));
        $this->userModel->setVerifyToken($uid, $token);
        $link = MailHelper::baseUrl() . '/Auth/verify/' . $token;
        $this->sendVerifyEmail($user['email'], $user['fullname'] ?: $user['username'], $link);

        $title = 'Đã gửi lại liên kết xác thực';
        $message = 'Vui lòng kiểm tra email để xác thực tài khoản.';
        $backUrl = '/Profile';
        if (MailHelper::DEV_MODE) $devLink = $link;
        include 'app/views/auth/notice.php';
    }

    // ======================= ĐỔI MẬT KHẨU =======================
    public function changePassword()
    {
        SessionHelper::requireLogin();
        $errors = [];
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current = $_POST['current_password'] ?? '';
            $new     = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            $user = $this->userModel->findById(SessionHelper::id());

            if (!password_verify($current, $user['password'])) {
                $errors[] = 'Mật khẩu hiện tại không đúng.';
            }
            if (strlen($new) < 6)      $errors[] = 'Mật khẩu mới tối thiểu 6 ký tự.';
            if ($new !== $confirm)     $errors[] = 'Mật khẩu nhập lại không khớp.';
            if (empty($errors) && password_verify($new, $user['password'])) {
                $errors[] = 'Mật khẩu mới phải khác mật khẩu cũ.';
            }

            if (empty($errors)) {
                $this->userModel->updatePassword($user['id'], $new);
                $success = 'Đổi mật khẩu thành công.';
            }
        }
        include 'app/views/auth/change_password.php';
    }

    // ======================= QUÊN MẬT KHẨU =======================
    public function forgotPassword()
    {
        $errors = [];
        $done = false;
        $devLink = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ.';
            } else {
                $user = $this->userModel->findByEmail($email);
                if ($user) {
                    $token   = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', time() + 3600); // hạn 1 giờ
                    $this->userModel->setResetToken($user['id'], $token, $expires);
                    $link = MailHelper::baseUrl() . '/Auth/resetPassword/' . $token;
                    $this->sendResetEmail($user['email'], $user['fullname'] ?: $user['username'], $link);
                    if (MailHelper::DEV_MODE) $devLink = $link;
                }
                // Thông báo chung để tránh dò email tồn tại hay không
                $done = true;
            }
        }
        include 'app/views/auth/forgot_password.php';
    }

    // ======================= ĐẶT LẠI MẬT KHẨU =======================
    public function resetPassword($token = null)
    {
        $errors = [];
        $user = $token ? $this->userModel->findByResetToken($token) : null;

        $invalid = (!$user || empty($user['reset_expires']) || strtotime($user['reset_expires']) < time());

        if (!$invalid && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $new     = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            if (strlen($new) < 6)  $errors[] = 'Mật khẩu mới tối thiểu 6 ký tự.';
            if ($new !== $confirm) $errors[] = 'Mật khẩu nhập lại không khớp.';

            if (empty($errors)) {
                $this->userModel->updatePassword($user['id'], $new);
                $this->userModel->clearResetToken($user['id']);
                SessionHelper::setFlash('success', 'Đặt lại mật khẩu thành công. Mời bạn đăng nhập.');
                header('Location: /Auth/login'); exit();
            }
        }
        include 'app/views/auth/reset_password.php';
    }

    // ======================= HÀM PHỤ TRỢ =======================
    private function redirectByRole()
    {
        if (SessionHelper::isAdmin()) header('Location: /Admin/dashboard');
        else header('Location: /');
        exit();
    }

    private function sendVerifyEmail($to, $name, $link)
    {
        $html = "<p>Xin chào <b>" . htmlspecialchars($name) . "</b>,</p>"
              . "<p>Cảm ơn bạn đã đăng ký TECH-SPECTRUM. Nhấn vào liên kết sau để xác thực email:</p>"
              . "<p><a href=\"$link\">$link</a></p>"
              . "<p>Nếu không phải bạn, hãy bỏ qua email này.</p>";
        return MailHelper::send($to, 'Xác thực tài khoản TECH-SPECTRUM', $html);
    }

    private function sendResetEmail($to, $name, $link)
    {
        $html = "<p>Xin chào <b>" . htmlspecialchars($name) . "</b>,</p>"
              . "<p>Bạn (hoặc ai đó) đã yêu cầu đặt lại mật khẩu. Nhấn liên kết sau (hết hạn sau 1 giờ):</p>"
              . "<p><a href=\"$link\">$link</a></p>"
              . "<p>Nếu không phải bạn, hãy bỏ qua email này.</p>";
        return MailHelper::send($to, 'Đặt lại mật khẩu TECH-SPECTRUM', $html);
    }
}
