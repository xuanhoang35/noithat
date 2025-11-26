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
        $mode = $_POST['mode'] ?? 'content';
        $title = $data['title'] ?? '';
        $subtitle = $data['subtitle'] ?? '';
        $message = $data['message'] ?? '';
        $enabled = (bool)($data['enabled'] ?? false);
        $imagePath = $data['image'] ?? '';

        if ($mode === 'content') {
            $title = trim($_POST['title'] ?? $data['title']);
            $subtitle = trim($_POST['subtitle'] ?? $data['subtitle']);
            $message = trim($_POST['message'] ?? $data['message']);

            if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
                $uploadDir = __DIR__ . '/../../public/uploads/maintenance';
                if (!is_dir($uploadDir) && !@mkdir($uploadDir, 0777, true)) {
                    $_SESSION['flash_error'] = 'Không tạo được thư mục lưu ảnh. Vui lòng kiểm tra quyền ghi public/uploads/maintenance.';
                    $this->redirect('/admin.php/maintenance');
                }
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($_FILES['image']['name'], PATHINFO_FILENAME));
                $filename = $safe . '_' . time() . ($ext ? '.' . $ext : '');
                $dest = $uploadDir . '/' . $filename;
                if (is_dir($uploadDir) && is_writable($uploadDir) && move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    $imagePath = 'public/uploads/maintenance/' . $filename;
                } else {
                    $_SESSION['flash_error'] = 'Tải ảnh thất bại. Vui lòng thử lại và kiểm tra quyền ghi thư mục uploads/maintenance.';
                    $this->redirect('/admin.php/maintenance');
                }
            }
            $save = [
                'enabled' => $enabled,
                'title' => $title,
                'subtitle' => $subtitle,
                'message' => $message,
                'image' => $imagePath
            ];
            $this->save($save);
            $_SESSION['flash_success'] = 'Đã lưu nội dung trang bảo trì.';
            $this->redirect('/admin.php/maintenance');
            return;
        }

        // mode toggle: chỉ bật/tắt dựa trên nội dung đã lưu
        $enabled = isset($_POST['enabled']) && $_POST['enabled'] === '1';
        if ($enabled && ($title === '' || $imagePath === '')) {
            $_SESSION['flash_error'] = 'Vui lòng thiết kế đủ tiêu đề và ảnh trước khi đóng trang web.';
            $this->redirect('/admin.php/maintenance');
        }
        $data['enabled'] = $enabled;
        $this->save($data);
        $_SESSION['flash_success'] = $enabled ? 'Đã bật chế độ bảo trì.' : 'Đã tắt chế độ bảo trì.';
        $this->redirect('/admin.php/maintenance');
    }
}
