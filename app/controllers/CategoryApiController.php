<?php
require_once 'app/controllers/ApiController.php';
require_once 'app/models/CategoryModel.php';

class CategoryApiController extends ApiController
{
    private $categoryModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->categoryModel = new CategoryModel($db);
    }

    // GET /api/category
    public function index(): void
    {
        $cats   = $this->categoryModel->getCategories();
        $result = [];
        foreach ($cats as $c) {
            $result[] = ['id' => (int)$c->id, 'name' => $c->name, 'description' => $c->description];
        }
        $this->json($result);
    }

    // GET /api/category/{id}
    public function show($id): void
    {
        $cat = $this->categoryModel->getCategoryById($id);
        if (!$cat) $this->json(['error' => 'Không tìm thấy danh mục'], 404);
        $this->json(['id' => (int)$cat->id, 'name' => $cat->name, 'description' => $cat->description]);
    }

    // POST /api/category — Admin only
    public function store(): void
    {
        $this->requireAdmin();
        $data        = $this->getBody();
        $name        = trim($data['name']        ?? '');
        $description = trim($data['description'] ?? '');

        if (!$name) $this->json(['errors' => ['Tên danh mục không được để trống']], 400);

        $this->categoryModel->addCategory($name, $description);
        $this->json(['message' => 'Tạo danh mục thành công'], 201);
    }

    // PUT /api/category/{id} — Admin only
    public function update($id): void
    {
        $this->requireAdmin();
        $cat = $this->categoryModel->getCategoryById($id);
        if (!$cat) $this->json(['error' => 'Không tìm thấy danh mục'], 404);

        $data        = $this->getBody();
        $name        = trim($data['name']        ?? $cat->name);
        $description = trim($data['description'] ?? $cat->description);

        if (!$name) $this->json(['errors' => ['Tên danh mục không được để trống']], 400);

        $this->categoryModel->updateCategory($id, $name, $description);
        $this->json(['message' => 'Cập nhật danh mục thành công']);
    }

    // DELETE /api/category/{id} — Admin only
    public function destroy($id): void
    {
        $this->requireAdmin();
        $cat = $this->categoryModel->getCategoryById($id);
        if (!$cat) $this->json(['error' => 'Không tìm thấy danh mục'], 404);

        // Không cho xóa nếu còn sản phẩm thuộc danh mục này
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM product WHERE category_id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ((int)$stmt->fetchColumn() > 0) {
            $this->json(['error' => 'Không thể xóa danh mục vì vẫn còn sản phẩm thuộc danh mục này'], 400);
        }

        $this->categoryModel->deleteCategory($id);
        $this->json(['message' => 'Xóa danh mục thành công']);
    }
}
