<?php
require_once __DIR__ . '/../core/Model.php';

/**
 * Chat model sử dụng một bảng duy nhất chat_messages để lưu nội dung và trạng thái hội thoại.
 * Mỗi hội thoại gắn với user_id; trạng thái mở/đóng lấy theo tin nhắn mới nhất.
 */
class Chat extends Model {
    private static bool $schemaEnsured = false;

    private function ensureSchema(): void {
        if (self::$schemaEnsured) return;
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS chat_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                user_email VARCHAR(150) NULL,
                is_admin TINYINT(1) DEFAULT 0,
                status ENUM('open','closed') DEFAULT 'open',
                content TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
        try { $this->db->exec("ALTER TABLE chat_messages ADD COLUMN user_email VARCHAR(150) NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE chat_messages ADD COLUMN status ENUM('open','closed') DEFAULT 'open'"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE chat_messages ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE users ADD COLUMN chat_last_admin_seen_id INT DEFAULT 0"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE users ADD COLUMN chat_last_user_seen_id INT DEFAULT 0"); } catch (\Throwable $e) {}
        self::$schemaEnsured = true;
    }

    private function userEmail(int $userId): ?string {
        $stmt = $this->db->prepare('SELECT email FROM users WHERE id=? LIMIT 1');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ? ($row['email'] ?? null) : null;
    }

    private function latestMessage(int $userId): ?array {
        $this->ensureSchema();
        $stmt = $this->db->prepare('SELECT * FROM chat_messages WHERE user_id=? ORDER BY id DESC LIMIT 1');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getOrCreateThread(int $userId, bool $forceNew = false): ?array {
        $this->ensureSchema();
        if ($userId <= 0) return null;
        $email = $this->userEmail($userId);
        if ($email === null) return null;
        if ($forceNew) {
            $this->db->prepare("UPDATE chat_messages SET status='closed' WHERE user_id=?")->execute([$userId]);
        }
        $latest = $this->latestMessage($userId);
        if (!$latest) {
            $stmt = $this->db->prepare('INSERT INTO chat_messages(user_id,user_email,is_admin,status,content) VALUES(?,?,0,"open","Khách bắt đầu cuộc trò chuyện")');
            $stmt->execute([$userId, $email]);
            $latest = $this->latestMessage($userId);
        }
        return $this->findThread($userId);
    }

    public function findThread(int $userId): ?array {
        $this->ensureSchema();
        $stmt = $this->db->prepare('
            SELECT u.id, u.email, u.chat_last_admin_seen_id, u.chat_last_user_seen_id,
                   cm.status, cm.created_at AS updated_at, cm.id AS last_message_id
            FROM users u
            LEFT JOIN chat_messages cm ON cm.user_id = u.id
            WHERE u.id = ?
            ORDER BY cm.id DESC
            LIMIT 1
        ');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        if (!$row) return null;
        if (empty($row['status'])) {
            $row['status'] = 'open';
            $row['updated_at'] = date('Y-m-d H:i:s');
            $row['last_message_id'] = 0;
        }
        return $row;
    }

    public function threads(): array {
        $this->ensureSchema();
        // Lấy tin nhắn mới nhất của mỗi user để hiển thị danh sách hội thoại
        $sql = "
            SELECT cm.user_id AS id, u.email, cm.status, cm.created_at AS updated_at, cm.id AS last_message_id
            FROM chat_messages cm
            JOIN (
                SELECT user_id, MAX(id) AS max_id FROM chat_messages GROUP BY user_id
            ) t ON t.max_id = cm.id
            JOIN users u ON u.id = cm.user_id
            ORDER BY cm.created_at DESC
        ";
        return $this->db->query($sql)->fetchAll();
    }

    public function messages(int $userId): array {
        $this->ensureSchema();
        $stmt = $this->db->prepare('SELECT cm.*, u.email FROM chat_messages cm JOIN users u ON cm.user_id=u.id WHERE cm.user_id=? ORDER BY cm.id ASC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function messagesSince(int $userId, int $lastId): array {
        $this->ensureSchema();
        $stmt = $this->db->prepare('SELECT cm.*, u.email FROM chat_messages cm JOIN users u ON cm.user_id=u.id WHERE cm.user_id=? AND cm.id > ? ORDER BY cm.id ASC');
        $stmt->execute([$userId, $lastId]);
        return $stmt->fetchAll();
    }

    public function addMessage(int $userId, int $senderUserId, bool $isAdmin, string $content, string $status = 'open'): void {
        $this->ensureSchema();
        if ($userId <= 0) return;
        $email = $this->userEmail($userId);
        $stmt = $this->db->prepare('INSERT INTO chat_messages(user_id,user_email,is_admin,status,content) VALUES(?,?,?,?,?)');
        $stmt->execute([$userId, $email, $isAdmin ? 1 : 0, $status, $content]);
    }

    public function updateStatus(int $userId, string $status): void {
        $this->ensureSchema();
        $status = $status === 'closed' ? 'closed' : 'open';
        $this->db->prepare("UPDATE chat_messages SET status=? WHERE user_id=?")->execute([$status, $userId]);
    }

    public function clearMessages(int $userId): void {
        $this->ensureSchema();
        $stmt = $this->db->prepare('DELETE FROM chat_messages WHERE user_id=?');
        $stmt->execute([$userId]);
    }

    public function markUserRead(int $userId): void {
        $this->ensureSchema();
        $latest = $this->latestMessage($userId);
        $lastId = $latest['id'] ?? 0;
        $stmt = $this->db->prepare('UPDATE users SET chat_last_user_seen_id=? WHERE id=?');
        $stmt->execute([$lastId, $userId]);
    }

    public function markAdminRead(int $userId): void {
        $this->ensureSchema();
        $latest = $this->latestMessage($userId);
        $lastId = $latest['id'] ?? 0;
        $stmt = $this->db->prepare('UPDATE users SET chat_last_admin_seen_id=? WHERE id=?');
        $stmt->execute([$lastId, $userId]);
    }

    public function hasUnreadForAdmin(): int {
        $this->ensureSchema();
        // Một hội thoại tính là unread nếu tin nhắn mới nhất từ khách và admin chưa xem
        $sql = "
            SELECT COUNT(*) FROM (
                SELECT cm.user_id, cm.id, cm.is_admin, u.chat_last_admin_seen_id
                FROM chat_messages cm
                JOIN (
                    SELECT user_id, MAX(id) AS max_id FROM chat_messages GROUP BY user_id
                ) t ON t.max_id = cm.id
                JOIN users u ON u.id = cm.user_id
            ) x
            WHERE x.is_admin = 0 AND x.id > COALESCE(x.chat_last_admin_seen_id, 0)
        ";
        return (int)$this->db->query($sql)->fetchColumn();
    }

    public function hasUnreadForUser(int $userId): bool {
        $this->ensureSchema();
        $stmt = $this->db->prepare("
            SELECT cm.id, cm.is_admin, u.chat_last_user_seen_id
            FROM chat_messages cm
            JOIN users u ON u.id = cm.user_id
            WHERE cm.user_id=?
            ORDER BY cm.id DESC
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        if (!$row) return false;
        return ($row['is_admin'] == 1) && ((int)$row['id'] > (int)($row['chat_last_user_seen_id'] ?? 0));
    }
}
