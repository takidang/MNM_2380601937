<?php
require_once 'app/models/CategoryModel.php';

class CategoryApiController
{
    private $categoryModel;

    public function __construct($db)
    {
        $this->categoryModel = new CategoryModel($db);
    }

    // GET /api/category
    public function index()
    {
        header('Content-Type: application/json');
        echo json_encode($this->categoryModel->getCategories());
    }

    // GET /api/category/{id}
    public function show($id)
    {
        header('Content-Type: application/json');
        $cat = $this->categoryModel->getCategoryById($id);
        if (!$cat) {
            http_response_code(404);
            echo json_encode(['error' => 'Không tìm thấy danh mục']);
            return;
        }
        echo json_encode($cat);
    }

    // POST /api/category
    public function store()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        $name        = trim($data['name'] ?? '');
        $description = trim($data['description'] ?? '');

        if (empty($name)) {
            http_response_code(400);
            echo json_encode(['errors' => ['Tên danh mục không được để trống']]);
            return;
        }

        $this->categoryModel->addCategory($name, $description);
        http_response_code(201);
        echo json_encode(['message' => 'Tạo danh mục thành công']);
    }

    // PUT /api/category/{id}
    public function update($id)
    {
        header('Content-Type: application/json');
        if (!$this->categoryModel->getCategoryById($id)) {
            http_response_code(404);
            echo json_encode(['error' => 'Không tìm thấy danh mục']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $name        = trim($data['name'] ?? '');
        $description = trim($data['description'] ?? '');

        if (empty($name)) {
            http_response_code(400);
            echo json_encode(['errors' => ['Tên danh mục không được để trống']]);
            return;
        }

        $this->categoryModel->updateCategory($id, $name, $description);
        echo json_encode(['message' => 'Cập nhật danh mục thành công']);
    }

    // DELETE /api/category/{id}
    public function destroy($id)
    {
        header('Content-Type: application/json');
        if (!$this->categoryModel->getCategoryById($id)) {
            http_response_code(404);
            echo json_encode(['error' => 'Không tìm thấy danh mục']);
            return;
        }

        $this->categoryModel->deleteCategory($id);
        echo json_encode(['message' => 'Xóa danh mục thành công']);
    }
}
