<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../models/Slider.php';

class SliderAdminController extends Controller {
    private Slider $sliderModel;
    public function __construct() {
        Auth::requireAdmin();
        $this->sliderModel = new Slider();
    }

    public function index() {
        $sliders = $this->sliderModel->all(false);
        $this->view('admin/slider/index', compact('sliders'));
    }

    public function store() {
        $path = '';
        if (!empty($_FILES['image_file']['tmp_name']) && is_uploaded_file($_FILES['image_file']['tmp_name'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/slider';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($_FILES['image_file']['name'], PATHINFO_FILENAME));
            $filename = $safeName . '_' . time() . ($ext ? '.' . $ext : '');
            $dest = $uploadDir . '/' . $filename;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $dest)) {
                $path = 'public/uploads/slider/' . $filename;
            }
        } elseif (!empty($_POST['image_url'])) {
            $path = trim($_POST['image_url']);
        }
        if ($path !== '') {
            if ($this->sliderModel->existsByImage($path)) {
                $_SESSION['flash_error'] = 'Ảnh này đã tồn tại trong slider.';
                $this->redirect('/admin.php/sliders');
            }
            $this->sliderModel->create($path);
            $_SESSION['flash_success'] = 'Đã thêm ảnh vào slider.';
        }
        $this->redirect('/admin.php/sliders');
    }

    public function delete($id) {
        $this->sliderModel->delete((int)$id);
        $this->redirect('/admin.php/sliders');
    }
}
