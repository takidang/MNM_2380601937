<?php
require_once 'app/controllers/ApiController.php';
require_once 'app/models/BannerModel.php';

class BannerApiController extends ApiController
{
    private $model;
    private $uploadDir;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->model     = new BannerModel($db);
        $this->uploadDir = __DIR__ . '/../../public/images/banners/';
    }

    // GET /api/banner — public: chỉ trả banner đang hoạt động
    public function index(): void
    {
        $token = $this->getBearerToken();
        if ($token && \JwtHelper::validate($token)) {
            // admin xem tất cả
            $payload = \JwtHelper::validate($token);
            if ($payload && $payload['role'] === 'admin') {
                $this->json($this->model->getAll());
            }
        }
        $this->json($this->model->getActive());
    }

    // GET /api/banner/{id}
    public function show($id): void
    {
        $this->requireAdmin();
        $row = $this->model->getById($id);
        if (!$row) $this->json(['error' => 'Không tìm thấy banner'], 404);
        $this->json($row);
    }

    // POST /api/banner  — multipart/form-data
    public function store(): void
    {
        $this->requireAdmin();
        $title      = trim($_POST['title']      ?? '');
        $subtitle   = trim($_POST['subtitle']   ?? '');
        $buttonText = trim($_POST['button_text'] ?? 'Xem ngay');
        $buttonLink = trim($_POST['button_link'] ?? '#products');
        $sortOrder  = (int)($_POST['sort_order'] ?? 0);
        $isActive   = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

        if (!$title) $this->json(['error' => 'Tiêu đề không được rỗng'], 400);

        // Debug: trả về lỗi upload nếu có file nhưng upload fail
        if (!empty($_FILES['image'])) {
            $errCode = $_FILES['image']['error'] ?? -1;
            if ($errCode !== UPLOAD_ERR_OK) {
                $msgs = [0=>'OK',1=>'File quá lớn (php.ini)',2=>'File quá lớn (form)',3=>'Upload không hoàn chỉnh',4=>'Không có file',6=>'Không có thư mục tạm',7=>'Không ghi được disk',8=>'Extension chặn'];
                $this->json(['error' => 'Upload lỗi: ' . ($msgs[$errCode] ?? "code $errCode")], 500);
            }
        }

        $image = $this->handleUpload();

        $id = $this->model->create($title, $subtitle, $image, $buttonText, $buttonLink, $sortOrder, $isActive);
        $this->json($this->model->getById($id), 201);
    }

    // PUT /api/banner/{id}  — multipart/form-data
    public function update($id): void
    {
        $this->requireAdmin();
        $row = $this->model->getById($id);
        if (!$row) $this->json(['error' => 'Không tìm thấy banner'], 404);

        // PUT qua form-data cần đọc từ php://input hoặc $_POST
        $title      = trim($_POST['title']       ?? $row['title']);
        $subtitle   = trim($_POST['subtitle']    ?? $row['subtitle']);
        $buttonText = trim($_POST['button_text'] ?? $row['button_text']);
        $buttonLink = trim($_POST['button_link'] ?? $row['button_link']);
        $sortOrder  = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : $row['sort_order'];
        $isActive   = isset($_POST['is_active'])  ? (int)$_POST['is_active']  : $row['is_active'];

        $image = $this->handleUpload();
        if (!$image) {
            $image = $row['image']; // giữ ảnh cũ nếu không upload mới
        } elseif ($row['image']) {
            // xóa ảnh cũ nếu upload ảnh mới
            $old = $this->uploadDir . $row['image'];
            if (file_exists($old)) unlink($old);
        }

        $this->model->update($id, $title, $subtitle, $image, $buttonText, $buttonLink, $sortOrder, $isActive);
        $this->json($this->model->getById($id));
    }

    // POST /api/banner/update/{id} — alias cho update qua POST (để $_FILES hoạt động)
    public function updatePost($id): void { $this->update($id); }

    // DELETE /api/banner/{id}
    public function destroy($id): void
    {
        $this->requireAdmin();
        if (!$this->model->getById($id)) $this->json(['error' => 'Không tìm thấy banner'], 404);
        $this->model->delete($id);
        $this->json(['message' => 'Đã xóa banner']);
    }

    private function handleUpload(): ?string
    {
        if (empty($_FILES['image']['tmp_name']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file    = $_FILES['image'];
        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($ext, $allowed)) {
            $this->json(['error' => 'Chỉ chấp nhận ảnh JPG, PNG, WebP, GIF'], 400);
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            $this->json(['error' => 'Ảnh không được vượt quá 5MB'], 400);
        }
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        $filename = 'banner_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], "{$this->uploadDir}{$filename}")) {
            $this->json(['error' => 'Lỗi lưu file, kiểm tra quyền thư mục'], 500);
        }
        return $filename;
    }
}
