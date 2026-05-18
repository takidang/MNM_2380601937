<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php'; // Cần gọi thêm CategoryModel để hiển thị danh sách chọn

class ProductController
{
    private $productModel;
    private $categoryModel;
    private $uploadDir = 'public/images/products/';

    public function __construct()
    {
        // Khởi tạo kết nối DB và gán vào các Model tương ứng
        $database = new Database();
        $db = $database->getConnection();

        $this->productModel = new ProductModel($db);
        $this->categoryModel = new CategoryModel($db);

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function index()
    {
        $this->list();
    }

    public function list()
    {
        // Lấy sản phẩm từ database
        $products = $this->productModel->getProducts();
        include 'app/views/product/list.php';
    }

    public function add()
    {
        $errors = [];
        // Lấy danh sách danh mục để đổ vào thẻ <select> trong View giao diện
        $categories = $this->categoryModel->getCategories();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name        = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price       = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null; // Đổi từ category sang category_id theo DB
            $imageName   = '';

            if (empty($name)) {
                $errors[] = 'Tên sản phẩm là bắt buộc.';
            } elseif (strlen($name) < 10 || strlen($name) > 100) {
                $errors[] = 'Tên sản phẩm phải có từ 10 đến 100 ký tự.';
            }

            if (!is_numeric($price) || $price <= 0) {
                $errors[] = 'Giá phải là một số dương lớn hơn 0.';
            }

            if (empty($category_id)) {
                $errors[] = 'Vui lòng chọn danh mục hợp lệ.';
            }

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = mime_content_type($_FILES['image']['tmp_name']);

                if (!in_array($fileType, $allowedTypes)) {
                    $errors[] = 'Chỉ chấp nhận ảnh JPG, PNG, GIF, WEBP.';
                } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    $errors[] = 'Ảnh không được vượt quá 5MB.';
                } else {
                    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $imageName = time() . '_' . uniqid() . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], $this->uploadDir . $imageName);
                }
            }

            if (empty($errors)) {
                // Database tự tăng ID (AUTO_INCREMENT) nên không cần dùng hàm max() tính ID nữa
                $result = $this->productModel->addProduct($name, $description, $price, $imageName, $category_id);
                if ($result) {
                    header('Location: /Product/list');
                    exit();
                } else {
                    $errors[] = 'Có lỗi xảy ra khi lưu sản phẩm vào cơ sở dữ liệu.';
                }
            }
        }
        include 'app/views/product/add.php';
    }

    public function edit($id)
    {
        $errors = [];
        // Lấy thông tin sản phẩm hiện tại từ DB bằng ID
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            die('Không tìm thấy sản phẩm');
        }

        $categories = $this->categoryModel->getCategories();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name        = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price       = $_POST['price'] ?? 0;
            $category_id = $_POST['category_id'] ?? null;
            $imageName   = $product->getImage(); // Giữ lại ảnh cũ mặc định nếu không upload ảnh mới

            // Validate dữ liệu tương tự lúc thêm...
            if (empty($name)) $errors[] = 'Tên sản phẩm không được để trống.';
            if (!is_numeric($price) || $price <= 0) $errors[] = 'Giá sản phẩm phải lớn hơn 0.';

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = mime_content_type($_FILES['image']['tmp_name']);

                if (in_array($fileType, $allowedTypes) && $_FILES['image']['size'] <= 5 * 1024 * 1024) {
                    // Xóa file ảnh cũ nếu có
                    if ($imageName && file_exists($this->uploadDir . $imageName)) {
                        unlink($this->uploadDir . $imageName);
                    }
                    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $imageName = time() . '_' . uniqid() . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], $this->uploadDir . $imageName);
                }
            }

            if (empty($errors)) {
                $this->productModel->updateProduct($id, $name, $description, $price, $imageName, $category_id);
                header('Location: /Product/list');
                exit();
            }
        }
        include 'app/views/product/edit.php';
    }

    public function delete($id)
    {
        $product = $this->productModel->getProductById($id);
        if ($product) {
            $imageName = $product->getImage();
            if ($imageName && file_exists($this->uploadDir . $imageName)) {
                unlink($this->uploadDir . $imageName);
            }
            $this->productModel->deleteProduct($id);
        }
        header('Location: /Product/list');
        exit();
    }
}