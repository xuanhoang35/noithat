<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';

class MaintenanceAdminController extends Controller {
    private string $configFile;
    public function __construct() {
        Auth::requireAdmin();
        $this->configFile = __DIR__ . '/../../config/maintenance.json';
    }

    private function load(): array {
        if (file_exists($this->configFile)) {
            $json = file_get_contents($this->configFile);
            $data = json_decode($json, true);
            if (is_array($data)) return $data;
        }
        return [
            'enabled' => false,
            'title' => 'Chúng tôi đang bảo trì',
            'subtitle' => 'Nội Thất Store sẽ trở lại sớm nhất',
            'message' => 'Xin lỗi vì sự bất tiện.',
            'image' => ''
        ];
    }

    private function save(array $data): void {
        file_put_contents($this->configFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function index() {
        $data = $this->load();
        $this->view('admin/maintenance/index', ['config' => $data]);
    }

    public function update() {
        $data = $this->load();
        $title = trim($_POST['title'] ?? $data['title']);
        $subtitle = trim($_POST['subtitle'] ?? $data['subtitle']);
        $message = trim($_POST['message'] ?? $data['message']);
        $enabled = isset($_POST['enabled']) && $_POST['enabled'] === '1';
        $imagePath = $data['image'] ?? '';

        if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/maintenance';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($_FILES['image']['name'], PATHINFO_FILENAME));
            $filename = $safe . '_' . time() . ($ext ? '.' . $ext : '');
            $dest = $uploadDir . '/' . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $imagePath = 'public/uploads/maintenance/' . $filename;
            }
        } elseif (!empty($_POST['image_url'])) {
            $imagePath = trim($_POST['image_url']);
        }

        if ($enabled && ($title === '' || $imagePath === '')) {
            $_SESSION['flash_error'] = 'Vui lòng thiết kế đủ tiêu đề và ảnh trước khi đóng trang web.';
            $this->redirect('/admin.php/maintenance');
        }

        $data = [
            'enabled' => $enabled,
            'title' => $title,
            'subtitle' => $subtitle,
            'message' => $message,
            'image' => $imagePath
        ];
        $this->save($data);
        $_SESSION['flash_success'] = 'Đã cập nhật chế độ bảo trì.';
        $this->redirect('/admin.php/maintenance');
    }
}
