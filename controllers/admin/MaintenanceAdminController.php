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
            'image' => '',
            'video' => ''
        ];
    }

    private function save(array $data): void {
        file_put_contents($this->configFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function deleteIfLocal(string $path): void {
        if (!$path) return;
        $full = __DIR__ . '/../../' . $path;
        $root = realpath(__DIR__ . '/../../public/uploads/maintenance');
        $fullReal = realpath($full);
        if ($root && $fullReal && strpos($fullReal, $root) === 0 && file_exists($fullReal)) {
            @unlink($fullReal);
        }
    }

    public function index() {
        $data = $this->load();
        $this->view('admin/maintenance/index', ['config' => $data]);
    }

    public function update() {
        $maxSize = 50 * 1024 * 1024; // 50MB
        $data = $this->load();
        $title = trim($_POST['title'] ?? $data['title']);
        $subtitle = trim($_POST['subtitle'] ?? $data['subtitle']);
        $message = trim($_POST['message'] ?? $data['message']);
        $enabled = isset($_POST['enabled']) && $_POST['enabled'] === '1';
        $imagePath = $data['image'] ?? '';
        $videoPath = $data['video'] ?? '';
        $videoUrl = trim($_POST['video_url'] ?? '');
        if ($videoUrl && !preg_match('#^https?://#', $videoUrl)) {
            $videoUrl = '';
        }

        if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            if ($_FILES['image']['size'] > $maxSize) {
                $_SESSION['flash_error'] = 'Ảnh vượt quá 50MB. Vui lòng chọn file nhỏ hơn.';
                $this->redirect('/admin.php/maintenance');
            }
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
                $this->deleteIfLocal($videoPath); // ảnh mới => bỏ video cũ
                $videoPath = '';
                $imagePath = 'public/uploads/maintenance/' . $filename;
            } else {
                $_SESSION['flash_error'] = 'Tải ảnh thất bại. Vui lòng thử lại và kiểm tra quyền ghi thư mục uploads/maintenance.';
                $this->redirect('/admin.php/maintenance');
            }
        }

        // Sử dụng đường dẫn đã upload trước (chunk) nếu có
        if (!empty($_POST['uploaded_image'])) {
            $imagePath = trim($_POST['uploaded_image']);
            if ($imagePath) {
                $this->deleteIfLocal($videoPath);
                $videoPath = '';
            }
        }
        if (!empty($_POST['uploaded_video'])) {
            $videoPath = trim($_POST['uploaded_video']);
            if ($videoPath) {
                $this->deleteIfLocal($imagePath);
                $imagePath = '';
            }
        }

        if (!empty($_FILES['video']['tmp_name']) && is_uploaded_file($_FILES['video']['tmp_name'])) {
            if ($_FILES['video']['size'] > $maxSize) {
                $_SESSION['flash_error'] = 'Video vượt quá 50MB. Vui lòng chọn file nhỏ hơn.';
                $this->redirect('/admin.php/maintenance');
            }
            $uploadDir = __DIR__ . '/../../public/uploads/maintenance';
            if (!is_dir($uploadDir) && !@mkdir($uploadDir, 0777, true)) {
                $_SESSION['flash_error'] = 'Không tạo được thư mục lưu video. Vui lòng kiểm tra quyền ghi public/uploads/maintenance.';
                $this->redirect('/admin.php/maintenance');
            }
            $ext = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
            $safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($_FILES['video']['name'], PATHINFO_FILENAME));
            $filename = $safe . '_' . time() . ($ext ? '.' . $ext : '');
            $dest = $uploadDir . '/' . $filename;
            if (is_dir($uploadDir) && is_writable($uploadDir) && move_uploaded_file($_FILES['video']['tmp_name'], $dest)) {
                $this->deleteIfLocal($imagePath); // video mới => bỏ ảnh cũ
                $imagePath = '';
                $videoPath = 'public/uploads/maintenance/' . $filename;
            } else {
                $_SESSION['flash_error'] = 'Tải video thất bại. Vui lòng thử lại và kiểm tra quyền ghi thư mục uploads/maintenance.';
                $this->redirect('/admin.php/maintenance');
            }
        }

        // Ưu tiên video URL (Google Drive, CDN...), bỏ file cục bộ nếu nhập
        if (!empty($videoUrl)) {
            $this->deleteIfLocal($imagePath);
            $this->deleteIfLocal($videoPath);
            $imagePath = '';
            $videoPath = $videoUrl; // lưu trực tiếp URL
        }

        // Khi tắt bảo trì: xóa media tạm
        if (!$enabled) {
            $this->deleteIfLocal($imagePath);
            $this->deleteIfLocal($videoPath);
            $imagePath = '';
            $videoPath = '';
            $videoUrl = '';
        }

        $save = [
            'enabled' => $enabled,
            'title' => $title,
            'subtitle' => $subtitle,
            'message' => $message,
            'image' => $imagePath,
            'video' => $videoPath ?: $videoUrl
        ];
        $this->save($save);
        $_SESSION['flash_success'] = $enabled ? 'Đã bật chế độ bảo trì.' : 'Đã lưu nội dung và tắt bảo trì.';
        $this->redirect('/admin.php/maintenance');
    }
}
