<?php
header("Content-Security-Policy: default-src 'self' https: data:; script-src 'self' 'unsafe-inline' https:; style-src 'self' 'unsafe-inline' https:;");
?>
<?php
// index.php
session_start();

$config = include __DIR__ . '/config/config.php';
require __DIR__ . '/core/Request.php';
require __DIR__ . '/core/Response.php';
require __DIR__ . '/core/Router.php';
require __DIR__ . '/core/Auth.php';
require __DIR__ . '/core/Controller.php';
require __DIR__ . '/core/Model.php';
require __DIR__ . '/helpers/url_helper.php';
require __DIR__ . '/models/User.php';
require __DIR__ . '/controllers/PageController.php';

$config = include __DIR__ . '/config/config.php';
$baseUrl = $config['base_url'] ?? '';
$router = new Router();

// Kiểm tra phiên user đã bị khóa hoặc xóa
if (Auth::check()) {
    $sessionUser = Auth::user();
    if (!empty($sessionUser['id'])) {
        $userModel = new User();
        $fresh = $userModel->findById((int)$sessionUser['id']);
        if (!$fresh || (int)($fresh['is_active'] ?? 1) !== 1) {
            $userModel->setOnline((int)$sessionUser['id'], false);
            $_SESSION['blocked_message'] = 'Tài khoản đã bị khóa. Bạn sẽ được đăng xuất.';
            header('Location: ' . base_url('blocked'));
            exit;
        }
    }
}

// Nếu bật bảo trì: chặn mọi route người dùng (không chặn admin + login/register/forgot/logout)
$maintenanceCfgFile = __DIR__ . '/config/maintenance.json';
$maintenanceCfg = [];
if (file_exists($maintenanceCfgFile)) {
    $maintenanceCfg = json_decode(file_get_contents($maintenanceCfgFile), true) ?: [];
}
$maintenanceEnabled = !empty($maintenanceCfg['enabled']);
$uri = $_SERVER['REQUEST_URI'] ?? '';
if ($maintenanceEnabled && !Auth::isAdmin()) {
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    $allowed = ['/login','/register','/forgot','/forgot/wait','/forgot/status','/forgot/resend','/logout','/maintenance','/blocked'];
    $isAllowed = false;
    foreach ($allowed as $allow) {
        if (str_starts_with($path, $allow)) { $isAllowed = true; break; }
    }
    if (!$isAllowed) {
        http_response_code(503);
        (new PageController())->maintenancePage();
        exit;
    }
}

$router->get('/', 'HomeController@index');
$router->get('/products', 'ProductController@index');
$router->get('/product/{id}', 'ProductController@show');
$router->get('/products/search', 'ProductController@searchJson');
$router->get('/about', 'PageController@about');
$router->get('/services', 'PageController@services');
$router->post('/services/book', 'ServiceController@book');
$router->post('/cart/add', 'CartController@add');
$router->post('/cart/remove', 'CartController@remove');
$router->post('/cart/apply-voucher', 'CartController@applyVoucher');
$router->get('/cart', 'CartController@index');
$router->post('/order/checkout', 'OrderController@checkout');
$router->get('/orders', 'OrderController@myOrders');
$router->get('/payment/{method}/{id}', 'PaymentController@show');
$router->post('/payment/{method}/{id}', 'PaymentController@confirm');
$router->get('/complaints/create/{orderId}', 'ComplaintController@createForm');
$router->post('/complaints/create/{orderId}', 'ComplaintController@store');
$router->post('/complaints/reply/{id}', 'ComplaintController@reply');
$router->get('/chat', 'ChatController@index');
$router->post('/chat', 'ChatController@send');
$router->get('/chat/poll', 'ChatController@poll');
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->get('/forgot', 'AuthController@forgotForm');
$router->post('/forgot', 'AuthController@forgotSubmit');
$router->get('/forgot/wait/{id}', 'AuthController@forgotWait');
$router->get('/forgot/status/{id}', 'AuthController@forgotStatus');
$router->get('/forgot/resend/{id}', 'AuthController@forgotResend');
$router->get('/logout', 'AuthController@logout');
$router->get('/profile', 'ProfileController@show');
$router->post('/profile', 'ProfileController@update');
$router->get('/notify/poll', 'NotifyController@poll');

$request = new Request($baseUrl);
$router->dispatch($request);
$router->get('/maintenance', 'PageController@maintenancePage');
$router->get('/blocked', 'PageController@blocked');
