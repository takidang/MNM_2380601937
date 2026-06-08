<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/models/ProductVariantModel.php';
require_once 'app/models/ProductImageModel.php';
require_once 'app/helpers/SessionHelper.php';

class ProductController
{
    private $productModel;
    private $categoryModel;
    private $variantModel;
    private $imageModel;
    private $uploadDir = 'public/images/products/';

    public function __construct()
    {
        SessionHelper::requireAdmin();

        $database = new Database();
        $db = $database->getConnection();

        $this->productModel  = new ProductModel($db);
        $this->categoryModel = new CategoryModel($db);
        $this->variantModel  = new ProductVariantModel($db);
        $this->imageModel    = new ProductImageModel($db);

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
        $products   = $this->productModel->getProducts();
        $categories = $this->categoryModel->getCategories();
        include 'app/views/product/list.php';
    }

    public function apidemo()
    {
        include 'app/views/product/apidemo.php';
    }

    public function add()
    {
        $errors     = [];
        $categories = $this->categoryModel->getCategories();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name        = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price       = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;

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

            $uploadedFiles = $this->handleMultipleUploads($_FILES['images'] ?? null, $errors);

            if (empty($errors)) {
                $mainImage = $uploadedFiles[0] ?? '';
                $productId = $this->productModel->addProduct($name, $description, $price, $mainImage, $category_id);
                if ($productId) {
                    if (!empty($uploadedFiles)) {
                        $this->imageModel->addImages($productId, $uploadedFiles);
                    }
                    $this->variantModel->saveVariants($productId, $_POST['variants'] ?? []);
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
        $errors  = [];
        $product = $this->productModel->getProductById($id);
        if (!$product) die('Không tìm thấy sản phẩm');

        $categories     = $this->categoryModel->getCategories();
        $variants       = $this->variantModel->getByProductId($id);
        $existingImages = $this->imageModel->getByProductId($id);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name        = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price       = $_POST['price'] ?? 0;
            $category_id = $_POST['category_id'] ?? null;
            $imageName   = $product->getImage();

            if (empty($name)) $errors[] = 'Tên sản phẩm không được để trống.';
            if (!is_numeric($price) || $price <= 0) $errors[] = 'Giá sản phẩm phải lớn hơn 0.';

            $newFiles = $this->handleMultipleUploads($_FILES['images'] ?? null, $errors);

            if (empty($errors)) {
                if (!empty($newFiles) && empty($imageName)) {
                    $imageName = $newFiles[0];
                }
                $this->productModel->updateProduct($id, $name, $description, $price, $imageName, $category_id);
                if (!empty($newFiles)) {
                    $this->imageModel->addImages($id, $newFiles);
                }
                $this->variantModel->saveVariants($id, $_POST['variants'] ?? []);
                header('Location: /Product/list');
                exit();
            }
            $existingImages = $this->imageModel->getByProductId($id);
        }
        include 'app/views/product/edit.php';
    }

    public function deleteImage($imageId)
    {
        SessionHelper::requireAdmin();
        $filename = $this->imageModel->deleteById($imageId);
        if ($filename && file_exists($this->uploadDir . $filename)) {
            unlink($this->uploadDir . $filename);
        }
        $back = $_SERVER['HTTP_REFERER'] ?? '/Product/list';
        header('Location: ' . $back);
        exit();
    }

    public function delete($id)
    {
        $product = $this->productModel->getProductById($id);
        if ($product) {
            $mainImage = $product->getImage();
            if ($mainImage && file_exists($this->uploadDir . $mainImage)) {
                unlink($this->uploadDir . $mainImage);
            }
            $extraFiles = $this->imageModel->deleteByProductId($id);
            foreach ($extraFiles as $f) {
                if ($f && file_exists($this->uploadDir . $f)) unlink($this->uploadDir . $f);
            }
            $this->productModel->deleteProduct($id);
        }
        header('Location: /Product/list');
        exit();
    }

    private function handleMultipleUploads($filesInput, &$errors)
    {
        if (!$filesInput || !isset($filesInput['tmp_name'])) return [];

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $uploaded     = [];

        $count = is_array($filesInput['tmp_name']) ? count($filesInput['tmp_name']) : 1;

        for ($i = 0; $i < $count; $i++) {
            $tmpName = is_array($filesInput['tmp_name']) ? $filesInput['tmp_name'][$i] : $filesInput['tmp_name'];
            $errCode = is_array($filesInput['error'])    ? $filesInput['error'][$i]    : $filesInput['error'];
            $size    = is_array($filesInput['size'])     ? $filesInput['size'][$i]     : $filesInput['size'];
            $origName = is_array($filesInput['name'])    ? $filesInput['name'][$i]     : $filesInput['name'];

            if ($errCode !== UPLOAD_ERR_OK) continue;

            $fileType = mime_content_type($tmpName);
            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = 'Chỉ chấp nhận ảnh JPG, PNG, GIF, WEBP.';
                continue;
            }
            if ($size > 5 * 1024 * 1024) {
                $errors[] = 'Ảnh không được vượt quá 5MB.';
                continue;
            }
            $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $filename = time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($tmpName, $this->uploadDir . $filename);
            $uploaded[] = $filename;
        }
        return $uploaded;
    }
}
