<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Voucher.php';
require_once __DIR__ . '/../models/Product.php';

class OrderController extends Controller {
    private Order $orderModel;
    private Voucher $voucherModel;
    private Product $productModel;
    public function __construct(){
        $this->orderModel=new Order();
        $this->voucherModel=new Voucher();
        $this->productModel = new Product();
    }

    public function checkout(){
        Auth::requireLogin();
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        if ($userId <= 0) { $this->redirect('/login'); }
        $cart=$_SESSION['cart']??[];
        if(empty($cart)){ $this->redirect('/cart'); }
        // Kiểm tra tồn kho realtime trước khi tạo đơn
        foreach ($cart as $id => $item) {
            $latest = $this->productModel->find((int)$id);
            if (!$latest || (int)($latest['stock'] ?? 0) <= 0) {
                $_SESSION['cart_error'] = 'Một số sản phẩm đã hết hàng. Vui lòng liên hệ hoặc chọn sản phẩm khác.';
                $this->redirect('/cart');
            }
            if ($item['qty'] > (int)$latest['stock']) {
                $_SESSION['cart_error'] = 'Số lượng trong giỏ vượt tồn kho (' . $latest['name'] . '). Vui lòng điều chỉnh.';
                $this->redirect('/cart');
            }
            // cập nhật dữ liệu mới nhất
            $_SESSION['cart'][$id]['product'] = $latest;
            $cart[$id]['product'] = $latest;
        }
        $customer=[
            'name'=>$_POST['name']??'',
            'phone'=>$_POST['phone']??'',
            'email'=>$_POST['email']??'',
            'address'=>$_POST['address']??'',
            'note'=>$_POST['note']??'',
        ];
        $errors = [];
        if ($customer['name'] === '' || mb_strlen($customer['name']) > 30) {
            $errors[] = 'Họ tên không được để trống và tối đa 30 ký tự.';
        }
        if (!preg_match('/^0\\d{9}$/', $customer['phone'])) {
            $errors[] = 'Số điện thoại phải bắt đầu bằng 0 và đủ 10 chữ số.';
        }
        if ($customer['email'] === '' || mb_strlen($customer['email']) > 30 || !preg_match('/^[A-Za-z0-9._%+-]+@(gmail|email)[A-Za-z0-9.-]*\\.[A-Za-z0-9.-]+$/', $customer['email'])) {
            $errors[] = 'Email phải chứa @gmail hoặc @email, tối đa 30 ký tự.';
        }
        if ($customer['address'] === '' || mb_strlen($customer['address']) > 255) {
            $errors[] = 'Địa chỉ không được để trống và tối đa 255 ký tự.';
        }
        if ($errors) {
            $_SESSION['cart_error'] = implode(' ', $errors);
            $this->redirect('/cart');
        }
        $voucher = $_SESSION['cart_voucher'] ?? null;
        $paymentMethod = $_POST['payment_method'] ?? 'cod';
        $allowedMethods = ['cod','vnpay','momo'];
        if (!in_array($paymentMethod, $allowedMethods, true)) {
            $paymentMethod = 'cod';
        }
        if ($voucher) {
            // lấy lại dữ liệu mới nhất
            $voucher = $this->voucherModel->findByCode($voucher['code']);
            if (!$voucher) {
                $_SESSION['cart_error'] = 'Mã giảm giá không tồn tại';
                unset($_SESSION['cart_voucher'], $_SESSION['cart_success']);
                $this->redirect('/cart');
            }
            if ((int)$voucher['used_count'] >= (int)($voucher['usage_limit'] ?? 1)) {
                $_SESSION['cart_error'] = 'Mã giảm giá đã được sử dụng hết';
                unset($_SESSION['cart_voucher'], $_SESSION['cart_success']);
                $this->redirect('/cart');
            }
            if (!empty($voucher['category_id'])) {
                $applies = false;
                foreach ($cart as $item) {
                    if ((int)$item['product']['category_id'] === (int)$voucher['category_id']) {
                        $applies = true;
                        break;
                    }
                }
                if (!$applies) {
                    $_SESSION['cart_error'] = 'Mã giảm giá không áp dụng cho sản phẩm trong giỏ hàng';
                    unset($_SESSION['cart_voucher'], $_SESSION['cart_success']);
                    $this->redirect('/cart');
                }
            }
        }
        $order=$this->orderModel->create($userId,$cart,$customer,$voucher,$paymentMethod);
        $orderId = (int)$order['id'];
        $orderCode = $order['code'];
        if ($voucher) {
            $this->voucherModel->incrementUsage($voucher['id']);
        }
        unset($_SESSION['cart'], $_SESSION['cart_voucher'], $_SESSION['cart_success']);
        if ($paymentMethod === 'cod') {
            $this->view('order/success',[
                'orderId'=>$orderCode,
                'voucherMessage'=>$voucher ? 'Áp dụng mã giảm giá thành công' : null,
                'paymentMethod' => $paymentMethod
            ]);
        } else {
            $this->redirect('/payment/' . $paymentMethod . '/' . $orderId);
        }
    }
    public function myOrders(){
        Auth::requireLogin();
        $this->orderModel->markAllRead($_SESSION['user']['id']);
        $orders=$this->orderModel->byUser($_SESSION['user']['id']);
        $this->view('order/list',compact('orders'));
    }

    public function cancel($id) {
        Auth::requireLogin();
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        if ($userId <= 0) { $this->redirect('/login'); }
        $ok = $this->orderModel->cancelByUser((int)$id, $userId);
        $_SESSION['order_notice'] = $ok ? 'Đơn hàng đã được hủy và tồn kho đã khôi phục.' : 'Không thể hủy đơn hàng này.';
        $this->redirect('/orders');
    }
}
