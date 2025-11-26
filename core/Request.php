<?php
class Request {
    private string $basePath;

    public function __construct(string $basePath = '') {
        // Cắt base path khỏi REQUEST_URI nếu chạy bên trong thư mục con
        $this->basePath = rtrim($basePath, '/');
    }

    public function method(): string {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function input(string $key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public function all(): array {
        return array_merge($_GET, $_POST);
    }

    public function uri(): string {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        if ($this->basePath && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath)) ?: '/';
        }
        return $uri === '' ? '/' : $uri;
    }
}
