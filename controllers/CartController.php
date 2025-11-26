<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Voucher.php';

class CartController extends Controller {
    private Product $productModel;
    private Voucher $voucherModel;
    public function __construct() {
        $this->productModel = new Product();
        $this->voucherModel = new Voucher();
    }
    public function index() {
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            unset($_SESSION['cart_voucher'], $_SESSION['cart_success']);
        }
        $appliedVoucher = $_SESSION['cart_voucher'] ?? null;
        $hasStockIssue = false;
        $stockWarnings = [];
        // Cập nhật dữ liệu sản phẩm mới nhất để tránh đặt hàng vượt tồn kho
        foreach ($cart as $id => $item) {
            $latest = $this->productModel->find((int)$id);
            if (!$latest) {
                unset($_SESSION['cart'][$id], $cart[$id]);
                $hasStockIssue = true;
                $stockWarnings[$id] = 'Sản phẩm không còn tồn tại.';
                continue;
            }
            $_SESSION['cart'][$id]['product'] = $latest;
            $cart[$id]['product'] = $latest;
            $available = (int)($latest['stock'] ?? 0);
            if ($available <= 0) {
                $hasStockIssue = true;
                $stockWarnings[$id] = 'Hết hàng';
            } elseif ($item['qty'] > $available) {
                $hasStockIssue = true;
                $stockWarnings[$id] = 'Vượt quá tồn kho (' . $available . ' sản phẩm)';
            }
        }
        $this->view('cart/index', [
            'cart' => $cart,
            'appliedVoucher' => $appliedVoucher,
            'hasStockIssue' => $hasStockIssue,
            'stockWarnings' => $stockWarnings,
        ]);
    }
    public function add() {
        $id = (int)($_POST['id'] ?? 0);
        $qty = max(1, (int)($_POST['qty'] ?? 1));
        $returnUrl = $this->resolveReturnUrl($_POST['redirect'] ?? null);
        $product = $this->productModel->find($id);
        if (!$product) {
            $_SESSION['flash_error'] = 'Sản phẩm hiện không khả dụng. Vui lòng thử lại sau.';
            $this->redirect($returnUrl);
        }
        $available = (int)($product['stock'] ?? 0);
        if ($available <= 0) {
            $_SESSION['flash_error'] = 'Sản phẩm đã hết hàng, vui lòng liên hệ để được hỗ trợ.';
            $this->redirect($returnUrl);
        }
        if ($qty > $available) {
            $qty = $available;
            $_SESSION['flash_error'] = 'Số lượng vượt tồn kho, đã giảm về ' . $available . ' sản phẩm.';
        }
        if (!isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] = ['product' => $product, 'qty' => 0];
        }
        $_SESSION['cart'][$id]['qty'] += $qty;
        $_SESSION['flash_success'] = 'Đã thêm "' . ($product['name'] ?? 'sản phẩm') . '" vào giỏ hàng';
        $this->redirect($returnUrl);
    }
    public function remove() {
        $id = (int)($_POST['id'] ?? 0);
        unset($_SESSION['cart'][$id]);
        if (empty($_SESSION['cart'])) {
            unset($_SESSION['cart_voucher'], $_SESSION['cart_success']);
        }
        $this->redirect('/cart');
    }

    public function applyVoucher() {
        $code = strtoupper(trim($_POST['voucher_code'] ?? ''));
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $_SESSION['cart_error'] = 'Chưa có sản phẩm trong giỏ hàng';
            unset($_SESSION['cart_success']);
            $this->redirect('/cart');
        }
        if ($code === '') {
            $_SESSION['cart_error'] = 'Vui lòng nhập mã giảm giá';
            unset($_SESSION['cart_success']);
            $this->redirect('/cart');
        }
        $voucher = $this->voucherModel->findByCode($code);
        if (!$voucher) {
            $_SESSION['cart_error'] = 'Mã giảm giá không tồn tại';
            unset($_SESSION['cart_voucher'], $_SESSION['cart_success']);
            $_SESSION['cart_voucher_code'] = $code;
            $this->redirect('/cart');
        }
        if ((int)($voucher['used_count'] ?? 0) >= (int)($voucher['usage_limit'] ?? 1)) {
            $_SESSION['cart_error'] = 'Mã giảm giá đã được sử dụng hết';
            unset($_SESSION['cart_voucher'], $_SESSION['cart_success']);
            $_SESSION['cart_voucher_code'] = $code;
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
                $_SESSION['cart_error'] = 'Mã giảm giá không áp dụng cho sản phẩm trong giỏ';
                unset($_SESSION['cart_voucher'], $_SESSION['cart_success']);
                $_SESSION['cart_voucher_code'] = $code;
                $this->redirect('/cart');
            }
        }
        $_SESSION['cart_voucher'] = $voucher;
        $_SESSION['cart_success'] = 'Áp dụng mã giảm giá thành công';
        $_SESSION['cart_voucher_code'] = $code;
        $this->redirect('/cart');
    }
    private function resolveReturnUrl(?string $target): string {
        $default = '/products';
        if (empty($target)) {
            $target = $_SERVER['HTTP_REFERER'] ?? '';
        }
        if (empty($target)) {
            return $default;
        }
        if (stripos($target, 'http') === 0) {
            $parts = parse_url($target);
            if (!$parts) {
                return $default;
            }
            $path = $parts['path'] ?? '';
            $query = isset($parts['query']) ? '?' . $parts['query'] : '';
            $target = $path . $query;
        }
        if (!str_starts_with($target, '/')) {
            $target = '/' . ltrim($target, '/');
        }
        $config = @include __DIR__ . '/../config/config.php';
        $base = rtrim($config['base_url'] ?? '', '/');
        if ($base && $base !== '/') {
            if (str_starts_with($target, $base . '/')) {
                $target = substr($target, strlen($base));
            } elseif ($target === $base) {
                $target = '/';
            }
        }
        return $target;
    }
}
