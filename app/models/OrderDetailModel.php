<?php
class OrderDetailModel
{
    private $conn;
    private $table = "order_detail";

    public $id;
    public $order_id;
    public $product_id;
    public $variant_name;
    public $quantity;
    public $price;
    public $subtotal;

    // Các field JOIN từ bảng product
    public $product_name;
    public $product_image;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ===== GETTERS =====
    public function getID()           { return $this->id; }
    public function getOrderID()      { return $this->order_id; }
    public function getProductID()    { return $this->product_id; }
    public function getQuantity()     { return $this->quantity; }
    public function getPrice()        { return $this->price; }
    public function getSubtotal()     { return $this->subtotal; }
    public function getProductName()  { return $this->product_name; }
    public function getProductImage() { return $this->product_image; }

    // ===== LẤY DANH SÁCH CHI TIẾT THEO ORDER ID (kèm thông tin sản phẩm) =====
    public function getDetailsByOrderId($order_id)
    {
        $query = "SELECT od.*, p.name AS product_name, p.image AS product_image
                  FROM " . $this->table . " od
                  LEFT JOIN product p ON od.product_id = p.id
                  WHERE od.order_id = :order_id
                  ORDER BY od.id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->execute();

        $details = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $detail = new OrderDetailModel($this->conn);
            $detail->id            = $row['id'];
            $detail->order_id      = $row['order_id'];
            $detail->product_id    = $row['product_id'];
            $detail->variant_name  = $row['variant_name'] ?? null;
            $detail->quantity      = $row['quantity'];
            $detail->price         = $row['price'];
            $detail->subtotal      = $row['subtotal'];
            $detail->product_name  = $row['product_name'];
            $detail->product_image = $row['product_image'];
            $details[] = $detail;
        }
        return $details;
    }

    // ===== THÊM 1 DÒNG CHI TIẾT (dùng khi cần thêm sản phẩm vào đơn đã tồn tại) =====
    public function addDetail($order_id, $product_id, $quantity, $price)
    {
        $subtotal = $price * $quantity;
        $query = "INSERT INTO " . $this->table . "
                  (order_id, product_id, quantity, price, subtotal)
                  VALUES (:order_id, :product_id, :quantity, :price, :subtotal)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id',   $order_id,   PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':quantity',   $quantity,   PDO::PARAM_INT);
        $stmt->bindParam(':price',      $price);
        $stmt->bindParam(':subtotal',   $subtotal);
        return $stmt->execute();
    }

    // ===== CẬP NHẬT SỐ LƯỢNG =====
    public function updateQuantity($id, $quantity)
    {
        // Lấy price hiện tại để tính lại subtotal
        $sel = $this->conn->prepare("SELECT price FROM " . $this->table . " WHERE id = :id");
        $sel->bindParam(':id', $id, PDO::PARAM_INT);
        $sel->execute();
        $row = $sel->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;

        $subtotal = $row['price'] * $quantity;

        $query = "UPDATE " . $this->table . "
                  SET quantity = :quantity, subtotal = :subtotal
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':subtotal', $subtotal);
        $stmt->bindParam(':id',       $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ===== XÓA 1 DÒNG CHI TIẾT =====
    public function deleteDetail($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ===== CẬP NHẬT LẠI TỔNG TIỀN CỦA ĐƠN (gọi sau khi sửa/xóa detail) =====
    public function recalculateOrderTotal($order_id)
    {
        $query = "UPDATE `order`
                  SET total_amount = (SELECT COALESCE(SUM(subtotal), 0) FROM " . $this->table . " WHERE order_id = :oid1)
                  WHERE id = :oid2";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':oid1', $order_id, PDO::PARAM_INT);
        $stmt->bindParam(':oid2', $order_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
