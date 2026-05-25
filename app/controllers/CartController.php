<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/OrderModel.php';
require_once 'app/models/OrderDetailModel.php';

class CartController
{
    private $productModel;
    private $orderModel;
    private $orderDetailModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        $database = new Database();
        $db = $database->getConnection();
        $this->productModel     = new ProductModel($db);
        $this->orderModel       = new OrderModel($db);
        $this->orderDetailModel = new OrderDetailModel($db);
    }

    // ====== HIỂN THỊ GIỎ HÀNG ======
    public function list()
    {
        $cartItems = [];
        $subtotal = 0;

        foreach ($_SESSION['cart'] as $productId => $item) {
            $product = $this->productModel->getProductById($productId);
            if (!$product) {
                unset($_SESSION['cart'][$productId]);
                continue;
            }
            $cartItems[] = [
                'product'  => $product,
                'quantity' => $item['quantity'],
                'subtotal' => $product->getPrice() * $item['quantity']
            ];
            $subtotal += $product->getPrice() * $item['quantity'];
        }

        include 'app/views/cart/list.php';
    }

    // ====== THÊM SP VÀO GIỎ ======
    public function add($productId)
    {
        $product = $this->productModel->getProductById($productId);
        if (!$product) {
            header('Location: /');
            exit();
        }
        $qty = (int)($_POST['quantity'] ?? 1);
        if ($qty < 1) $qty = 1;

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $qty;
        } else {
            $_SESSION['cart'][$productId] = ['quantity' => $qty];
        }

        $back = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . $back);
        exit();
    }

    // ====== MUA NGAY (giống add nhưng redirect thẳng đến checkout) ======
    public function buyNow($productId)
    {
        $product = $this->productModel->getProductById($productId);
        if (!$product) {
            header('Location: /');
            exit();
        }
        $qty = (int)($_POST['quantity'] ?? 1);
        if ($qty < 1) $qty = 1;

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $qty;
        } else {
            $_SESSION['cart'][$productId] = ['quantity' => $qty];
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

    // ====== TRANG THANH TOÁN ======
    public function checkout()
    {
        if (empty($_SESSION['cart'])) {
            header('Location: /Cart/list');
            exit();
        }

        $errors    = [];
        $cartItems = [];
        $subtotal  = 0;

        foreach ($_SESSION['cart'] as $productId => $item) {
            $product = $this->productModel->getProductById($productId);
            if (!$product) {
                unset($_SESSION['cart'][$productId]);
                continue;
            }
            $cartItems[] = [
                'product'  => $product,
                'quantity' => $item['quantity'],
                'subtotal' => $product->getPrice() * $item['quantity']
            ];
            $subtotal += $product->getPrice() * $item['quantity'];
        }

        if (empty($cartItems)) {
            header('Location: /Cart/list');
            exit();
        }

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
                // Tạo items dạng phù hợp với OrderModel::addOrder
                $items = [];
                foreach ($cartItems as $ci) {
                    $items[] = [
                        'product_id' => $ci['product']->getID(),
                        'quantity'   => $ci['quantity'],
                        'price'      => $ci['product']->getPrice()
                    ];
                }

                $orderId = $this->orderModel->addOrder($name, $phone, $email, $address, $note, $items);

                if ($orderId) {
                    // Đặt hàng thành công → clear giỏ
                    $_SESSION['cart'] = [];
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
}
