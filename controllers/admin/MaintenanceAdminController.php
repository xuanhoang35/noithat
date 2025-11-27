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
        $defaults = [
            'enabled' => false,
            'title' => 'Chúng tôi đang bảo trì',
            'subtitle' => 'Nội Thất Store sẽ trở lại sớm nhất',
            'message' => 'Xin lỗi vì sự bất tiện.',
            'image' => '',
            'images' => [],
            'video' => ''
        ];
        if (file_exists($this->configFile)) {
            $json = file_get_contents($this->configFile);
            $data = json_decode($json, true);
            if (is_array($data)) {
                $data = array_merge($defaults, $data);
                $images = [];
                if (!empty($data['images']) && is_array($data['images'])) {
                    foreach ($data['images'] as $img) {
                        $img = trim((string)$img);
                        if ($img !== '') {
                            $images[] = $img;
                        }
                    }
                }
                if (empty($images) && !empty($data['image'])) {
                    $images[] = $data['image'];
                }
                $data['images'] = array_values(array_unique($images));
                $data['image'] = $data['images'][0] ?? '';
                return $data;
            }
        }
        return $defaults;
    }

    private function save(array $data): void {
        $images = [];
        if (!empty($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $img) {
                $img = trim((string)$img);
                if ($img !== '') {
                    $images[] = $img;
                }
            }
        }
        if (empty($images) && !empty($data['image'])) {
            $images[] = trim((string)$data['image']);
        }
        $data['images'] = array_values(array_unique($images));
        $data['image'] = $data['images'][0] ?? '';
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

    private function deleteLocalFiles(array $paths): void {
        foreach ($paths as $p) {
            $this->deleteIfLocal((string)$p);
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
        $images = $data['images'] ?? [];
        $imagePath = $images[0] ?? ($data['image'] ?? '');
        $videoPath = $data['video'] ?? '';
        $videoUrl = trim($_POST['video_url'] ?? '');
        if ($videoUrl && !preg_match('#^https?://#', $videoUrl)) {
            $videoUrl = '';
        }

        // Gom danh sách file ảnh (hỗ trợ chọn nhiều hoặc một)
        $uploadSets = [];
        if (!empty($_FILES['images'])) {
            $uploadSets[] = $_FILES['images'];
        }
        if (!empty($_FILES['image']) && empty($uploadSets)) {
            // fallback nếu form cũ
            $uploadSets[] = $_FILES['image'];
        }

        $collected = [];
        foreach ($uploadSets as $set) {
            $isArray = is_array($set['name']);
            $count = $isArray ? count($set['name']) : 1;
            for ($i = 0; $i < $count; $i++) {
                $tmp = $isArray ? ($set['tmp_name'][$i] ?? '') : ($set['tmp_name'] ?? '');
                if (!$tmp) continue;
                $collected[] = [
                    'tmp' => $tmp,
                    'name' => $isArray ? ($set['name'][$i] ?? '') : ($set['name'] ?? ''),
                    'size' => $isArray ? ($set['size'][$i] ?? 0) : ($set['size'] ?? 0),
                    'error' => $isArray ? ($set['error'][$i] ?? UPLOAD_ERR_OK) : ($set['error'] ?? UPLOAD_ERR_OK)
                ];
            }
        }

        if (!empty($collected)) {
            $uploadDir = __DIR__ . '/../../public/uploads/maintenance';
            if (!is_dir($uploadDir) && !@mkdir($uploadDir, 0777, true)) {
                $_SESSION['flash_error'] = 'Không tạo được thư mục lưu ảnh. Vui lòng kiểm tra quyền ghi public/uploads/maintenance.';
                $this->redirect('/admin.php/maintenance');
            }
            $newImages = [];
            $created = [];
            foreach ($collected as $file) {
                if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                    $_SESSION['flash_error'] = 'Tải ảnh thất bại, vui lòng thử lại.';
                    $this->deleteLocalFiles($created);
                    $this->redirect('/admin.php/maintenance');
                }
                if ($file['size'] > $maxSize) {
                    $_SESSION['flash_error'] = 'Ảnh vượt quá 50MB. Vui lòng chọn file nhỏ hơn.';
                    $this->deleteLocalFiles($created);
                    $this->redirect('/admin.php/maintenance');
                }
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
                $filename = $safe . '_' . time() . '_' . uniqid('', false) . ($ext ? '.' . $ext : '');
                $dest = $uploadDir . '/' . $filename;
                if (is_dir($uploadDir) && is_writable($uploadDir) && is_uploaded_file($file['tmp']) && move_uploaded_file($file['tmp'], $dest)) {
                    $newImages[] = 'public/uploads/maintenance/' . $filename;
                    $created[] = 'public/uploads/maintenance/' . $filename;
                } else {
                    $_SESSION['flash_error'] = 'Tải ảnh thất bại. Vui lòng thử lại và kiểm tra quyền ghi thư mục uploads/maintenance.';
                    $this->deleteLocalFiles($created);
                    $this->redirect('/admin.php/maintenance');
                }
            }
            if (!empty($newImages)) {
                $this->deleteLocalFiles($images);
                $this->deleteIfLocal($videoPath);
                $videoPath = '';
                $images = $newImages;
            }
        }

        // Sử dụng đường dẫn đã upload trước (chunk) nếu có
        if (!empty($_POST['uploaded_image'])) {
            $imagePath = trim($_POST['uploaded_image']);
            if ($imagePath) {
                $this->deleteIfLocal($videoPath);
                $videoPath = '';
                $images = [$imagePath];
            }
        }
        if (!empty($_POST['uploaded_video'])) {
            $videoPath = trim($_POST['uploaded_video']);
            if ($videoPath) {
                $this->deleteLocalFiles($images);
                $images = [];
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
                $this->deleteLocalFiles($images); // video mới => bỏ ảnh cũ
                $images = [];
                $imagePath = '';
                $videoPath = 'public/uploads/maintenance/' . $filename;
            } else {
                $_SESSION['flash_error'] = 'Tải video thất bại. Vui lòng thử lại và kiểm tra quyền ghi thư mục uploads/maintenance.';
                $this->redirect('/admin.php/maintenance');
            }
        }

        // Ưu tiên video URL (Google Drive, CDN...), bỏ file cục bộ nếu nhập
        if (!empty($videoUrl)) {
            $this->deleteLocalFiles($images);
            $this->deleteIfLocal($videoPath);
            $images = [];
            $imagePath = '';
            $videoPath = $videoUrl; // lưu trực tiếp URL
        }

        // Khi tắt bảo trì: xóa media tạm
        if (!$enabled) {
            $this->deleteLocalFiles($images);
            $this->deleteIfLocal($videoPath);
            $images = [];
            $imagePath = '';
            $videoPath = '';
            $videoUrl = '';
        }

        $imagePath = $images[0] ?? '';

        $save = [
            'enabled' => $enabled,
            'title' => $title,
            'subtitle' => $subtitle,
            'message' => $message,
            'image' => $imagePath,
            'images' => $images,
            'video' => $videoPath ?: $videoUrl
        ];
        $this->save($save);
        $_SESSION['flash_success'] = $enabled ? 'Đã bật chế độ bảo trì.' : 'Đã lưu nội dung và tắt bảo trì.';
        $this->redirect('/admin.php/maintenance');
    }
}
