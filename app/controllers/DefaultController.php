<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';

class DefaultController
{
    private $productModel;
    private $categoryModel;

    public function __construct()
    {
        // Khởi tạo kết nối DB và gán vào các Model
        $database = new Database();
        $db = $database->getConnection();

        $this->productModel  = new ProductModel($db);
        $this->categoryModel = new CategoryModel($db);
    }

    public function index()
    {
        // Lấy dữ liệu thật từ DB
        $products   = $this->productModel->getProducts();
        $categories = $this->categoryModel->getCategories();

        // Truyền dữ liệu sang trang home
        include 'app/views/home/home.php';
    }
}
