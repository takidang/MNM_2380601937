<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';

class ShopController
{
    private $productModel;
    private $categoryModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->productModel  = new ProductModel($db);
        $this->categoryModel = new CategoryModel($db);
    }

    // Hiển thị tất cả sản phẩm
    public function all()
    {
        $products   = $this->productModel->getProducts();
        $categories = $this->categoryModel->getCategories();
        $currentCategory = null;
        include 'app/views/shop/list.php';
    }

    // Xem sản phẩm theo danh mục
    public function category($categoryId)
    {
        $categories = $this->categoryModel->getCategories();
        $currentCategory = null;
        foreach ($categories as $c) {
            if ((string)$c->id === (string)$categoryId) {
                $currentCategory = $c;
                break;
            }
        }
        $products = $this->productModel->getProductsByCategory($categoryId);
        include 'app/views/shop/list.php';
    }

    // Chi tiết 1 sản phẩm
    public function detail($id)
    {
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            header('Location: /');
            exit();
        }
        // Lấy sản phẩm liên quan cùng danh mục
        $relatedProducts = [];
        if ($product->getCategory()) {
            $allRelated = $this->productModel->getProductsByCategory($product->getCategory());
            foreach ($allRelated as $rp) {
                if ($rp->getID() != $product->getID()) {
                    $relatedProducts[] = $rp;
                    if (count($relatedProducts) >= 4) break;
                }
            }
        }
        include 'app/views/shop/detail.php';
    }
}
