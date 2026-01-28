<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../models/Voucher.php';
require_once __DIR__ . '/../../models/Category.php';

class VoucherAdminController extends Controller {
    private Voucher $voucherModel;
    private Category $categoryModel;

    public function __construct() {
        Auth::requireAdmin();
        $this->voucherModel = new Voucher();
        $this->categoryModel = new Category();
    }

    public function index() {
        $search = trim($_GET['q'] ?? '');
        $vouchers = $this->voucherModel->all($search);
        $categories = $this->categoryModel->all();
        $this->view('admin/voucher/index', compact('vouchers', 'categories', 'search'));
    }

    public function store() {
        $data = [
            'code' => trim($_POST['code'] ?? ''),
            'discount_percent' => (int)($_POST['discount_percent'] ?? 0),
            'category_id' => $_POST['category_id'] ?? '',
            'description' => trim($_POST['description'] ?? ''),
            'usage_limit' => (int)($_POST['usage_limit'] ?? 1),
        ];
        if ($data['code'] === '' || $data['discount_percent'] <= 0) {
            $_SESSION['flash_error'] = 'Vui lòng nhập mã và % giảm hợp lệ.';
            $this->redirect('/admin.php/vouchers');
        }
        if ($this->voucherModel->existsByCode($data['code'])) {
            $_SESSION['flash_error'] = 'Mã voucher đã tồn tại.';
            $this->redirect('/admin.php/vouchers');
        }
        $this->voucherModel->create($data);
        $_SESSION['flash_success'] = 'Đã thêm mã giảm giá.';
        $this->redirect('/admin.php/vouchers');
    }

    public function update($id) {
        $data = [
            'code' => trim($_POST['code'] ?? ''),
            'discount_percent' => (int)($_POST['discount_percent'] ?? 0),
            'category_id' => $_POST['category_id'] ?? '',
            'description' => trim($_POST['description'] ?? ''),
            'usage_limit' => (int)($_POST['usage_limit'] ?? 1),
        ];
        $id = (int)$id;
        if ($data['code'] === '' || $data['discount_percent'] <= 0) {
            $_SESSION['flash_error'] = 'Vui lòng nhập mã và % giảm hợp lệ.';
            $this->redirect('/admin.php/vouchers');
        }
        if ($this->voucherModel->existsByCode($data['code'], $id)) {
            $_SESSION['flash_error'] = 'Mã voucher đã tồn tại.';
            $this->redirect('/admin.php/vouchers');
        }
        $this->voucherModel->update($id, $data);
        $_SESSION['flash_success'] = 'Đã cập nhật mã giảm giá.';
        $this->redirect('/admin.php/vouchers');
    }

    public function delete($id) {
        $this->voucherModel->delete((int)$id);
        $this->redirect('/admin.php/vouchers');
    }
}
