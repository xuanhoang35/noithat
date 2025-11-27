<?php
// admin.php
session_start();

$config = include __DIR__ . '/config/config.php';
require __DIR__ . '/core/Request.php';
require __DIR__ . '/core/Response.php';
require __DIR__ . '/core/Router.php';
require __DIR__ . '/core/Auth.php';
require __DIR__ . '/core/Controller.php';
require __DIR__ . '/core/Model.php';
require __DIR__ . '/helpers/url_helper.php';

Auth::requireAdmin();
// Đặt base path cho admin: bao gồm script admin.php để router nhận uri đúng
$baseUrl = ($config['base_url'] ?? '') . '/admin.php';
$router = new Router();

$router->get('/', 'admin/AdminController@index');
$router->get('/products', 'admin/ProductAdminController@index');
$router->post('/products/create', 'admin/ProductAdminController@store');
$router->get('/products/edit/{id}', 'admin/ProductAdminController@edit');
$router->post('/products/edit/{id}', 'admin/ProductAdminController@update');
$router->post('/products/delete/{id}', 'admin/ProductAdminController@delete');
$router->get('/categories', 'admin/CategoryAdminController@index');
$router->post('/categories/create', 'admin/CategoryAdminController@store');
$router->post('/categories/update/{id}', 'admin/CategoryAdminController@update');
$router->post('/categories/delete/{id}', 'admin/CategoryAdminController@delete');
$router->get('/orders', 'admin/OrderAdminController@index');
$router->post('/orders/update-status/{id}', 'admin/OrderAdminController@updateStatus');
$router->get('/users', 'admin/UserAdminController@index');
$router->post('/users/toggle/{id}', 'admin/UserAdminController@toggleActive');
$router->post('/users/reset-password/{id}', 'admin/UserAdminController@resetPassword');
$router->post('/users/delete/{id}', 'admin/UserAdminController@delete');
$router->get('/complaints', 'admin/ComplaintAdminController@index');
$router->post('/complaints/update-status/{id}', 'admin/ComplaintAdminController@updateStatus');
$router->post('/complaints/reply/{id}', 'admin/ComplaintAdminController@reply');
$router->get('/chats', 'admin/ChatAdminController@index');
$router->get('/chats/show/{id}', 'admin/ChatAdminController@show');
$router->get('/chats/poll/{id}', 'admin/ChatAdminController@poll');
$router->post('/chats/reply/{id}', 'admin/ChatAdminController@reply');
$router->get('/notify/poll', 'admin/NotifyAdminController@poll');
$router->get('/maintenance', 'admin/MaintenanceAdminController@index');
$router->post('/maintenance', 'admin/MaintenanceAdminController@update');
$router->post('/maintenance/upload', 'admin/MaintenanceAdminController@uploadMedia');
$router->get('/services', 'admin/ServiceAdminController@index');
$router->post('/services/create', 'admin/ServiceAdminController@store');
$router->post('/services/update/{id}', 'admin/ServiceAdminController@update');
$router->post('/services/delete/{id}', 'admin/ServiceAdminController@delete');
$router->get('/sliders', 'admin/SliderAdminController@index');
$router->post('/sliders/create', 'admin/SliderAdminController@store');
$router->post('/sliders/delete/{id}', 'admin/SliderAdminController@delete');
$router->get('/voucher', function() use ($baseUrl) {
    header('Location: ' . $baseUrl . '/vouchers');
    exit;
});
$router->get('/vouchers', 'admin/VoucherAdminController@index');
$router->post('/vouchers/create', 'admin/VoucherAdminController@store');
$router->post('/vouchers/update/{id}', 'admin/VoucherAdminController@update');
$router->post('/vouchers/delete/{id}', 'admin/VoucherAdminController@delete');

$request = new Request($baseUrl);
$router->dispatch($request);
