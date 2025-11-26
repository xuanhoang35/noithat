<?php
require_once __DIR__ . '/../core/Model.php';

class Voucher extends Model {
    public function __construct() {
        parent::__construct();
        $this->ensureSchema();
    }

    private function ensureSchema(): void {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS vouchers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(50) UNIQUE NOT NULL,
                discount_percent INT NOT NULL,
                category_id INT NULL,
                description TEXT,
                usage_limit INT DEFAULT 1,
                used_count INT DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
            )
        ");
        try { $this->db->exec("ALTER TABLE vouchers ADD COLUMN usage_limit INT DEFAULT 1"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE vouchers ADD COLUMN used_count INT DEFAULT 0"); } catch (\Throwable $e) {}
    }

    public function all(): array {
        return $this->db->query('SELECT v.*, c.name AS category_name FROM vouchers v LEFT JOIN categories c ON v.category_id = c.id ORDER BY v.created_at DESC')->fetchAll();
    }

    public function create(array $data): void {
        $stmt = $this->db->prepare('INSERT INTO vouchers(code, discount_percent, category_id, description, usage_limit, used_count) VALUES(?,?,?,?,?,0)');
        $stmt->execute([$data['code'], $data['discount_percent'], $data['category_id'] ?: null, $data['description'], $data['usage_limit'] ?? 1]);
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare('UPDATE vouchers SET code=?, discount_percent=?, category_id=?, description=?, usage_limit=? WHERE id=?');
        $stmt->execute([$data['code'], $data['discount_percent'], $data['category_id'] ?: null, $data['description'], $data['usage_limit'] ?? 1, $id]);
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare('DELETE FROM vouchers WHERE id=?');
        $stmt->execute([$id]);
    }

    public function findByCode(string $code): ?array {
        $stmt = $this->db->prepare('SELECT v.*, c.name AS category_name FROM vouchers v LEFT JOIN categories c ON v.category_id=c.id WHERE v.code=? LIMIT 1');
        $stmt->execute([$code]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function incrementUsage(int $id): void {
        $stmt = $this->db->prepare('UPDATE vouchers SET used_count = used_count + 1 WHERE id=?');
        $stmt->execute([$id]);
    }
}
