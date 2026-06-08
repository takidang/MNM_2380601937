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
    $db       = (new Database())->getConnection();
    $resource = $url[1] ?? '';
    $id       = $url[2] ?? null;
    $method   = $_SERVER['REQUEST_METHOD'];

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

    switch ($method) {
        case 'GET':    $id ? $controller->show($id) : $controller->index(); break;
        case 'POST':   $controller->store();      break;
        case 'PUT':    $controller->update($id);  break;
        case 'DELETE': $controller->destroy($id); break;
        default:
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Method not allowed']);
    }
    exit;
}

// Kiểm tra phần đầu tiên của URL để xác định controller
$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' :
'DefaultController';
// Kiểm tra phần thứ hai của URL để xác định action
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// die ("controller=$controllerName - action=$action");

// Kiểm tra xem controller và action có tồn tại không
if (!file_exists('app/controllers/' . $controllerName . '.php')) {
// Xử lý không tìm thấy controller
die('Controller not found');
}
require_once 'app/controllers/' . $controllerName . '.php';
$controller = new $controllerName();
if (!method_exists($controller, $action)) {
// Xử lý không tìm thấy action
die('Action not found');
}
// Gọi action với các tham số còn lại (nếu có)
call_user_func_array([$controller, $action], array_slice($url, 2));