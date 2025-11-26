<?php
class Controller {
    protected function view(string $path, array $data = []): void {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $path . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(404);
            echo "View not found: {$path}";
            return;
        }
        include $viewFile;
    }

    protected function redirect(string $url): void {
        // Thêm base_url nếu dùng subfolder (vd. /shop)
        if (strpos($url, 'http') !== 0) {
            $config = @include __DIR__ . '/../config/config.php';
            $base = rtrim($config['base_url'] ?? '', '/');
            if ($base && str_starts_with($url, '/')) {
                $url = $base . $url;
            }
        }
        header('Location: ' . $url);
        exit;
    }
}
