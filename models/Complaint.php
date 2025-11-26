<?php
require_once __DIR__ . '/../core/Model.php';
class Complaint extends Model {
    private function ensureSchema(): void {
        try {
            $this->db->exec("ALTER TABLE complaints ADD COLUMN response TEXT NULL");
        } catch (\Throwable $e) {
            // cột đã tồn tại -> bỏ qua
        }
    }
    private function ensureReplySchema(): void {
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS complaint_replies (
                id INT AUTO_INCREMENT PRIMARY KEY,
                complaint_id INT NOT NULL,
                user_id INT NOT NULL,
                is_admin TINYINT(1) DEFAULT 0,
                content TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (complaint_id) REFERENCES complaints(id),
                FOREIGN KEY (user_id) REFERENCES users(id)
            )");
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function create(int $userId, int $orderId, string $title, string $content): void {
        $this->ensureSchema();
        $stmt = $this->db->prepare('INSERT INTO complaints(order_id,user_id,title,content,status,created_at) VALUES(?,?,?,?,?,NOW())');
        $stmt->execute([$orderId,$userId,$title,$content,'new']);
    }
    public function all(): array {
        $this->ensureSchema();
        return $this->db->query('SELECT c.*, u.email, o.code FROM complaints c JOIN users u ON c.user_id=u.id JOIN orders o ON c.order_id=o.id ORDER BY c.created_at DESC')->fetchAll();
    }
    public function updateStatus(int $id, string $status, ?string $response = null): void {
        $this->ensureSchema();
        $stmt = $this->db->prepare("UPDATE complaints SET status=?, response=?, resolved_at = CASE WHEN ?='resolved' THEN NOW() ELSE resolved_at END WHERE id=?");
        $stmt->execute([$status, $response, $status, $id]);
    }
    public function byUser(int $userId): array {
        $this->ensureSchema();
        $stmt = $this->db->prepare('SELECT c.*, o.code FROM complaints c JOIN orders o ON c.order_id=o.id WHERE c.user_id=? ORDER BY c.created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function addReply(int $complaintId, int $userId, bool $isAdmin, string $content): void {
        $this->ensureReplySchema();
        if ($userId <= 0) {
            $stmtFind = $this->db->prepare('SELECT user_id FROM complaints WHERE id=? LIMIT 1');
            $stmtFind->execute([$complaintId]);
            $fallback = $stmtFind->fetchColumn();
            $userId = $fallback ? (int)$fallback : $userId;
        }
        $stmt = $this->db->prepare('INSERT INTO complaint_replies(complaint_id,user_id,is_admin,content) VALUES(?,?,?,?)');
        $stmt->execute([$complaintId, $userId, $isAdmin ? 1 : 0, $content]);
    }

    public function replies(int $complaintId): array {
        $this->ensureReplySchema();
        $stmt = $this->db->prepare('SELECT * FROM complaint_replies WHERE complaint_id=? ORDER BY created_at ASC');
        $stmt->execute([$complaintId]);
        return $stmt->fetchAll();
    }
}
