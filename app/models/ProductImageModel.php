<?php
class ProductImageModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getByProductId($productId)
    {
        $stmt = $this->conn->prepare(
            "SELECT id, filename, sort_order FROM product_image WHERE product_id = :pid ORDER BY sort_order ASC, id ASC"
        );
        $stmt->bindParam(':pid', $productId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function addImages($productId, $filenames)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO product_image (product_id, filename, sort_order) VALUES (:pid, :filename, :sort)"
        );
        foreach ($filenames as $i => $filename) {
            $stmt->execute([':pid' => $productId, ':filename' => $filename, ':sort' => $i]);
        }
    }

    public function deleteById($id)
    {
        $stmt = $this->conn->prepare("SELECT filename FROM product_image WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        if (!$row) return null;

        $this->conn->prepare("DELETE FROM product_image WHERE id = :id")->execute([':id' => $id]);
        return $row->filename;
    }

    public function deleteByProductId($productId)
    {
        $stmt = $this->conn->prepare("SELECT filename FROM product_image WHERE product_id = :pid");
        $stmt->bindParam(':pid', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $filenames = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $this->conn->prepare("DELETE FROM product_image WHERE product_id = :pid")->execute([':pid' => $productId]);
        return $filenames;
    }
}
