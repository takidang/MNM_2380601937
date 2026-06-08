<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/ProductVariantModel.php';
require_once 'app/models/OrderModel.php';
require_once 'app/models/OrderDetailModel.php';
require_once 'app/models/CouponModel.php';

class CartController
{
    private $productModel;
    private $variantModel;
    private $orderModel;
    private $orderDetailModel;
    private $couponModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        $database = new Database();
        $db = $database->getConnection();
        $this->productModel     = new ProductModel($db);
        $this->variantModel     = new ProductVariantModel($db);
        $this->orderModel       = new OrderModel($db);
        $this->orderDetailModel = new OrderDetailModel($db);
        $this->couponModel      = new CouponModel($db);
    }

    // ====== HIỂN THỊ GIỎ HÀNG ======
    public function list()
    {
        $cartItems = $this->buildCartItems();
        $subtotal  = array_sum(array_column($cartItems, 'subtotal'));
        $coupon    = $_SESSION['coupon'] ?? null;
        $discount  = $coupon ? $coupon['discount'] : 0;
        include 'app/views/cart/list.php';
    }

    // ====== THÊM SP VÀO GIỎ ======
    public function add($productId)
    {
        $product = $this->productModel->getProductById($productId);
        if (!$product) { header('Location: /'); exit(); }

        $qty         = max(1, (int)($_POST['quantity'] ?? 1));
        $variantId   = (int)($_POST['variant_id'] ?? 0) ?: null;
        $variantName = trim($_POST['variant_name'] ?? '');
        $price       = $product->getPrice();

        if ($variantId) {
            $variant = $this->variantModel->getById($variantId);
            if ($variant && $variant->product_id == $productId) {
                $price       = $variant->price;
                $variantName = $variant->name;
            } else {
                $variantId   = null;
                $variantName = '';
            }
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $qty;
        } else {
            $_SESSION['cart'][$productId] = [
                'quantity'     => $qty,
                'variant_id'   => $variantId,
                'variant_name' => $variantName,
                'price'        => $price,
            ];
        }

        $back = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . $back);
        exit();
    }

    // ====== MUA NGAY ======
    public function buyNow($productId)
    {
        $product = $this->productModel->getProductById($productId);
        if (!$product) { header('Location: /'); exit(); }

        $qty         = max(1, (int)($_POST['quantity'] ?? 1));
        $variantId   = (int)($_POST['variant_id'] ?? 0) ?: null;
        $variantName = '';
        $price       = $product->getPrice();

        if ($variantId) {
            $variant = $this->variantModel->getById($variantId);
            if ($variant && $variant->product_id == $productId) {
                $price       = $variant->price;
                $variantName = $variant->name;
            } else {
                $variantId = null;
            }
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $qty;
        } else {
            $_SESSION['cart'][$productId] = [
                'quantity'     => $qty,
                'variant_id'   => $variantId,
                'variant_name' => $variantName,
                'price'        => $price,
            ];
        }

        header('Location: /Cart/checkout');
        exit();
    }

    // ====== CẬP NHẬT SỐ LƯỢNG ======
    public function update($productId)
    {
        $qty = (int)($_POST['quantity'] ?? 1);
        if ($qty <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId]['quantity'] = $qty;
        }
        header('Location: /Cart/list');
        exit();
    }

    // ====== XÓA 1 SP KHỎI GIỎ ======
    public function remove($productId)
    {
        unset($_SESSION['cart'][$productId]);
        header('Location: /Cart/list');
        exit();
    }

    // ====== XÓA TOÀN BỘ GIỎ HÀNG ======
    public function clear()
    {
        $_SESSION['cart'] = [];
        header('Location: /Cart/list');
        exit();
    }

    // ====== ÁP DỤNG MÃ GIẢM GIÁ (AJAX) ======
    public function applyCoupon()
    {
        header('Content-Type: application/json');
        $code     = strtoupper(trim($_POST['code'] ?? ''));
        $subtotal = (float)($_POST['subtotal'] ?? 0);

        if (!$code) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá.']);
            exit();
        }

        $coupon = $this->couponModel->getByCode($code);
        if (!$coupon || !$this->couponModel->validate($coupon)) {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.']);
            exit();
        }

        $discount = $this->couponModel->calculateDiscount($coupon, $subtotal);
        $_SESSION['coupon'] = [
            'id'       => $coupon->id,
            'code'     => $coupon->code,
            'percent'  => $coupon->discount_value,
            'discount' => $discount,
        ];

        echo json_encode([
            'success'  => true,
            'message'  => 'Áp dụng thành công! Giảm ' . $coupon->discount_value . '%',
            'discount' => $discount,
            'code'     => $coupon->code,
        ]);
        exit();
    }

    // ====== XÓA MÃ GIẢM GIÁ ======
    public function removeCoupon()
    {
        unset($_SESSION['coupon']);
        header('Location: /Cart/list');
        exit();
    }

    // ====== TRANG THANH TOÁN ======
    public function checkout()
    {
        if (empty($_SESSION['cart'])) {
            header('Location: /Cart/list');
            exit();
        }

        $errors    = [];
        $cartItems = $this->buildCartItems();

        if (empty($cartItems)) {
            header('Location: /Cart/list');
            exit();
        }

        $subtotal = array_sum(array_column($cartItems, 'subtotal'));
        $coupon   = $_SESSION['coupon'] ?? null;
        $discount = $coupon ? $coupon['discount'] : 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name    = trim($_POST['customer_name']    ?? '');
            $phone   = trim($_POST['customer_phone']   ?? '');
            $email   = trim($_POST['customer_email']   ?? '');
            $address = trim($_POST['customer_address'] ?? '');
            $note    = trim($_POST['note']             ?? '');

            if (empty($name))    $errors[] = 'Vui lòng nhập họ tên.';
            if (empty($phone))   $errors[] = 'Vui lòng nhập số điện thoại.';
            elseif (!preg_match('/^[0-9]{9,11}$/', $phone))
                $errors[] = 'Số điện thoại không hợp lệ.';
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL))
                $errors[] = 'Email không hợp lệ.';
            if (empty($address)) $errors[] = 'Vui lòng nhập địa chỉ giao hàng.';

            if (empty($errors)) {
                $items = [];
                foreach ($cartItems as $ci) {
                    $items[] = [
                        'product_id'   => $ci['product']->getID(),
                        'quantity'     => $ci['quantity'],
                        'price'        => $ci['price'],
                        'variant_name' => $ci['variant_name'],
                    ];
                }

                $couponCode = $coupon ? $coupon['code'] : '';
                $orderId = $this->orderModel->addOrder(
                    $name, $phone, $email, $address, $note, $items, $couponCode, $discount
                );

                if ($orderId) {
                    if ($coupon) {
                        $this->couponModel->incrementUsage($coupon['id']);
                    }
                    $_SESSION['cart']   = [];
                    unset($_SESSION['coupon']);
                    $_SESSION['last_order_id'] = $orderId;
                    header('Location: /Cart/success/' . $orderId);
                    exit();
                } else {
                    $errors[] = 'Có lỗi khi đặt hàng. Vui lòng thử lại.';
                }
            }
        }

        include 'app/views/cart/checkout.php';
    }

    // ====== TRANG ĐẶT HÀNG THÀNH CÔNG ======
    public function success($orderId)
    {
        $order = $this->orderModel->getOrderById($orderId);
        if (!$order) {
            header('Location: /');
            exit();
        }
        $details = $this->orderDetailModel->getDetailsByOrderId($orderId);
        include 'app/views/cart/success.php';
    }

    // ====== HELPER ======
    private function buildCartItems()
    {
        $items = [];
        foreach ($_SESSION['cart'] as $productId => $item) {
            $product = $this->productModel->getProductById($productId);
            if (!$product) {
                unset($_SESSION['cart'][$productId]);
                continue;
            }
            $price       = $item['price'] ?? $product->getPrice();
            $variantName = $item['variant_name'] ?? '';
            $qty         = $item['quantity'];
            $items[] = [
                'product'      => $product,
                'quantity'     => $qty,
                'price'        => $price,
                'variant_name' => $variantName,
                'subtotal'     => $price * $qty,
            ];
        }
        return $items;
    }
}
