<?php
require_once 'app/config/database.php';
require_once 'app/models/CouponModel.php';
require_once 'app/helpers/SessionHelper.php';

class CouponController
{
    private $couponModel;

    public function __construct()
    {
        SessionHelper::requireAdmin();
        $database = new Database();
        $this->couponModel = new CouponModel($database->getConnection());
    }

    public function index()
    {
        $this->list();
    }

    public function list()
    {
        $coupons   = $this->couponModel->getCoupons();
        $pageTitle = 'Mã giảm giá | TECH-SPECTRUM Admin';
        $activeMenu = 'coupons';
        include 'app/views/coupon/list.php';
    }

    public function add()
    {
        $errors = [];
        $pageTitle  = 'Thêm mã giảm giá | TECH-SPECTRUM Admin';
        $activeMenu = 'coupons';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code         = strtoupper(trim($_POST['code'] ?? ''));
            $discountValue = (float)($_POST['discount_value'] ?? 0);
            $usageLimit   = $_POST['usage_limit'] !== '' ? (int)$_POST['usage_limit'] : null;
            $validFrom    = $_POST['valid_from']  ?? '';
            $validUntil   = $_POST['valid_until'] ?? '';

            if (empty($code))                        $errors[] = 'Mã giảm giá không được trống.';
            if ($discountValue <= 0 || $discountValue > 100) $errors[] = 'Phần trăm giảm phải từ 1 đến 100.';

            if (empty($errors)) {
                $this->couponModel->addCoupon($code, $discountValue, $usageLimit, $validFrom ?: null, $validUntil ?: null);
                header('Location: /Coupon/list');
                exit();
            }
        }
        include 'app/views/coupon/add.php';
    }

    public function edit($id)
    {
        $errors = [];
        $coupon = $this->couponModel->getById($id);
        if (!$coupon) { header('Location: /Coupon/list'); exit(); }

        $pageTitle  = 'Sửa mã giảm giá | TECH-SPECTRUM Admin';
        $activeMenu = 'coupons';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code         = strtoupper(trim($_POST['code'] ?? ''));
            $discountValue = (float)($_POST['discount_value'] ?? 0);
            $usageLimit   = $_POST['usage_limit'] !== '' ? (int)$_POST['usage_limit'] : null;
            $validFrom    = $_POST['valid_from']  ?? '';
            $validUntil   = $_POST['valid_until'] ?? '';
            $isActive     = isset($_POST['is_active']) ? 1 : 0;

            if (empty($code))                        $errors[] = 'Mã giảm giá không được trống.';
            if ($discountValue <= 0 || $discountValue > 100) $errors[] = 'Phần trăm giảm phải từ 1 đến 100.';

            if (empty($errors)) {
                $this->couponModel->updateCoupon($id, $code, $discountValue, $usageLimit, $validFrom ?: null, $validUntil ?: null, $isActive);
                header('Location: /Coupon/list');
                exit();
            }
        }
        include 'app/views/coupon/edit.php';
    }

    public function delete($id)
    {
        $this->couponModel->deleteCoupon($id);
        header('Location: /Coupon/list');
        exit();
    }
}
