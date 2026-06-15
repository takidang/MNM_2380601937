<?php
require_once 'app/controllers/ApiController.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/models/ProductVariantModel.php';
require_once 'app/models/ProductImageModel.php';

class ProductApiController extends ApiController
{
    private $productModel;
    private $categoryModel;
    private $variantModel;
    private $imageModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->productModel  = new ProductModel($db);
        $this->categoryModel = new CategoryModel($db);
        $this->variantModel  = new ProductVariantModel($db);
        $this->imageModel    = new ProductImageModel($db);
    }

    // GET /api/product?page=1&per_page=10&category_id=&sort=newest&min_price=&max_price=
    public function index(): void
    {
        $pg         = $this->getPagination();
        $categoryId = $_GET['category_id'] ?? null;
        $sort       = $_GET['sort']        ?? 'newest';
        $minPrice   = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
        $maxPrice   = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;

        $products = $this->productModel->filterProducts($categoryId, $sort, $minPrice, $maxPrice, $pg['page'], $pg['per_page']);
        $total    = $this->productModel->countProducts($categoryId, null, $minPrice, $maxPrice);

        $this->json([
            'data'       => array_map([$this, 'formatProduct'], $products),
            'pagination' => [
                'total'    => $total,
                'page'     => $pg['page'],
                'per_page' => $pg['per_page'],
                'pages'    => (int)ceil($total / $pg['per_page']),
            ],
        ]);
    }

    // GET /api/product/{id}
    public function show($id): void
    {
        $product = $this->productModel->getProductById($id);
        if (!$product) $this->json(['error' => 'Không tìm thấy sản phẩm'], 404);

        $variants = $this->variantModel->getByProductId($id);
        $images   = $this->imageModel->getByProductId($id);

        $data             = $this->formatProduct($product);
        $data['variants'] = $variants;
        $data['images']   = array_map(fn($img) => [
            'id'       => $img['id'],
            'filename' => $img['filename'],
            'url'      => 'public/images/products/' . $img['filename'],
        ], $images);

        $this->json($data);
    }

    // GET /api/product/search?q=keyword&page=1&per_page=10
    public function search(): void
    {
        $q  = trim($_GET['q'] ?? '');
        if (!$q) $this->json(['error' => 'Vui lòng nhập từ khóa tìm kiếm'], 400);

        $pg       = $this->getPagination();
        $products = $this->productModel->searchProducts($q, $pg['page'], $pg['per_page']);
        $total    = $this->productModel->countProducts(null, $q);

        $this->json([
            'data'       => array_map([$this, 'formatProduct'], $products),
            'pagination' => [
                'total'    => $total,
                'page'     => $pg['page'],
                'per_page' => $pg['per_page'],
                'pages'    => (int)ceil($total / $pg['per_page']),
            ],
        ]);
    }

    // POST /api/product — Admin only
    // body JSON: {name, description, price, category_id}
    public function store(): void
    {
        $this->requireAdmin();
        $data = $this->getBody();

        // Kiểm tra danh mục hợp lệ
        if (!empty($data['category_id'])) {
            $cat = $this->categoryModel->getCategoryById($data['category_id']);
            if (!$cat) $this->json(['error' => 'Danh mục không tồn tại'], 400);
        }

        $result = $this->productModel->addProduct(
            $data['name']        ?? '',
            $data['description'] ?? '',
            $data['price']       ?? '',
            $data['category_id'] ?? ''
        );

        if (is_array($result)) $this->json(['errors' => $result], 400);

        // Lưu variants nếu có
        if (!empty($data['variants']) && is_array($data['variants'])) {
            $this->variantModel->saveVariants($result, $data['variants']);
        }

        $this->json(['message' => 'Tạo sản phẩm thành công', 'id' => $result], 201);
    }

    // PUT /api/product/{id} — Admin only
    public function update($id): void
    {
        $this->requireAdmin();
        $product = $this->productModel->getProductById($id);
        if (!$product) $this->json(['error' => 'Không tìm thấy sản phẩm'], 404);

        $data = $this->getBody();

        if (!empty($data['category_id'])) {
            $cat = $this->categoryModel->getCategoryById($data['category_id']);
            if (!$cat) $this->json(['error' => 'Danh mục không tồn tại'], 400);
        }

        $result = $this->productModel->updateProduct(
            $id,
            $data['name']        ?? $product->getName(),
            $data['description'] ?? $product->getDescription(),
            $data['price']       ?? $product->getPrice(),
            $data['category_id'] ?? $product->getCategory()
        );

        if (is_array($result)) $this->json(['errors' => $result], 400);

        if (isset($data['variants']) && is_array($data['variants'])) {
            $this->variantModel->saveVariants($id, $data['variants']);
        }

        $this->json(['message' => 'Cập nhật sản phẩm thành công']);
    }

    // DELETE /api/product/{id} — Admin only
    public function destroy($id): void
    {
        $this->requireAdmin();
        $product = $this->productModel->getProductById($id);
        if (!$product) $this->json(['error' => 'Không tìm thấy sản phẩm'], 404);

        // Xóa ảnh vật lý nếu có
        $filenames = $this->imageModel->deleteByProductId($id);
        foreach ((array)$filenames as $fn) {
            $path = 'public/images/products/' . $fn;
            if (file_exists($path)) @unlink($path);
        }
        if ($product->getImage()) {
            $main = 'public/images/products/' . $product->getImage();
            if (file_exists($main)) @unlink($main);
        }

        $this->productModel->deleteProduct($id);
        $this->json(['message' => 'Xóa sản phẩm thành công']);
    }

    private function formatProduct($p): array
    {
        return [
            'id'            => (int)$p->getID(),
            'name'          => $p->getName(),
            'description'   => $p->getDescription(),
            'price'         => (float)$p->getPrice(),
            'image'         => $p->getImage(),
            'image_url'     => $p->getImage() ? 'public/images/products/' . $p->getImage() : null,
            'category_id'   => (int)$p->getCategory(),
            'category_name' => $p->getCategoryName(),
        ];
    }
}
