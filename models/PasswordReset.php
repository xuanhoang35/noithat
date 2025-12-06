<?php
require_once __DIR__ . '/../core/Model.php';

class PasswordReset extends Model {
    public function __construct() {
        parent::__construct();
        $this->ensureSchema();
    }

    private function ensureSchema(): void {
        try { $this->db->exec("ALTER TABLE users ADD COLUMN reset_token VARCHAR(100) NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE users ADD COLUMN reset_status ENUM('pending','completed','delivered') NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE users ADD COLUMN reset_password_plain VARCHAR(255) NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE users ADD COLUMN reset_requested_at DATETIME NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE users ADD COLUMN reset_completed_at DATETIME NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE users ADD COLUMN reset_delivered_at DATETIME NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE users ADD COLUMN reset_email VARCHAR(150) NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE users ADD COLUMN reset_phone VARCHAR(50) NULL"); } catch (\Throwable $e) {}
    }

    private function generateToken(): string {
        return (string)(time() . rand(1000, 9999));
    }

    public function create(int $userId, string $email, string $phone): string {
        $token = $this->generateToken();
        $stmt = $this->db->prepare('UPDATE users SET reset_token=?, reset_status="pending", reset_password_plain=NULL, reset_requested_at=NOW(), reset_completed_at=NULL, reset_delivered_at=NULL, reset_email=?, reset_phone=? WHERE id=?');
        $stmt->execute([$token, $email, $phone, $userId]);
        return $token;
    }

    public function all(): array {
        $stmt = $this->db->query('SELECT id AS user_id, name, email, reset_token AS id, reset_status AS status, reset_password_plain AS new_password_plain, reset_requested_at AS created_at, reset_completed_at AS completed_at, reset_delivered_at AS delivered_at, reset_email, reset_phone FROM users WHERE reset_token IS NOT NULL ORDER BY reset_requested_at DESC');
        return $stmt->fetchAll();
    }

    public function find(string $token): ?array {
        $stmt = $this->db->prepare('SELECT id AS user_id, reset_token AS id, reset_status AS status, reset_password_plain AS new_password_plain, reset_requested_at AS created_at, reset_completed_at AS completed_at, reset_delivered_at AS delivered_at, reset_email AS email, reset_phone AS phone FROM users WHERE reset_token=? LIMIT 1');
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function complete(string $token, string $newPassword): void {
        $reset = $this->find($token);
        if (!$reset || $reset['status'] === 'delivered') {
            return;
        }
        $this->db->beginTransaction();
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateUser = $this->db->prepare('UPDATE users SET password=?, password_plain=?, reset_status="completed", reset_password_plain=?, reset_completed_at=NOW() WHERE reset_token=?');
        $updateUser->execute([$hash, $newPassword, $newPassword, $token]);
        $this->db->commit();
    }

    public function markDelivered(string $token): void {
        $stmt = $this->db->prepare('UPDATE users SET reset_status="delivered", reset_delivered_at=NOW() WHERE reset_token=? AND reset_status="completed"');
        $stmt->execute([$token]);
    }

    public function resend(string $token): ?string {
        $reset = $this->find($token);
        if (!$reset) {
            return null;
        }
        return $this->create((int)$reset['user_id'], $reset['email'], $reset['phone']);
    }

    public function reject(string $token): void {
        $stmt = $this->db->prepare('UPDATE users SET reset_token=NULL, reset_status="delivered", reset_password_plain="__REJECTED__", reset_requested_at=NULL, reset_completed_at=NULL, reset_delivered_at=NOW(), reset_email=NULL, reset_phone=NULL WHERE reset_token=?');
        $stmt->execute([$token]);
    }

}
