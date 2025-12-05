<?php
require_once __DIR__ . '/../core/Model.php';
class Complaint extends Model {
    private function ensureSchema(): void {
        try {
            $this->db->exec("ALTER TABLE complaints ADD COLUMN response TEXT NULL");
        } catch (\Throwable $e) {
            // cột đã tồn tại -> bỏ qua
        }
        try {
            $this->db->exec("ALTER TABLE complaints ADD COLUMN replies_json LONGTEXT NULL");
        } catch (\Throwable $e) {
            // đã có hoặc không cần
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
        $this->ensureSchema();
        if ($userId <= 0) {
            $stmtFind = $this->db->prepare('SELECT user_id FROM complaints WHERE id=? LIMIT 1');
            $stmtFind->execute([$complaintId]);
            $fallback = $stmtFind->fetchColumn();
            $userId = $fallback ? (int)$fallback : $userId;
        }
        $stmt = $this->db->prepare('SELECT replies_json FROM complaints WHERE id=? LIMIT 1');
        $stmt->execute([$complaintId]);
        $json = $stmt->fetchColumn();
        $replies = [];
        if ($json) {
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                $replies = $decoded;
            }
        }
        $replies[] = [
            'user_id' => $userId,
            'is_admin' => $isAdmin ? 1 : 0,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $save = $this->db->prepare('UPDATE complaints SET replies_json=? WHERE id=?');
        $save->execute([json_encode($replies, JSON_UNESCAPED_UNICODE), $complaintId]);
    }

    public function replies(int $complaintId): array {
        $this->ensureSchema();
        $stmt = $this->db->prepare('SELECT replies_json FROM complaints WHERE id=? LIMIT 1');
        $stmt->execute([$complaintId]);
        $json = $stmt->fetchColumn();
        if (!$json) return [];
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) return [];
        return $decoded;
    }
}
