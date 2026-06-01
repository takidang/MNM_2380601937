<?php
require_once 'app/config/database.php';
require_once 'app/models/OrderModel.php';
require_once 'app/models/OrderDetailModel.php';
require_once 'app/models/ProductModel.php';
require_once 'app/helpers/SessionHelper.php';

class OrderController
{
    private $orderModel;
    private $orderDetailModel;
    private $productModel;

    public function __construct()
    {
        // PHÂN QUYỀN: Quản lý đơn hàng chỉ dành cho admin.
        SessionHelper::requireAdmin();

        $database = new Database();
        $db = $database->getConnection();

        $this->orderModel       = new OrderModel($db);
        $this->orderDetailModel = new OrderDetailModel($db);
        $this->productModel     = new ProductModel($db);
    }

    // ===== HIỂN THỊ DANH SÁCH ĐƠN HÀNG =====
    public function list()
    {
        $status = $_GET['status'] ?? null;
        $orders = $this->orderModel->getOrders($status);
        $stats  = $this->orderModel->countByStatus();
        $revenue = $this->orderModel->getTotalRevenue();

        include 'app/views/order/list.php';
    }

    // ===== XEM CHI TIẾT ĐƠN HÀNG =====
    public function detail($id)
    {
        $order = $this->orderModel->getOrderById($id);
        if (!$order) {
            header('Location: /Order/list');
            exit();
        }
        $details = $this->orderDetailModel->getDetailsByOrderId($id);

        include 'app/views/order/detail.php';
    }

    // ===== THÊM ĐƠN HÀNG MỚI =====
    public function add()
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerName    = trim($_POST['customer_name']    ?? '');
            $customerPhone   = trim($_POST['customer_phone']   ?? '');
            $customerEmail   = trim($_POST['customer_email']   ?? '');
            $customerAddress = trim($_POST['customer_address'] ?? '');
            $note            = trim($_POST['note']             ?? '');

            // Items có dạng: product_id[], quantity[]
            $productIds = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity']   ?? [];

            // Validate thông tin khách
            if (empty($customerName))    $errors[] = 'Tên khách hàng không được để trống.';
            if (empty($customerPhone))   $errors[] = 'Số điện thoại không được để trống.';
            elseif (!preg_match('/^[0-9]{9,11}$/', $customerPhone))
                $errors[] = 'Số điện thoại không hợp lệ (9-11 chữ số).';
            if (!empty($customerEmail) && !filter_var($customerEmail, FILTER_VALIDATE_EMAIL))
                $errors[] = 'Email không hợp lệ.';
            if (empty($customerAddress)) $errors[] = 'Địa chỉ giao hàng không được để trống.';

            // Validate items
            $items = [];
            if (empty($productIds) || count($productIds) === 0) {
                $errors[] = 'Đơn hàng phải có ít nhất 1 sản phẩm.';
            } else {
                foreach ($productIds as $idx => $pid) {
                    $qty = (int)($quantities[$idx] ?? 0);
                    if ($qty <= 0) continue;

                    $product = $this->productModel->getProductById($pid);
                    if (!$product) {
                        $errors[] = "Sản phẩm ID #$pid không tồn tại.";
                        continue;
                    }
                    $items[] = [
                        'product_id' => (int)$pid,
                        'quantity'   => $qty,
                        'price'      => $product->getPrice() // Snapshot giá hiện tại
                    ];
                }
                if (empty($items)) $errors[] = 'Không có sản phẩm hợp lệ trong đơn.';
            }

            // Nếu OK → tạo đơn
            if (empty($errors)) {
                $orderId = $this->orderModel->addOrder(
                    $customerName, $customerPhone, $customerEmail,
                    $customerAddress, $note, $items
                );
                if ($orderId) {
                    header('Location: /Order/detail/' . $orderId);
                    exit();
                } else {
                    $errors[] = 'Có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại.';
                }
            }
        }

        // Load danh sách sản phẩm cho dropdown chọn
        $products = $this->productModel->getProducts();
        include 'app/views/order/add.php';
    }

    // ===== CẬP NHẬT THÔNG TIN KHÁCH HÀNG CỦA ĐƠN =====
    public function edit($id)
    {
        $order = $this->orderModel->getOrderById($id);
        if (!$order) {
            header('Location: /Order/list');
            exit();
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerName    = trim($_POST['customer_name']    ?? '');
            $customerPhone   = trim($_POST['customer_phone']   ?? '');
            $customerEmail   = trim($_POST['customer_email']   ?? '');
            $customerAddress = trim($_POST['customer_address'] ?? '');
            $note            = trim($_POST['note']             ?? '');

            if (empty($customerName))    $errors[] = 'Tên khách hàng không được để trống.';
            if (empty($customerPhone))   $errors[] = 'Số điện thoại không được để trống.';
            elseif (!preg_match('/^[0-9]{9,11}$/', $customerPhone))
                $errors[] = 'Số điện thoại không hợp lệ.';
            if (!empty($customerEmail) && !filter_var($customerEmail, FILTER_VALIDATE_EMAIL))
                $errors[] = 'Email không hợp lệ.';
            if (empty($customerAddress)) $errors[] = 'Địa chỉ không được để trống.';

            if (empty($errors)) {
                $this->orderModel->updateOrder($id, $customerName, $customerPhone, $customerEmail, $customerAddress, $note);
                header('Location: /Order/detail/' . $id);
                exit();
            }

            // Cập nhật lại biến order để form giữ dữ liệu user vừa nhập khi có lỗi
            $order->customer_name    = $customerName;
            $order->customer_phone   = $customerPhone;
            $order->customer_email   = $customerEmail;
            $order->customer_address = $customerAddress;
            $order->note             = $note;
        }

        include 'app/views/order/edit.php';
    }

    // ===== CẬP NHẬT TRẠNG THÁI (qua AJAX hoặc form POST) =====
    public function updateStatus($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status'] ?? '';
            $this->orderModel->updateStatus($id, $status);
        }
        header('Location: /Order/detail/' . $id);
        exit();
    }

    // ===== XÓA ĐƠN HÀNG =====
    public function delete($id)
    {
        // Cascade tự động xóa order_detail
        $this->orderModel->deleteOrder($id);
        header('Location: /Order/list');
        exit();
    }
}
