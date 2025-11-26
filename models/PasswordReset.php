<?php
require_once __DIR__ . '/../core/Model.php';

class PasswordReset extends Model {
    public function __construct() {
        parent::__construct();
        $this->ensureSchema();
    }

    private function ensureSchema(): void {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                email VARCHAR(150) NOT NULL,
                phone VARCHAR(50) NOT NULL,
                status ENUM('pending','completed','delivered') DEFAULT 'pending',
                new_password_plain VARCHAR(255) NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                completed_at DATETIME NULL,
                delivered_at DATETIME NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
    }

    public function create(int $userId, string $email, string $phone): int {
        $stmt = $this->db->prepare('INSERT INTO password_resets(user_id,email,phone,status) VALUES(?,?,?,"pending")');
        $stmt->execute([$userId, $email, $phone]);
        return (int)$this->db->lastInsertId();
    }

    public function all(): array {
        $stmt = $this->db->query('SELECT pr.*, u.name FROM password_resets pr LEFT JOIN users u ON pr.user_id = u.id ORDER BY pr.created_at DESC');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM password_resets WHERE id=? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function complete(int $id, string $newPassword): void {
        $reset = $this->find($id);
        if (!$reset || $reset['status'] === 'delivered') {
            return;
        }
        $this->db->beginTransaction();
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateUser = $this->db->prepare('UPDATE users SET password=? WHERE id=?');
        $updateUser->execute([$hash, $reset['user_id']]);
        $stmt = $this->db->prepare('UPDATE password_resets SET status="completed", new_password_plain=?, completed_at=NOW() WHERE id=?');
        $stmt->execute([$newPassword, $id]);
        $this->db->commit();
    }

    public function markDelivered(int $id): void {
        $stmt = $this->db->prepare('UPDATE password_resets SET status="delivered", delivered_at=NOW() WHERE id=? AND status="completed"');
        $stmt->execute([$id]);
    }

    public function resend(int $id): ?int {
        $reset = $this->find($id);
        if (!$reset) {
            return null;
        }
        return $this->create((int)$reset['user_id'], $reset['email'], $reset['phone']);
    }

}
