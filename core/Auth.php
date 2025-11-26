<?php
class Auth {
    public static function user(): ?array {
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool {
        return isset($_SESSION['user']);
    }

    public static function isAdmin(): bool {
        return self::check() && ($_SESSION['user']['role'] ?? 'user') === 'admin';
    }

    public static function login(array $user): void {
        $_SESSION['user'] = $user;
    }

    public static function logout(): void {
        unset($_SESSION['user']);
    }

    public static function requireLogin(): void {
        if (!self::check()) {
            $base = self::basePath();
            header('Location: ' . $base . '/login');
            exit;
        }
    }

    public static function requireAdmin(): void {
        if (!self::isAdmin()) {
            $base = self::basePath();
            header('Location: ' . $base . '/login');
            exit;
        }
    }

    private static function basePath(): string {
        static $base = null;
        if ($base === null) {
            $config = @include __DIR__ . '/../config/config.php';
            $base = rtrim($config['base_url'] ?? '', '/');
        }
        // bảo đảm luôn có dấu / đầu
        return $base === '' ? '' : $base;
    }
}
