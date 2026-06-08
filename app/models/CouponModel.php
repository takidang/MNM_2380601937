<?php
class CouponModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getCoupons()
    {
        $stmt = $this->conn->prepare("SELECT * FROM coupon ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM coupon WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    public function getByCode($code)
    {
        $stmt = $this->conn->prepare("SELECT * FROM coupon WHERE code = :code LIMIT 1");
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    public function validate($coupon)
    {
        if (!$coupon || !$coupon->is_active) return false;
        $now = date('Y-m-d H:i:s');
        if ($coupon->valid_from && $now < $coupon->valid_from) return false;
        if ($coupon->valid_until && $now > $coupon->valid_until) return false;
        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) return false;
        return true;
    }

    public function calculateDiscount($coupon, $subtotal)
    {
        return round($subtotal * ($coupon->discount_value / 100), 0);
    }

    public function incrementUsage($id)
    {
        $this->conn->prepare("UPDATE coupon SET used_count = used_count + 1 WHERE id = :id")
            ->execute([':id' => $id]);
    }

    public function addCoupon($code, $discountValue, $usageLimit, $validFrom, $validUntil)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO coupon (code, discount_value, usage_limit, valid_from, valid_until) VALUES (:code, :val, :limit, :from, :until)"
        );
        return $stmt->execute([
            ':code'  => strtoupper(trim($code)),
            ':val'   => $discountValue,
            ':limit' => $usageLimit ?: null,
            ':from'  => $validFrom ?: null,
            ':until' => $validUntil ?: null,
        ]);
    }

    public function updateCoupon($id, $code, $discountValue, $usageLimit, $validFrom, $validUntil, $isActive)
    {
        $stmt = $this->conn->prepare(
            "UPDATE coupon SET code=:code, discount_value=:val, usage_limit=:limit, valid_from=:from, valid_until=:until, is_active=:active WHERE id=:id"
        );
        return $stmt->execute([
            ':id'     => $id,
            ':code'   => strtoupper(trim($code)),
            ':val'    => $discountValue,
            ':limit'  => $usageLimit ?: null,
            ':from'   => $validFrom ?: null,
            ':until'  => $validUntil ?: null,
            ':active' => $isActive ? 1 : 0,
        ]);
    }

    public function deleteCoupon($id)
    {
        $this->conn->prepare("DELETE FROM coupon WHERE id = :id")->execute([':id' => $id]);
    }
}
