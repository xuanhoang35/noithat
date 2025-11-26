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
        // cập nhật trạng thái online
        if (!empty($user['id'])) {
            require_once __DIR__ . '/../models/User.php';
            try { (new User())->setOnline((int)$user['id'], true); } catch (\Throwable $e) {}
        }
    }

    public static function logout(): void {
        if (!empty($_SESSION['user']['id'])) {
            require_once __DIR__ . '/../models/User.php';
            try { (new User())->setOnline((int)$_SESSION['user']['id'], false); } catch (\Throwable $e) {}
        }
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
