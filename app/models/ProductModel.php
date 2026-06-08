<?php
class ProductModel
{
    private $conn;
    private $table_name = "product";

    // Các thuộc tính của đối tượng sản phẩm
    private $id;
    private $name;
    private $description;
    private $price;
    private $image;
    private $category_id;
    private $category_name; // Thuộc tính lưu tên danh mục khi JOIN bảng

    // ===== CONSTRUCTOR ĐÃ ĐƯỢC SỬA LẠI CHUẨN =====
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // --- CÁC HÀM GETTER / SETTER (Giữ lại để tương thích với View của bạn) ---
    public function getID() { return $this->id; }
    public function getName() { return $this->name; }
    public function getDescription() { return $this->description; }
    public function getPrice() { return $this->price; }
    public function getImage() { return $this->image; }
    public function getCategory() { return $this->category_id; }
    public function getCategoryName() { return $this->category_name; }

    public function setName($name) { $this->name = $name; }
    public function setDescription($description) { $this->description = $description; }
    public function setPrice($price) { $this->price = $price; }
    public function setCategory($category_id) { $this->category_id = $category_id; }

    // --- CÁC HÀM XỬ LÝ DATABASE ---

    // 1. Lấy toàn bộ danh sách sản phẩm (kèm tên danh mục)
    public function getProducts()
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.image, p.category_id, c.name as category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  ORDER BY p.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $products = [];
        // Duyệt từng dòng dữ liệu và map vào đối tượng ProductModel
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $product = new ProductModel($this->conn);
            $product->id = $row['id'];
            $product->name = $row['name'];
            $product->description = $row['description'];
            $product->price = $row['price'];
            $product->image = $row['image'];
            $product->category_id = $row['category_id'];
            $product->category_name = $row['category_name'];
            $products[] = $product;
        }
        return $products;
    }

    // 2. Lấy thông tin chi tiết một sản phẩm theo ID
    public function getProductById($id)
    {
        $query = "SELECT id, name, description, price, image, category_id
                  FROM " . $this->table_name . "
                  WHERE id = :id LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->image = $row['image'];
            $this->category_id = $row['category_id'];
            return $this;
        }
        return null;
    }

    // 3. Thêm sản phẩm mới — trả về mảng $errors nếu validate thất bại, hoặc int (lastInsertId) nếu thành công
    public function addProduct($name, $description, $price, $category_id)
    {
        $errors = [];
        if (empty(trim((string)$name)))        $errors[] = 'Tên sản phẩm không được để trống';
        if (empty(trim((string)$description))) $errors[] = 'Mô tả không được để trống';
        if (!is_numeric($price) || (float)$price <= 0) $errors[] = 'Giá phải là số dương';
        if (empty($category_id))               $errors[] = 'Danh mục không được để trống';
        if (!empty($errors)) return $errors;

        $query = "INSERT INTO " . $this->table_name . "
                  (name, description, price, category_id)
                  VALUES (:name, :description, :price, :category_id)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);

        if ($stmt->execute()) {
            return (int)$this->conn->lastInsertId();
        }
        return ['Lỗi khi thêm sản phẩm vào database'];
    }

    // 4. Cập nhật thông tin sản phẩm — trả về mảng $errors nếu validate thất bại, hoặc true nếu thành công
    public function updateProduct($id, $name, $description, $price, $category_id)
    {
        $errors = [];
        if (empty(trim((string)$name)))        $errors[] = 'Tên sản phẩm không được để trống';
        if (empty(trim((string)$description))) $errors[] = 'Mô tả không được để trống';
        if (!is_numeric($price) || (float)$price <= 0) $errors[] = 'Giá phải là số dương';
        if (empty($category_id))               $errors[] = 'Danh mục không được để trống';
        if (!empty($errors)) return $errors;

        $query = "UPDATE " . $this->table_name . "
                  SET name = :name, description = :description, price = :price, category_id = :category_id
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);

        return $stmt->execute();
    }

    // 5. Xóa sản phẩm
    public function deleteProduct($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // 6. Lấy danh sách sản phẩm theo danh mục
    public function getProductsByCategory($category_id)
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.image, p.category_id, c.name AS category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE p.category_id = :category_id
                  ORDER BY p.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();

        $products = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $product = new ProductModel($this->conn);
            $product->id            = $row['id'];
            $product->name          = $row['name'];
            $product->description   = $row['description'];
            $product->price         = $row['price'];
            $product->image         = $row['image'];
            $product->category_id   = $row['category_id'];
            $product->category_name = $row['category_name'];
            $products[] = $product;
        }
        return $products;
    }
}
?>