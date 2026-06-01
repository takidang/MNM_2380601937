<?php
/**
 * SessionHelper
 * -------------
 * Lớp trợ giúp tĩnh (static) quản lý phiên đăng nhập và phân quyền theo vai trò (RBAC).
 *
 * Vai trò được hỗ trợ:
 *   - 'admin' : toàn quyền quản trị (thêm/sửa/xóa sản phẩm, danh mục, đơn hàng...)
 *   - 'user'  : khách hàng thông thường (xem sản phẩm, thêm giỏ hàng, thanh toán)
 *
 * Cách dùng nhanh:
 *   SessionHelper::start();                 // khởi tạo session (gọi 1 lần ở index.php)
 *   SessionHelper::login($userArray);       // sau khi xác thực thành công
 *   SessionHelper::requireAdmin();          // chặn truy cập nếu không phải admin
 *   if (SessionHelper::isAdmin()) { ... }   // ẩn/hiện nút trong view
 *   SessionHelper::logout();                // đăng xuất
 */
class SessionHelper
{
    /** Bắt đầu session nếu chưa được bật. */
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Ghi nhận người dùng đã đăng nhập vào session.
     * @param array $user Mảng dữ liệu user (từ DB).
     */
    public static function login($user)
    {
        self::start();
        // Chống session fixation: cấp lại ID session sau khi đăng nhập
        session_regenerate_id(true);
        self::store($user);
    }

    /** Cập nhật thông tin user trong session (không tạo lại session id). */
    public static function update($user)
    {
        self::start();
        self::store($user);
    }

    /** Lưu các trường cần thiết của user vào session. */
    private static function store($user)
    {
        $_SESSION['user'] = [
            'id'          => $user['id']          ?? null,
            'username'    => $user['username']    ?? '',
            'fullname'    => $user['fullname']    ?? ($user['username'] ?? ''),
            'email'       => $user['email']       ?? '',
            'avatar'      => $user['avatar']      ?? null,
            'role'        => $user['role']        ?? 'user',
            'status'      => $user['status']      ?? 'active',
            'is_verified' => (int)($user['is_verified'] ?? 0),
        ];
    }

    /** Xóa thông tin đăng nhập (đăng xuất). Giữ lại giỏ hàng nếu muốn. */
    public static function logout()
    {
        self::start();
        unset($_SESSION['user']);
    }

    /** @return array|null Thông tin user hiện tại hoặc null nếu chưa đăng nhập. */
    public static function user()
    {
        self::start();
        return $_SESSION['user'] ?? null;
    }

    /** @return bool Đã đăng nhập hay chưa. */
    public static function isLoggedIn()
    {
        return self::user() !== null;
    }

    /** @return string Vai trò hiện tại ('admin' | 'user' | 'guest'). */
    public static function role()
    {
        $u = self::user();
        return $u['role'] ?? 'guest';
    }

    /** @return bool Có phải admin không. */
    public static function isAdmin()
    {
        return self::role() === 'admin';
    }

    /** @return bool Kiểm tra vai trò cụ thể. */
    public static function hasRole($role)
    {
        return self::role() === $role;
    }

    /** Lấy tên hiển thị thân thiện của người dùng hiện tại. */
    public static function displayName()
    {
        $u = self::user();
        if (!$u) return 'Khách';
        return $u['fullname'] !== '' ? $u['fullname'] : $u['username'];
    }

    /** @return int|null ID người dùng hiện tại. */
    public static function id()
    {
        $u = self::user();
        return $u['id'] ?? null;
    }

    /** @return bool Tài khoản đã xác thực email chưa. */
    public static function isVerified()
    {
        $u = self::user();
        return !empty($u) && (int)($u['is_verified'] ?? 0) === 1;
    }

    /** Avatar URL để hiển thị (hoặc null nếu chưa có). */
    public static function avatarUrl()
    {
        $u = self::user();
        if (!empty($u['avatar'])) {
            return '/public/images/avatars/' . $u['avatar'];
        }
        return null;
    }

    // ===================== REMEMBER ME (COOKIE) =====================
    const REMEMBER_COOKIE = 'remember_token';

    public static function setRememberCookie($plainToken, $days = 30)
    {
        $expire = time() + 60 * 60 * 24 * $days;
        setcookie(self::REMEMBER_COOKIE, $plainToken, [
            'expires'  => $expire,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $_COOKIE[self::REMEMBER_COOKIE] = $plainToken;
    }

    public static function clearRememberCookie()
    {
        if (isset($_COOKIE[self::REMEMBER_COOKIE])) {
            setcookie(self::REMEMBER_COOKIE, '', [
                'expires'  => time() - 3600,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            unset($_COOKIE[self::REMEMBER_COOKIE]);
        }
    }

    // ===================== FLASH MESSAGE =====================
    /** Đặt thông báo tạm (hiển thị 1 lần ở request kế tiếp). */
    public static function setFlash($key, $message)
    {
        self::start();
        $_SESSION['_flash'][$key] = $message;
    }

    /** Lấy và xóa thông báo tạm. */
    public static function getFlash($key)
    {
        self::start();
        if (isset($_SESSION['_flash'][$key])) {
            $msg = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $msg;
        }
        return null;
    }

    // ===================== CÁC "LÁ CHẮN" PHÂN QUYỀN =====================

    /**
     * Yêu cầu phải đăng nhập. Nếu chưa → chuyển tới trang đăng nhập.
     * @param string $redirect URL quay lại sau khi đăng nhập.
     */
    public static function requireLogin($redirect = '/Auth/login')
    {
        if (!self::isLoggedIn()) {
            self::setFlash('error', 'Vui lòng đăng nhập để tiếp tục.');
            // Lưu trang đang muốn vào để quay lại sau khi đăng nhập
            $_SESSION['_intended'] = $_SERVER['REQUEST_URI'] ?? '/';
            header('Location: ' . $redirect);
            exit();
        }
    }

    /**
     * Yêu cầu phải là admin. Đây là cơ chế chính bảo vệ các chức năng quản lý.
     * - Chưa đăng nhập  → chuyển tới trang đăng nhập.
     * - Đã đăng nhập nhưng không phải admin → từ chối (403) và quay về trang chủ.
     */
    public static function requireAdmin()
    {
        // Bắt buộc đăng nhập trước
        self::requireLogin();

        // Đã đăng nhập nhưng vai trò không đủ quyền
        if (!self::isAdmin()) {
            self::setFlash('error', 'Bạn không có quyền truy cập chức năng quản trị. Cần tài khoản admin.');
            http_response_code(403);
            header('Location: /');
            exit();
        }
    }
}
