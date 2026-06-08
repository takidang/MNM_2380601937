<?php
require_once 'app/models/ProductModel.php';

class ProductApiController
{
    private $productModel;

    public function __construct($db)
    {
        $this->productModel = new ProductModel($db);
    }

    // GET /api/product
    public function index()
    {
        header('Content-Type: application/json');
        $products = $this->productModel->getProducts();
        $result = [];
        foreach ($products as $p) {
            $result[] = [
                'id'            => $p->getID(),
                'name'          => $p->getName(),
                'description'   => $p->getDescription(),
                'price'         => $p->getPrice(),
                'category_id'   => $p->getCategory(),
                'category_name' => $p->getCategoryName(),
            ];
        }
        echo json_encode($result);
    }

    // GET /api/product/{id}
    public function show($id)
    {
        header('Content-Type: application/json');
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            http_response_code(404);
            echo json_encode(['error' => 'Không tìm thấy sản phẩm']);
            return;
        }
        echo json_encode([
            'id'          => $product->getID(),
            'name'        => $product->getName(),
            'description' => $product->getDescription(),
            'price'       => $product->getPrice(),
            'category_id' => $product->getCategory(),
        ]);
    }

    // POST /api/product
    public function store()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        $result = $this->productModel->addProduct(
            $data['name']        ?? '',
            $data['description'] ?? '',
            $data['price']       ?? '',
            $data['category_id'] ?? ''
        );

        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
            return;
        }

        http_response_code(201);
        echo json_encode(['message' => 'Tạo sản phẩm thành công', 'id' => $result]);
    }

    // PUT /api/product/{id}
    public function update($id)
    {
        header('Content-Type: application/json');
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            http_response_code(404);
            echo json_encode(['error' => 'Không tìm thấy sản phẩm']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $result = $this->productModel->updateProduct(
            $id,
            $data['name']        ?? '',
            $data['description'] ?? '',
            $data['price']       ?? '',
            $data['category_id'] ?? ''
        );

        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
            return;
        }

        echo json_encode(['message' => 'Cập nhật sản phẩm thành công']);
    }

    // DELETE /api/product/{id}
    public function destroy($id)
    {
        header('Content-Type: application/json');
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            http_response_code(404);
            echo json_encode(['error' => 'Không tìm thấy sản phẩm']);
            return;
        }

        $this->productModel->deleteProduct($id);
        echo json_encode(['message' => 'Xóa sản phẩm thành công']);
    }
}
