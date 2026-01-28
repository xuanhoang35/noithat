<?php
require_once __DIR__ . '/../core/Model.php';
class Category extends Model {
    public function existsByNameOrSlug(string $name, string $slug, ?int $excludeId = null): bool {
        $sql = 'SELECT 1 FROM categories WHERE (LOWER(name) = LOWER(?) OR slug = ?)';
        $params = [$name, $slug];
        if ($excludeId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $excludeId;
        }
        $sql .= ' LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetchColumn();
    }
    public function all(string $keyword = ''): array {
        $sql = 'SELECT * FROM categories';
        $params = [];
        $kw = trim($keyword);
        if ($kw !== '') {
            $sql .= ' WHERE name LIKE ?';
            $params[] = '%' . $kw . '%';
        }
        $sql .= ' ORDER BY id DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public function create(string $name,string $slug): void {
        $stmt = $this->db->prepare('INSERT INTO categories(name,slug) VALUES(?,?)');
        $stmt->execute([$name,$slug]);
    }
    public function find(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id=?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    public function update(int $id, string $name, string $slug): void {
        $stmt = $this->db->prepare('UPDATE categories SET name=?, slug=? WHERE id=?');
        $stmt->execute([$name,$slug,$id]);
    }
    public function delete(int $id): void {
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id=?');
        $stmt->execute([$id]);
    }
}
