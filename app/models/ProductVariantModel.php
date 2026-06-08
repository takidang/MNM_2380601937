<?php
class ProductVariantModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getByProductId($productId)
    {
        $stmt = $this->conn->prepare(
            "SELECT id, name, price, sort_order FROM product_variant WHERE product_id = :pid ORDER BY sort_order ASC, id ASC"
        );
        $stmt->bindParam(':pid', $productId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT id, product_id, name, price FROM product_variant WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    public function saveVariants($productId, $variants)
    {
        $this->conn->prepare("DELETE FROM product_variant WHERE product_id = :pid")
            ->execute([':pid' => $productId]);

        if (empty($variants)) return true;

        $stmt = $this->conn->prepare(
            "INSERT INTO product_variant (product_id, name, price, sort_order) VALUES (:pid, :name, :price, :sort)"
        );
        foreach ($variants as $i => $v) {
            $name  = trim($v['name'] ?? '');
            $price = (float)($v['price'] ?? 0);
            if ($name === '' || $price <= 0) continue;
            $stmt->execute([':pid' => $productId, ':name' => $name, ':price' => $price, ':sort' => $i]);
        }
        return true;
    }
}
