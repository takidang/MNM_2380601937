<?php
require_once 'app/controllers/ApiController.php';
require_once 'app/models/OrderModel.php';
require_once 'app/models/OrderDetailModel.php';
require_once 'app/models/CouponModel.php';

class OrderApiController extends ApiController
{
    private $orderModel;
    private $detailModel;
    private $couponModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->orderModel  = new OrderModel($db);
        $this->detailModel = new OrderDetailModel($db);
        $this->couponModel = new CouponModel($db);
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    // GET /api/order
    // Admin: tất cả đơn hàng; User: đơn hàng của mình
    public function index(): void
    {
        $payload = $this->requireAuth();
        $status  = $_GET['status'] ?? null;

        if ($payload['role'] === 'admin') {
            $orders = $this->orderModel->getOrders($status);
        } else {
            $orders = $this->orderModel->getOrdersByUserId($payload['sub']);
        }

        $result = [];
        foreach ($orders as $o) {
            $result[] = $this->formatOrder($o);
        }
        $this->json($result);
    }

    // GET /api/order/{id}
    public function show($id): void
    {
        $payload = $this->requireAuth();
        $order   = $this->orderModel->getOrderById($id);
        if (!$order) $this->json(['error' => 'Không tìm thấy đơn hàng'], 404);

        // User chỉ xem được đơn của mình
        if ($payload['role'] !== 'admin' && (int)$order->user_id !== (int)$payload['sub']) {
            $this->json(['error' => 'Forbidden'], 403);
        }

        $details = $this->detailModel->getDetailsByOrderId($id);
        $items   = [];
        foreach ($details as $d) {
            $items[] = [
                'id'           => (int)$d->id,
                'product_id'   => (int)$d->product_id,
                'product_name' => $d->product_name,
                'variant_name' => $d->variant_name ?? null,
                'quantity'     => (int)$d->quantity,
                'price'        => (float)$d->price,
                'subtotal'     => (float)$d->subtotal,
            ];
        }

        $data         = $this->formatOrder($order);
        $data['items'] = $items;
        $this->json($data);
    }

    // POST /api/order — tạo đơn từ giỏ hàng
    public function store(): void
    {
        $payload = $this->requireAuth();
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            $this->json(['error' => 'Giỏ hàng trống, không thể đặt hàng'], 400);
        }

        $data = $this->getBody();
        $name    = trim($data['customer_name']    ?? '');
        $phone   = trim($data['customer_phone']   ?? '');
        $email   = trim($data['customer_email']   ?? '');
        $address = trim($data['customer_address'] ?? '');
        $note    = trim($data['note']             ?? '');

        $errors = [];
        if (!$name)    $errors[] = 'Họ tên không được rỗng';
        if (!$phone)   $errors[] = 'Số điện thoại không được rỗng';
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ';
        if (!$address) $errors[] = 'Địa chỉ không được rỗng';
        if ($errors)   $this->json(['errors' => $errors], 400);

        // Xây dựng items từ session cart
        $items = [];
        foreach ($_SESSION['cart'] as $productId => $item) {
            $items[] = [
                'product_id'   => (int)$productId,
                'quantity'     => (int)$item['quantity'],
                'price'        => (float)$item['price'],
                'variant_name' => $item['variant_name'] ?? null,
            ];
        }

        $coupon      = $_SESSION['coupon'] ?? null;
        $couponCode  = $coupon['code']     ?? '';
        $discount    = (float)($coupon['discount'] ?? 0);
        $couponId    = $coupon['id']       ?? null;

        $orderId = $this->orderModel->addOrder(
            $name, $phone, $email, $address, $note,
            $items, $couponCode, $discount, $payload['sub']
        );

        if (!$orderId) $this->json(['error' => 'Tạo đơn hàng thất bại'], 500);

        // Tăng usage coupon và clear giỏ
        if ($couponId) $this->couponModel->incrementUsage($couponId);
        $_SESSION['cart']   = [];
        $_SESSION['coupon'] = null;

        $this->json(['message' => 'Đặt hàng thành công', 'order_id' => (int)$orderId], 201);
    }

    // PUT /api/order/{id} — Admin cập nhật trạng thái
    public function update($id): void
    {
        $this->requireAdmin();
        $order = $this->orderModel->getOrderById($id);
        if (!$order) $this->json(['error' => 'Không tìm thấy đơn hàng'], 404);

        $data   = $this->getBody();
        $status = $data['status'] ?? '';
        $allowed = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'];
        if (!in_array($status, $allowed)) {
            $this->json(['error' => 'Trạng thái không hợp lệ. Cho phép: ' . implode(', ', $allowed)], 400);
        }

        $this->orderModel->updateStatus($id, $status);
        $this->json(['message' => 'Cập nhật trạng thái thành công']);
    }

    // POST /api/order/cancel  body: {order_id}
    public function cancel(): void
    {
        $payload = $this->requireAuth();
        $data    = $this->getBody();
        $orderId = (int)($data['order_id'] ?? 0);
        if (!$orderId) $this->json(['error' => 'order_id không hợp lệ'], 400);

        $order = $this->orderModel->getOrderById($orderId);
        if (!$order) $this->json(['error' => 'Không tìm thấy đơn hàng'], 404);

        if ($payload['role'] !== 'admin' && (int)$order->user_id !== (int)$payload['sub']) {
            $this->json(['error' => 'Forbidden'], 403);
        }

        if ($order->status === 'cancelled') {
            $this->json(['error' => 'Đơn hàng đã được hủy'], 400);
        }
        if (in_array($order->status, ['completed', 'shipping'])) {
            $this->json(['error' => 'Không thể hủy đơn hàng đang giao hoặc đã hoàn thành'], 400);
        }

        $this->orderModel->updateStatus($orderId, 'cancelled');
        $this->json(['message' => 'Hủy đơn hàng thành công']);
    }

    // ---- Helper ----
    private function formatOrder($order): array
    {
        return [
            'id'               => (int)$order->id,
            'user_id'          => isset($order->user_id) ? (int)$order->user_id : null,
            'customer_name'    => $order->customer_name,
            'customer_phone'   => $order->customer_phone,
            'customer_email'   => $order->customer_email,
            'customer_address' => $order->customer_address,
            'total_amount'     => (float)$order->total_amount,
            'coupon_code'      => $order->coupon_code    ?? null,
            'discount_amount'  => isset($order->discount_amount) ? (float)$order->discount_amount : 0,
            'status'           => $order->status,
            'note'             => $order->note,
            'created_at'       => $order->created_at,
        ];
    }
}
