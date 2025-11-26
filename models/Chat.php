<?php
require_once __DIR__ . '/../core/Model.php';
class Chat extends Model {
    private function ensureSchema(): void {
        try {
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS chat_threads (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    status ENUM('open','closed') DEFAULT 'open',
                    user_unread TINYINT(1) DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id)
                );
            ");
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS chat_messages (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    thread_id INT NOT NULL,
                    user_id INT NOT NULL,
                    is_admin TINYINT(1) DEFAULT 0,
                    content TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (thread_id) REFERENCES chat_threads(id),
                    FOREIGN KEY (user_id) REFERENCES users(id)
                );
            ");
            // đảm bảo cột user_unread tồn tại khi nâng cấp
            $this->db->exec("ALTER TABLE chat_threads ADD COLUMN user_unread TINYINT(1) DEFAULT 0");
        } catch (\Throwable $e) { /* ignore */ }
    }

    public function getOrCreateThread(int $userId, bool $forceNew = false): ?array {
        $this->ensureSchema();
        if ($userId <= 0) return null;
        if ($forceNew) {
            $this->db->prepare("UPDATE chat_threads SET status='closed', user_unread=0, updated_at=NOW() WHERE user_id=? AND status!='closed'")->execute([$userId]);
        }
        $stmt = $this->db->prepare('SELECT * FROM chat_threads WHERE user_id=? ORDER BY id DESC LIMIT 1');
        $stmt->execute([$userId]);
        $thread = $stmt->fetch();
        if ($thread && !$forceNew && $thread['status'] !== 'closed') {
            return $thread;
        }
        $this->db->prepare('INSERT INTO chat_threads(user_id,status) VALUES(?, "open")')->execute([$userId]);
        $id = (int)$this->db->lastInsertId();
        return $this->findThread($id);
    }

    public function findThread(int $id): ?array {
        $this->ensureSchema();
        $stmt = $this->db->prepare('SELECT ct.*, u.email FROM chat_threads ct JOIN users u ON ct.user_id=u.id WHERE ct.id=?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function threads(): array {
        $this->ensureSchema();
        return $this->db->query('SELECT ct.*, u.email FROM chat_threads ct JOIN users u ON ct.user_id=u.id ORDER BY ct.updated_at DESC')->fetchAll();
    }

    public function messages(int $threadId): array {
        $this->ensureSchema();
        $stmt = $this->db->prepare('SELECT cm.*, u.email FROM chat_messages cm JOIN users u ON cm.user_id=u.id WHERE thread_id=? ORDER BY cm.created_at ASC');
        $stmt->execute([$threadId]);
        return $stmt->fetchAll();
    }

    public function messagesSince(int $threadId, int $lastId): array {
        $this->ensureSchema();
        $stmt = $this->db->prepare('SELECT cm.*, u.email FROM chat_messages cm JOIN users u ON cm.user_id=u.id WHERE thread_id=? AND cm.id > ? ORDER BY cm.id ASC');
        $stmt->execute([$threadId, $lastId]);
        return $stmt->fetchAll();
    }

    public function addMessage(int $threadId, int $userId, bool $isAdmin, string $content): void {
        $this->ensureSchema();
        // fallback user id if missing (avoid FK error)
        if ($userId <= 0) {
            $stmt = $this->db->prepare('SELECT user_id FROM chat_threads WHERE id=?');
            $stmt->execute([$threadId]);
            $row = $stmt->fetch();
            $userId = $row ? (int)$row['user_id'] : 0;
        }
        if ($userId <= 0) return;
        // ensure status open + set cờ đọc
        $unread = $isAdmin ? 1 : 0;
        $this->db->prepare("UPDATE chat_threads SET updated_at=NOW(), status='open', user_unread=? WHERE id=?")->execute([$unread,$threadId]);
        $stmt = $this->db->prepare('INSERT INTO chat_messages(thread_id,user_id,is_admin,content) VALUES(?,?,?,?)');
        $stmt->execute([$threadId,$userId,$isAdmin ? 1 : 0,$content]);
    }

    public function updateStatus(int $threadId, string $status): void {
        $this->ensureSchema();
        $stmt = $this->db->prepare('UPDATE chat_threads SET status=?, updated_at=NOW() WHERE id=?');
        $stmt->execute([$status,$threadId]);
    }

    public function clearMessages(int $threadId): void {
        $this->ensureSchema();
        $stmt = $this->db->prepare('DELETE FROM chat_messages WHERE thread_id=?');
        $stmt->execute([$threadId]);
    }

    public function markUserRead(int $threadId): void {
        $this->ensureSchema();
        $stmt = $this->db->prepare('UPDATE chat_threads SET user_unread=0 WHERE id=?');
        $stmt->execute([$threadId]);
    }

    public function hasUnreadForUser(int $userId): bool {
        $this->ensureSchema();
        $stmt = $this->db->prepare('SELECT user_unread FROM chat_threads WHERE user_id=? ORDER BY id DESC LIMIT 1');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ? (bool)$row['user_unread'] : false;
    }
}
