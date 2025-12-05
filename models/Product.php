<?php
require_once __DIR__ . '/../core/Model.php';
class Product extends Model {
    private function reseedAutoIncrement(): void {
        try {
            $next = (int)$this->db->query('SELECT COALESCE(MAX(id), 0) + 1 FROM products')->fetchColumn();
            $next = max(1, $next);
            $this->db->exec('ALTER TABLE products AUTO_INCREMENT = ' . $next);
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function all($categoryId=null, string $keyword = '', string $priceSort = '', string $priceRange = '', ?int $priceMin = null, ?int $priceMax = null): array {
        $sql = 'SELECT * FROM products';
        $conditions = [];
        $params = [];
        if ($categoryId) {
            $conditions[] = 'category_id = ?';
            $params[] = $categoryId;
        }
        $keyword = trim($keyword);
        if ($keyword !== '') {
            $conditions[] = '(name LIKE ? OR description LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        switch ($priceRange) {
            case 'under-1':
                $conditions[] = 'price < ?';
                $params[] = 1000000;
                break;
            case '1-2':
                $conditions[] = 'price BETWEEN ? AND ?';
                $params[] = 1000000;
                $params[] = 2000000;
                break;
            case '2-4':
                $conditions[] = 'price BETWEEN ? AND ?';
                $params[] = 2000000;
                $params[] = 4000000;
                break;
            case '4-6':
                $conditions[] = 'price BETWEEN ? AND ?';
                $params[] = 4000000;
                $params[] = 6000000;
                break;
            case 'custom':
                if ($priceMin !== null) {
                    $conditions[] = 'price >= ?';
                    $params[] = $priceMin;
                }
                if ($priceMax !== null) {
                    $conditions[] = 'price <= ?';
                    $params[] = $priceMax;
                }
                break;
        }
        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        // Luôn ưu tiên còn hàng trước, sau đó mới áp dụng sắp xếp theo giá hoặc mặc định
        if ($priceSort === 'asc') {
            $sql .= ' ORDER BY (stock > 0) DESC, price ASC';
        } elseif ($priceSort === 'desc') {
            $sql .= ' ORDER BY (stock > 0) DESC, price DESC';
        } else {
            $sql .= ' ORDER BY (stock > 0) DESC, id DESC';
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public function find(int $id): ?array {
        $stmt=$this->db->prepare('SELECT * FROM products WHERE id=?');
        $stmt->execute([$id]);
        $row=$stmt->fetch();
        return $row?:null;
    }
    public function featured(): array { return $this->db->query('SELECT * FROM products ORDER BY created_at DESC LIMIT 4')->fetchAll(); }
    public function create(array $data): void {
        $stmt=$this->db->prepare('INSERT INTO products(category_id,name,slug,description,price,stock,image) VALUES(?,?,?,?,?,?,?)');
        $stmt->execute([$data['category_id'],$data['name'],$data['slug'],$data['description'],$data['price'],$data['stock'],$data['image']]);
    }
    public function update(int $id, array $data): void {
        $stmt=$this->db->prepare('UPDATE products SET category_id=?, name=?, slug=?, description=?, price=?, stock=?, image=?, updated_at=NOW() WHERE id=?');
        $stmt->execute([$data['category_id'],$data['name'],$data['slug'],$data['description'],$data['price'],$data['stock'],$data['image'],$id]);
    }
    public function delete(int $id): void {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id=?');
        $stmt->execute([$id]);
        $this->reseedAutoIncrement();
    }

    public function quickSearch(string $keyword, ?int $categoryId = null, int $limit = 8): array {
        $keyword = trim($keyword);
        if ($keyword === '') return [];
        $limit = max(1, min(12, $limit));
        $sql = 'SELECT id, name, price, image FROM products WHERE name LIKE ?';
        $params = [$keyword . '%'];
        if ($categoryId) {
            $sql .= ' AND category_id = ?';
            $params[] = $categoryId;
        }
        $sql .= ' ORDER BY created_at DESC LIMIT ' . $limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
