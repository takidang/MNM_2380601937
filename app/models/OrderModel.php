<?php
class OrderModel
{
    private $conn;
    private $table = "`order`"; // Backtick vì order là từ khóa SQL

    // Properties
    public $id;
    public $customer_name;
    public $customer_phone;
    public $customer_email;
    public $customer_address;
    public $total_amount;
    public $status;
    public $note;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ===== GETTERS =====
    public function getID()              { return $this->id; }
    public function getCustomerName()    { return $this->customer_name; }
    public function getCustomerPhone()   { return $this->customer_phone; }
    public function getCustomerEmail()   { return $this->customer_email; }
    public function getCustomerAddress() { return $this->customer_address; }
    public function getTotalAmount()     { return $this->total_amount; }
    public function getStatus()          { return $this->status; }
    public function getNote()            { return $this->note; }
    public function getCreatedAt()       { return $this->created_at; }
    public function getUpdatedAt()       { return $this->updated_at; }

    // Hiển thị label tiếng Việt cho status
    public function getStatusLabel()
    {
        $labels = [
            'pending'   => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'shipping'  => 'Đang giao',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy'
        ];
        return $labels[$this->status] ?? $this->status;
    }

    // Class CSS cho badge status (Bootstrap)
    public function getStatusBadgeClass()
    {
        $classes = [
            'pending'   => 'bg-secondary',
            'confirmed' => 'bg-info',
            'shipping'  => 'bg-primary',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger'
        ];
        return $classes[$this->status] ?? 'bg-secondary';
    }

    // ===== LẤY DANH SÁCH TẤT CẢ ĐƠN HÀNG =====
    public function getOrders($status = null)
    {
        $query = "SELECT * FROM " . $this->table;
        if ($status) {
            $query .= " WHERE status = :status";
        }
        $query .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        $stmt->execute();

        $orders = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $order = new OrderModel($this->conn);
            foreach ($row as $k => $v) $order->$k = $v;
            $orders[] = $order;
        }
        return $orders;
    }

    // ===== LẤY 1 ĐƠN HÀNG THEO ID =====
    public function getOrderById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        $order = new OrderModel($this->conn);
        foreach ($row as $k => $v) $order->$k = $v;
        return $order;
    }

    // ===== LẤY ĐƠN HÀNG THEO USER =====
    public function getOrdersByUserId($userId)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $orders = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $order = new OrderModel($this->conn);
            foreach ($row as $k => $v) $order->$k = $v;
            $orders[] = $order;
        }
        return $orders;
    }

    // ===== TẠO ĐƠN HÀNG MỚI (kèm chi tiết) =====
    // $items: [['product_id', 'quantity', 'price', 'variant_name'], ...]
    public function addOrder($customerName, $customerPhone, $customerEmail, $customerAddress, $note, $items, $couponCode = '', $discount = 0, $userId = null)
    {
        try {
            $this->conn->beginTransaction();

            $subtotal    = 0;
            foreach ($items as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $totalAmount = max(0, $subtotal - $discount);

            $query = "INSERT INTO " . $this->table . "
                      (user_id, customer_name, customer_phone, customer_email, customer_address, total_amount, coupon_code, discount_amount, note)
                      VALUES (:user_id, :name, :phone, :email, :address, :total, :coupon, :discount, :note)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id',  $userId,          PDO::PARAM_INT);
            $stmt->bindParam(':name',     $customerName);
            $stmt->bindParam(':phone',    $customerPhone);
            $stmt->bindParam(':email',    $customerEmail);
            $stmt->bindParam(':address',  $customerAddress);
            $stmt->bindParam(':total',    $totalAmount);
            $stmt->bindParam(':coupon',   $couponCode);
            $stmt->bindParam(':discount', $discount);
            $stmt->bindParam(':note',     $note);
            $stmt->execute();

            $orderId = $this->conn->lastInsertId();

            $detailQuery = "INSERT INTO order_detail (order_id, product_id, variant_name, quantity, price, subtotal)
                            VALUES (:order_id, :product_id, :variant_name, :quantity, :price, :subtotal)";
            $detailStmt = $this->conn->prepare($detailQuery);

            foreach ($items as $item) {
                $subtotalRow  = $item['price'] * $item['quantity'];
                $variantName  = $item['variant_name'] ?? null;
                $detailStmt->bindValue(':order_id',     $orderId,            PDO::PARAM_INT);
                $detailStmt->bindValue(':product_id',   $item['product_id'], PDO::PARAM_INT);
                $detailStmt->bindValue(':variant_name', $variantName);
                $detailStmt->bindValue(':quantity',     $item['quantity'],   PDO::PARAM_INT);
                $detailStmt->bindValue(':price',        $item['price']);
                $detailStmt->bindValue(':subtotal',     $subtotalRow);
                $detailStmt->execute();
            }

            $this->conn->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Order creation failed: " . $e->getMessage());
            return false;
        }
    }

    // ===== CẬP NHẬT THÔNG TIN ĐƠN HÀNG =====
    public function updateOrder($id, $customerName, $customerPhone, $customerEmail, $customerAddress, $note)
    {
        $query = "UPDATE " . $this->table . "
                  SET customer_name = :name,
                      customer_phone = :phone,
                      customer_email = :email,
                      customer_address = :address,
                      note = :note
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name',    $customerName);
        $stmt->bindParam(':phone',   $customerPhone);
        $stmt->bindParam(':email',   $customerEmail);
        $stmt->bindParam(':address', $customerAddress);
        $stmt->bindParam(':note',    $note);
        $stmt->bindParam(':id',      $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ===== CẬP NHẬT TRẠNG THÁI =====
    public function updateStatus($id, $status)
    {
        $allowed = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'];
        if (!in_array($status, $allowed)) return false;

        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id',     $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ===== XÓA ĐƠN HÀNG (cascade xóa cả order_detail) =====
    public function deleteOrder($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ===== THỐNG KÊ =====
    public function countByStatus()
    {
        $query = "SELECT status, COUNT(*) as total FROM " . $this->table . " GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[$row['status']] = $row['total'];
        }
        return $result;
    }

    public function getTotalRevenue()
    {
        $query = "SELECT SUM(total_amount) as revenue FROM " . $this->table . " WHERE status = 'completed'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['revenue'] ?? 0;
    }
}
