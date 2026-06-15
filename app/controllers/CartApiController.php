<?php
require_once 'app/controllers/ApiController.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/ProductVariantModel.php';
require_once 'app/models/CouponModel.php';

class CartApiController extends ApiController
{
    private $productModel;
    private $variantModel;
    private $couponModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->productModel = new ProductModel($db);
        $this->variantModel = new ProductVariantModel($db);
        $this->couponModel  = new CouponModel($db);
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    }

    // Xóa giỏ hàng nếu user thay đổi
    private function authCart(): array
    {
        $payload = $this->requireAuth();
        $userId  = (int)$payload['sub'];
        if (isset($_SESSION['cart_user_id']) && $_SESSION['cart_user_id'] !== $userId) {
            $_SESSION['cart']   = [];
            $_SESSION['coupon'] = null;
        }
        $_SESSION['cart_user_id'] = $userId;
        return $payload;
    }

    // GET /api/cart
    public function index(): void
    {
        $this->authCart();
        $this->json($this->buildCartData());
    }

    // POST /api/cart  body: {product_id, quantity, variant_id?}
    public function store(): void
    {
        $this->authCart();
        $data       = $this->getBody();
        $productId  = (int)($data['product_id'] ?? 0);
        $qty        = (int)($data['quantity']   ?? 1);
        $variantId  = isset($data['variant_id']) ? (int)$data['variant_id'] : null;

        if ($productId <= 0) $this->json(['error' => 'product_id không hợp lệ'], 400);
        if ($qty <= 0)       $this->json(['error' => 'Số lượng phải lớn hơn 0'], 400);

        $product = $this->productModel->getProductById($productId);
        if (!$product) $this->json(['error' => 'Sản phẩm không tồn tại'], 404);

        $price       = (float)$product->getPrice();
        $variantName = '';
        if ($variantId) {
            $variant = $this->variantModel->getById($variantId);
            if ($variant && (int)$variant['product_id'] === $productId) {
                $price       = (float)$variant['price'];
                $variantName = $variant['name'];
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

        $this->json(['message' => 'Đã thêm vào giỏ hàng', 'cart' => $this->buildCartData()], 201);
    }

    // PUT /api/cart/{product_id}  body: {quantity}
    public function update($productId): void
    {
        $this->authCart();
        $productId = (int)$productId;
        $data      = $this->getBody();
        $qty       = (int)($data['quantity'] ?? 0);

        if (!isset($_SESSION['cart'][$productId])) {
            $this->json(['error' => 'Sản phẩm không có trong giỏ hàng'], 404);
        }

        if ($qty <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId]['quantity'] = $qty;
        }

        $this->json(['message' => 'Đã cập nhật giỏ hàng', 'cart' => $this->buildCartData()]);
    }

    // DELETE /api/cart/{product_id}  hoặc DELETE /api/cart (xóa toàn bộ)
    public function destroy($productId = null): void
    {
        $this->authCart();

        if ($productId === null) {
            $_SESSION['cart']   = [];
            $_SESSION['coupon'] = null;
            $this->json(['message' => 'Đã xóa toàn bộ giỏ hàng']);
        }

        $productId = (int)$productId;
        if (!isset($_SESSION['cart'][$productId])) {
            $this->json(['error' => 'Sản phẩm không có trong giỏ hàng'], 404);
        }

        unset($_SESSION['cart'][$productId]);
        $this->json(['message' => 'Đã xóa sản phẩm khỏi giỏ hàng', 'cart' => $this->buildCartData()]);
    }

    // GET /api/cart/total
    public function total(): void
    {
        $this->authCart();
        $data = $this->buildCartData();
        $this->json([
            'subtotal'        => $data['subtotal'],
            'discount'        => $data['discount'],
            'total'           => $data['total'],
            'coupon_code'     => $data['coupon_code'] ?? null,
            'item_count'      => $data['item_count'],
        ]);
    }

    // POST /api/cart/coupon  body: {code}
    public function coupon(): void
    {
        $this->authCart();
        $data = $this->getBody();
        $code = trim($data['code'] ?? '');
        if (!$code) $this->json(['error' => 'Vui lòng nhập mã giảm giá'], 400);

        $coupon = $this->couponModel->getByCode($code);
        if (!$coupon) $this->json(['error' => 'Mã giảm giá không tồn tại'], 404);
        if (!$this->couponModel->validate($coupon)) {
            $this->json(['error' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'], 400);
        }

        $subtotal = $this->calcSubtotal();
        $discount = $this->couponModel->calculateDiscount($coupon, $subtotal);

        $_SESSION['coupon'] = [
            'id'       => $coupon->id,
            'code'     => $coupon->code,
            'percent'  => $coupon->discount_value,
            'discount' => $discount,
        ];

        $this->json([
            'message'  => 'Áp dụng mã giảm giá thành công',
            'code'     => $coupon->code,
            'discount' => $discount,
            'total'    => max(0, $subtotal - $discount),
        ]);
    }

    // DELETE /api/cart/coupon  → router calls destroyCoupon()
    public function destroyCoupon(): void
    {
        $this->authCart();
        $_SESSION['coupon'] = null;
        $this->json(['message' => 'Đã xóa mã giảm giá']);
    }

    // ---- Helpers ----

    private function calcSubtotal(): float
    {
        $subtotal = 0;
        foreach ($_SESSION['cart'] as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        return $subtotal;
    }

    private function buildCartData(): array
    {
        $items    = [];
        $subtotal = 0;

        foreach ($_SESSION['cart'] as $productId => $item) {
            $product = $this->productModel->getProductById($productId);
            $line    = $item['price'] * $item['quantity'];
            $subtotal += $line;
            $items[] = [
                'product_id'   => (int)$productId,
                'name'         => $product ? $product->getName() : "Sản phẩm #$productId",
                'image'        => $product ? $product->getImage() : null,
                'variant_id'   => $item['variant_id'],
                'variant_name' => $item['variant_name'],
                'price'        => (float)$item['price'],
                'quantity'     => (int)$item['quantity'],
                'line_total'   => (float)$line,
            ];
        }

        $discount   = isset($_SESSION['coupon']) ? (float)($_SESSION['coupon']['discount'] ?? 0) : 0;
        $couponCode = $_SESSION['coupon']['code'] ?? null;

        return [
            'items'       => $items,
            'item_count'  => count($items),
            'subtotal'    => (float)$subtotal,
            'discount'    => (float)$discount,
            'coupon_code' => $couponCode,
            'total'       => (float)max(0, $subtotal - $discount),
        ];
    }
}
