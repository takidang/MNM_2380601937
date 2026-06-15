<?php
// Nạp lớp trợ giúp phân quyền và khởi tạo session cho toàn bộ ứng dụng
require_once 'app/helpers/SessionHelper.php';
SessionHelper::start();

// ===== GHI NHỚ ĐĂNG NHẬP (Remember Me) =====
// Nếu chưa đăng nhập nhưng có cookie remember_token hợp lệ → tự đăng nhập lại.
if (!SessionHelper::isLoggedIn() && isset($_COOKIE[SessionHelper::REMEMBER_COOKIE])) {
    require_once 'app/config/database.php';
    require_once 'app/models/UserModel.php';
    $db = (new Database())->getConnection();
    if ($db) {
        $rememberUser = (new UserModel($db))->findByRememberToken($_COOKIE[SessionHelper::REMEMBER_COOKIE]);
        if ($rememberUser) {
            SessionHelper::login($rememberUser);
        } else {
            SessionHelper::clearRememberCookie(); // cookie không hợp lệ → xóa
        }
    }
}

require_once 'app/models/ProductModel.php';
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// ===== API ROUTING =====
if (isset($url[0]) && $url[0] === 'api') {
    require_once 'app/config/database.php';
    require_once 'app/controllers/ApiController.php';
    $db       = (new Database())->getConnection();
    $resource = $url[1] ?? '';
    $method   = $_SERVER['REQUEST_METHOD'];

    // Phân tích segment thứ 3: số → $id, chuỗi → $action
    $seg2   = $url[2] ?? null;
    $id     = null;
    $action = null;
    if ($seg2 !== null) {
        if (is_numeric($seg2)) {
            $id = $seg2;
        } else {
            $action = $seg2;
            $id = $url[3] ?? null; // /api/resource/action/id
        }
    }

    $apiControllerName = ucfirst($resource) . 'ApiController';
    $apiControllerFile = 'app/controllers/' . $apiControllerName . '.php';

    if (!$resource || !file_exists($apiControllerFile)) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Resource not found']);
        exit;
    }

    require_once $apiControllerFile;
    $controller = new $apiControllerName($db);

    $call = function($method, $arg = null) use ($controller) {
        if (!method_exists($controller, $method)) {
            http_response_code(404);
            echo json_encode(['error' => "Action '$method' not found"]);
            exit;
        }
        $arg !== null ? $controller->$method($arg) : $controller->$method();
    };

    switch ($method) {
        case 'OPTIONS':
            http_response_code(200);
            break;
        case 'GET':
            if ($action)      $call($action, $id);
            elseif ($id)      $call('show', $id);
            else              $call('index');
            break;
        case 'POST':
            if ($action)      $call($action, $id);
            else              $call('store');
            break;
        case 'PUT':
            if ($action)      $call($action, $id ?? $seg2);
            else              $call('update', $id);
            break;
        case 'DELETE':
            if ($action)      $call('destroy' . ucfirst($action), $id);
            else              $call('destroy', $id);
            break;
        default:
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Method not allowed']);
    }
    exit;
}

// ===== NON-API: Redirect về frontend HTML =====
// Tất cả request không phải /api đều chuyển về frontend tương ứng
$frontendMap = [
    ''         => '/frontend/index.html',
    'login'    => '/frontend/login.html',
    'register' => '/frontend/register.html',
    'cart'     => '/frontend/cart.html',
    'checkout' => '/frontend/checkout.html',
    'orders'   => '/frontend/orders.html',
    'shop'     => '/frontend/index.html',
    'admin'    => '/frontend/admin/index.html',
];

$segment = strtolower($url[0] ?? '');
$target  = $frontendMap[$segment] ?? '/frontend/index.html';
header('Location: ' . $target);
exit;