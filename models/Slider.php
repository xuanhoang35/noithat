<?php
require_once __DIR__ . '/../core/Model.php';

class Slider extends Model {
    private static bool $schemaEnsured = false;

    private function ensureSchema(): void {
        if (self::$schemaEnsured) return;
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS slider_images (
                id INT AUTO_INCREMENT PRIMARY KEY,
                image VARCHAR(255) NOT NULL,
                is_active TINYINT(1) DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        self::$schemaEnsured = true;
    }

    public function all(bool $onlyActive = true): array {
        $this->ensureSchema();
        $sql = 'SELECT * FROM slider_images';
        if ($onlyActive) {
            $sql .= ' WHERE is_active = 1';
        }
        $sql .= ' ORDER BY created_at DESC, id DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function create(string $path): int {
        $this->ensureSchema();
        $stmt = $this->db->prepare('INSERT INTO slider_images(image,is_active) VALUES(?,1)');
        $stmt->execute([$path]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id): void {
        $this->ensureSchema();
        $stmt = $this->db->prepare('SELECT image FROM slider_images WHERE id=? LIMIT 1');
        $stmt->execute([$id]);
        $img = $stmt->fetch();
        $this->db->prepare('DELETE FROM slider_images WHERE id=?')->execute([$id]);
        if ($img && !empty($img['image'])) {
            $this->deleteLocalFile($img['image']);
        }
    }

    private function deleteLocalFile(string $path): void {
        $path = trim($path);
        if ($path === '' || preg_match('#^(?:https?:)?//#', $path)) {
            return;
        }
        $full = realpath(__DIR__ . '/../' . ltrim($path, '/'));
        if ($full && file_exists($full) && is_file($full)) {
            @unlink($full);
        }
    }
}
