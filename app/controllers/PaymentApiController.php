<?php
require_once 'app/controllers/ApiController.php';
require_once 'app/models/OrderModel.php';

class PaymentApiController extends ApiController
{
    private $orderModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->orderModel = new OrderModel($db);
    }

    // GET /api/payment/{order_id} — xem trạng thái thanh toán
    public function show($orderId): void
    {
        $payload = $this->requireAuth();
        $order   = $this->orderModel->getOrderById($orderId);
        if (!$order) $this->json(['error' => 'Không tìm thấy đơn hàng'], 404);

        if ($payload['role'] !== 'admin' && (int)$order->user_id !== (int)$payload['sub']) {
            $this->json(['error' => 'Forbidden'], 403);
        }

        $payment = $this->findPayment((int)$orderId);
        if (!$payment) $this->json(['error' => 'Đơn hàng chưa có thông tin thanh toán'], 404);

        $this->json($payment);
    }

    // POST /api/payment — tạo thanh toán
    // body: {order_id, method: 'cod'|'bank'}
    public function store(): void
    {
        $payload = $this->requireAuth();
        $data    = $this->getBody();
        $orderId = (int)($data['order_id'] ?? 0);
        $method  = in_array($data['method'] ?? '', ['cod', 'bank']) ? $data['method'] : 'cod';

        if (!$orderId) $this->json(['error' => 'order_id không hợp lệ'], 400);

        $order = $this->orderModel->getOrderById($orderId);
        if (!$order) $this->json(['error' => 'Không tìm thấy đơn hàng'], 404);

        if ($payload['role'] !== 'admin' && (int)$order->user_id !== (int)$payload['sub']) {
            $this->json(['error' => 'Forbidden'], 403);
        }

        if ($order->status === 'cancelled') {
            $this->json(['error' => 'Không thể thanh toán đơn hàng đã hủy'], 400);
        }

        // Kiểm tra đã thanh toán chưa
        $existing = $this->findPayment($orderId);
        if ($existing && $existing['status'] === 'paid') {
            $this->json(['error' => 'Đơn hàng đã được thanh toán'], 400);
        }

        // COD → pending, bank → paid (mock)
        $status = $method === 'bank' ? 'paid' : 'pending';

        if ($existing) {
            // Cập nhật
            $stmt = $this->db->prepare("UPDATE payment SET method = :m, status = :s WHERE order_id = :oid");
        } else {
            // Tạo mới
            $stmt = $this->db->prepare("INSERT INTO payment (order_id, method, status) VALUES (:oid, :m, :s)");
        }
        $stmt->bindValue(':oid', $orderId, PDO::PARAM_INT);
        $stmt->bindValue(':m',   $method);
        $stmt->bindValue(':s',   $status);
        $stmt->execute();

        // Nếu bank (đã trả) → cập nhật đơn hàng sang confirmed
        if ($status === 'paid' && $order->status === 'pending') {
            $this->orderModel->updateStatus($orderId, 'confirmed');
        }

        $this->json([
            'message'  => $status === 'paid' ? 'Thanh toán thành công' : 'Đặt COD thành công, thanh toán khi nhận hàng',
            'order_id' => $orderId,
            'method'   => $method,
            'status'   => $status,
        ], $existing ? 200 : 201);
    }

    private function findPayment(int $orderId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM payment WHERE order_id = :oid LIMIT 1");
        $stmt->bindValue(':oid', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
