<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Order.php';

class PaymentController extends Controller {
    private Order $orderModel;

    public function __construct() {
        $this->orderModel = new Order();
    }

    private function requireOrder(string $method, int $orderId): ?array {
        Auth::requireLogin();
        $allowed = ['vnpay','momo'];
        if (!in_array($method, $allowed, true)) {
            http_response_code(404);
            echo 'Phương thức không hỗ trợ';
            return null;
        }
        $order = $this->orderModel->findByIdForUser($orderId, $_SESSION['user']['id']);
        if (!$order) {
            http_response_code(404);
            echo 'Không tìm thấy đơn hàng';
            return null;
        }
        return $order;
    }

    public function show($method, $orderId) {
        $order = $this->requireOrder($method, (int)$orderId);
        if (!$order) return;
        $stage = $_GET['stage'] ?? 'banner';
        if ($stage !== 'qr') {
            $banners = [
                'vnpay' => asset_url('public/bank/banner-vnp.jpg'),
                'momo' => asset_url('public/bank/banner-momo.png')
            ];
            $redirect = base_url('payment/' . $method . '/' . $order['id']) . '?stage=qr';
            $this->view('order/payment_banner', [
                'order' => $order,
                'method' => $method,
                'banner' => $banners[$method] ?? ($banners['vnpay']),
                'redirectUrl' => $redirect
            ]);
            return;
        }
        if ($method === 'momo' && $stage === 'qr') {
            include __DIR__ . '/../views/order/payment_momo.php';
            return;
        }
        if ($method === 'vnpay' && $stage === 'qr') {
            include __DIR__ . '/../views/order/payment_vnpay.php';
            return;
        }
        $this->view('order/payment', [
            'order' => $order,
            'method' => $method,
        ]);
    }

    public function confirm($method, $orderId) {
        $order = $this->requireOrder($method, (int)$orderId);
        if (!$order) return;
        $result = $_POST['result'] ?? 'success';
        $labels = [
            'vnpay' => 'VNPay',
            'momo' => 'MoMo'
        ];
        if ($result === 'success') {
            $this->orderModel->updateStatus($order['id'], 'processing');
            $_SESSION['order_notice'] = 'Thanh toán mô phỏng qua ' . ($labels[$method] ?? $method) . ' thành công. Đơn hàng đang được xử lý.';
            $this->redirect('/');
        } else {
            $this->orderModel->updateStatus($order['id'], 'cancelled');
            $_SESSION['order_notice'] = 'Thanh toán thất bại hoặc bị hủy. Đơn hàng đã được hủy.';
            $this->redirect('/orders');
        }
    }
}
