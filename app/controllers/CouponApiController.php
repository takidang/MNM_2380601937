<?php
require_once 'app/controllers/ApiController.php';
require_once 'app/models/CouponModel.php';

class CouponApiController extends ApiController
{
    private $couponModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->couponModel = new CouponModel($db);
    }

    // GET /api/coupon — Admin: danh sách tất cả coupon
    public function index(): void
    {
        $this->requireAdmin();
        $coupons = $this->couponModel->getCoupons();
        $result  = [];
        foreach ($coupons as $c) {
            $result[] = $this->format($c);
        }
        $this->json($result);
    }

    // GET /api/coupon/{id}
    public function show($id): void
    {
        $this->requireAdmin();
        $coupon = $this->couponModel->getById($id);
        if (!$coupon) $this->json(['error' => 'Không tìm thấy coupon'], 404);
        $this->json($this->format($coupon));
    }

    // POST /api/coupon
    // body: {code, discount_value, usage_limit?, valid_from?, valid_until?}
    public function store(): void
    {
        $this->requireAdmin();
        $data = $this->getBody();

        $code          = trim($data['code']           ?? '');
        $discountValue = (float)($data['discount_value'] ?? 0);
        $usageLimit    = isset($data['usage_limit']) ? (int)$data['usage_limit'] : null;
        $validFrom     = $data['valid_from']  ?? null;
        $validUntil    = $data['valid_until'] ?? null;

        $errors = [];
        if (!$code)                              $errors[] = 'Mã coupon không được rỗng';
        if ($discountValue <= 0 || $discountValue > 100) $errors[] = 'Giá trị giảm phải từ 0.01 đến 100 (%)';
        if ($this->couponModel->getByCode($code)) $errors[] = 'Mã coupon đã tồn tại';
        if ($errors) $this->json(['errors' => $errors], 400);

        $this->couponModel->addCoupon($code, $discountValue, $usageLimit, $validFrom, $validUntil);
        $this->json(['message' => 'Tạo coupon thành công'], 201);
    }

    // PUT /api/coupon/{id}
    public function update($id): void
    {
        $this->requireAdmin();
        $coupon = $this->couponModel->getById($id);
        if (!$coupon) $this->json(['error' => 'Không tìm thấy coupon'], 404);

        $data          = $this->getBody();
        $code          = trim($data['code']           ?? $coupon->code);
        $discountValue = (float)($data['discount_value'] ?? $coupon->discount_value);
        $usageLimit    = array_key_exists('usage_limit', $data) ? ($data['usage_limit'] !== null ? (int)$data['usage_limit'] : null) : $coupon->usage_limit;
        $validFrom     = $data['valid_from']  ?? $coupon->valid_from;
        $validUntil    = $data['valid_until'] ?? $coupon->valid_until;
        $isActive      = isset($data['is_active']) ? (int)(bool)$data['is_active'] : $coupon->is_active;

        $errors = [];
        if (!$code) $errors[] = 'Mã coupon không được rỗng';
        if ($discountValue <= 0 || $discountValue > 100) $errors[] = 'Giá trị giảm phải từ 0.01 đến 100 (%)';
        // Kiểm tra trùng code với coupon khác
        $existing = $this->couponModel->getByCode($code);
        if ($existing && (int)$existing->id !== (int)$id) $errors[] = 'Mã coupon đã tồn tại';
        if ($errors) $this->json(['errors' => $errors], 400);

        $this->couponModel->updateCoupon($id, $code, $discountValue, $usageLimit, $validFrom, $validUntil, $isActive);
        $this->json(['message' => 'Cập nhật coupon thành công']);
    }

    // DELETE /api/coupon/{id}
    public function destroy($id): void
    {
        $this->requireAdmin();
        $coupon = $this->couponModel->getById($id);
        if (!$coupon) $this->json(['error' => 'Không tìm thấy coupon'], 404);
        $this->couponModel->deleteCoupon($id);
        $this->json(['message' => 'Xóa coupon thành công']);
    }

    private function format($c): array
    {
        return [
            'id'             => (int)$c->id,
            'code'           => $c->code,
            'discount_value' => (float)$c->discount_value,
            'usage_limit'    => $c->usage_limit !== null ? (int)$c->usage_limit : null,
            'used_count'     => (int)$c->used_count,
            'valid_from'     => $c->valid_from,
            'valid_until'    => $c->valid_until,
            'is_active'      => (bool)$c->is_active,
            'created_at'     => $c->created_at,
        ];
    }
}
