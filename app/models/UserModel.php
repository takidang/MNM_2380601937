<?php
/**
 * UserModel
 * ---------
 * Xử lý toàn bộ dữ liệu người dùng: đăng nhập, đăng ký, hồ sơ, avatar,
 * đổi/đặt lại mật khẩu, xác thực email, ghi nhớ đăng nhập (remember me),
 * khóa/mở khóa và phân quyền (admin quản lý người dùng).
 *
 * Bảng `users`: id, username, password(hash), fullname, email, avatar,
 *               role(admin|user), status(active|locked), is_verified,
 *               verify_token, reset_token, reset_expires, remember_token, created_at
 */
class UserModel
{
    private $conn;
    private $table_name = "users";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ===================== TRUY VẤN CƠ BẢN =====================
    public function findByUsername($username)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table_name} WHERE username = :u LIMIT 1");
        $stmt->bindParam(':u', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table_name} WHERE email = :e LIMIT 1");
        $stmt->bindParam(':e', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table_name} WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByVerifyToken($token)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table_name} WHERE verify_token = :t LIMIT 1");
        $stmt->bindParam(':t', $token);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByResetToken($token)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table_name} WHERE reset_token = :t LIMIT 1");
        $stmt->bindParam(':t', $token);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /** Tìm theo cookie remember-me (so khớp bằng hash đã lưu). */
    public function findByRememberToken($plainToken)
    {
        $hash = hash('sha256', $plainToken);
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table_name} WHERE remember_token = :t AND status = 'active' LIMIT 1");
        $stmt->bindParam(':t', $hash);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // ===================== XÁC THỰC ĐĂNG NHẬP =====================
    /** Trả về user (kèm password để controller kiểm tra) nếu username tồn tại. */
    public function authenticate($username, $password)
    {
        $user = $this->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user; // controller sẽ kiểm tra status/is_verified rồi mới đăng nhập
        }
        return null;
    }

    // ===================== ĐĂNG KÝ / TẠO TÀI KHOẢN =====================
    public function create($username, $password, $fullname = '', $email = null, $role = 'user', $verifyToken = null, $isVerified = 0)
    {
        if ($this->findByUsername($username)) return false;
        if ($email && $this->findByEmail($email)) return false;

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO {$this->table_name}
                (username, password, fullname, email, role, status, is_verified, verify_token)
                VALUES (:u, :p, :f, :e, :r, 'active', :v, :vt)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':u', $username);
        $stmt->bindParam(':p', $hash);
        $stmt->bindParam(':f', $fullname);
        $stmt->bindParam(':e', $email);
        $stmt->bindParam(':r', $role);
        $stmt->bindValue(':v', (int)$isVerified, PDO::PARAM_INT);
        $stmt->bindParam(':vt', $verifyToken);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // ===================== HỒ SƠ / AVATAR =====================
    public function updateProfile($id, $fullname, $email)
    {
        $sql = "UPDATE {$this->table_name} SET fullname = :f, email = :e WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':f', $fullname);
        $stmt->bindParam(':e', $email);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateAvatar($id, $avatar)
    {
        $stmt = $this->conn->prepare("UPDATE {$this->table_name} SET avatar = :a WHERE id = :id");
        $stmt->bindParam(':a', $avatar);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ===================== MẬT KHẨU =====================
    public function updatePassword($id, $newPlainPassword)
    {
        $hash = password_hash($newPlainPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE {$this->table_name} SET password = :p WHERE id = :id");
        $stmt->bindParam(':p', $hash);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function setResetToken($id, $token, $expiresAt)
    {
        $sql = "UPDATE {$this->table_name} SET reset_token = :t, reset_expires = :ex WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':t', $token);
        $stmt->bindParam(':ex', $expiresAt);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function clearResetToken($id)
    {
        $stmt = $this->conn->prepare("UPDATE {$this->table_name} SET reset_token = NULL, reset_expires = NULL WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ===================== XÁC THỰC EMAIL =====================
    public function setVerifyToken($id, $token)
    {
        $stmt = $this->conn->prepare("UPDATE {$this->table_name} SET verify_token = :t, is_verified = 0 WHERE id = :id");
        $stmt->bindParam(':t', $token);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function markVerified($id)
    {
        $stmt = $this->conn->prepare("UPDATE {$this->table_name} SET is_verified = 1, verify_token = NULL WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ===================== REMEMBER ME =====================
    /** Lưu hash của token vào DB; trả về token gốc để gắn vào cookie. */
    public function setRememberToken($id, $plainToken)
    {
        $hash = hash('sha256', $plainToken);
        $stmt = $this->conn->prepare("UPDATE {$this->table_name} SET remember_token = :t WHERE id = :id");
        $stmt->bindParam(':t', $hash);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function clearRememberToken($id)
    {
        $stmt = $this->conn->prepare("UPDATE {$this->table_name} SET remember_token = NULL WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ===================== ADMIN: QUẢN LÝ NGƯỜI DÙNG =====================
    public function getAllUsers()
    {
        $stmt = $this->conn->prepare("SELECT id, username, fullname, email, avatar, role, status, is_verified, created_at
                                      FROM {$this->table_name} ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setStatus($id, $status)
    {
        $status = ($status === 'locked') ? 'locked' : 'active';
        $stmt = $this->conn->prepare("UPDATE {$this->table_name} SET status = :s WHERE id = :id");
        $stmt->bindParam(':s', $status);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function setRole($id, $role)
    {
        $role = ($role === 'admin') ? 'admin' : 'user';
        $stmt = $this->conn->prepare("UPDATE {$this->table_name} SET role = :r WHERE id = :id");
        $stmt->bindParam(':r', $role);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteUser($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table_name} WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function countAll()
    {
        return (int)$this->conn->query("SELECT COUNT(*) FROM {$this->table_name}")->fetchColumn();
    }
}
