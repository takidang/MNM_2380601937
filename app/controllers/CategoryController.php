<?php
require_once 'app/config/database.php';
require_once 'app/models/CategoryModel.php';

class CategoryController
{
    private $categoryModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->categoryModel = new CategoryModel($db);
    }

    public function index()
    {
        $this->list();
    }

    public function list()
    {
        $categories = $this->categoryModel->getCategories();
        include 'app/views/category/list.php';
    }

    public function add()
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            if (empty($name)) {
                $errors[] = 'Tên danh mục không được để trống.';
            }

            if (empty($errors)) {
                $this->categoryModel->addCategory($name, $description);
                header('Location: /Category/list');
                exit();
            }
        }
        include 'app/views/category/add.php';
    }

    public function edit($id)
    {
        $category = $this->categoryModel->getCategoryById($id);
        if (!$category) {
            die('Không tìm thấy danh mục');
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            if (empty($name)) {
                $errors[] = 'Tên danh mục không được để trống.';
            }

            if (empty($errors)) {
                $this->categoryModel->updateCategory($id, $name, $description);
                header('Location: /Category/list');
                exit();
            }
        }
        include 'app/views/category/edit.php';
    }

    public function delete($id)
    {
        // Lưu ý: Do DB cài đặt ON DELETE CASCADE nên khi xóa Category, các sản phẩm liên quan sẽ tự động bị xóa trong SQL
        $this->categoryModel->deleteCategory($id);
        header('Location: /Category/list');
        exit();
    }
}