<?php
require_once __DIR__ . '/../core/Model.php';
class Category extends Model {
    public function all(): array { return $this->db->query('SELECT * FROM categories ORDER BY id DESC')->fetchAll(); }
    public function create(string $name,string $slug): void { $stmt=$this->db->prepare('INSERT INTO categories(name,slug) VALUES(?,?)'); $stmt->execute([$name,$slug]); }
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
