<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/models/OrderModel.php';

class AdminController
{
    private $productModel;
    private $categoryModel;
    private $orderModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->productModel  = new ProductModel($db);
        $this->categoryModel = new CategoryModel($db);
        $this->orderModel    = new OrderModel($db);
    }

    public function dashboard()
    {
        $products   = $this->productModel->getProducts();
        $categories = $this->categoryModel->getCategories();
        $orders     = $this->orderModel->getOrders();
        $orderStats = $this->orderModel->countByStatus();

        include 'app/views/admin/dashboard.php';
    }
}
