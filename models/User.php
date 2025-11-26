<?php
require_once __DIR__ . '/../core/Model.php';
class User extends Model {
    private function ensureSchema(): void {
        try {
            $this->db->exec("ALTER TABLE users ADD COLUMN address VARCHAR(255) NULL");
        } catch (\Throwable $e) {
            // đã có
        }
        try {
            $this->db->exec("ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL");
        } catch (\Throwable $e) {
            // đã có
        }
        try {
            $this->db->exec("ALTER TABLE users ADD COLUMN deleted_at DATETIME NULL");
        } catch (\Throwable $e) {
            // đã có
        }
    }
    public function findByEmail(string $email): ?array {
        $this->ensureSchema();
        $stmt=$this->db->prepare('SELECT * FROM users WHERE email = ? AND deleted_at IS NULL LIMIT 1');
        $stmt->execute([$email]);
        $row=$stmt->fetch();
        return $row ?: null;
    }
    public function create(string $name,string $email,string $phone,string $password): void {
        $this->ensureSchema();
        $stmt=$this->db->prepare('INSERT INTO users(name,email,phone,password,role,is_active) VALUES(?,?,?,?,?,1)');
        $stmt->execute([$name,$email,$phone,password_hash($password,PASSWORD_DEFAULT),'user']);
    }
    public function all(string $keyword = ''): array {
        $this->ensureSchema();
        $sql = 'SELECT * FROM users WHERE deleted_at IS NULL';
        $params = [];
        $keyword = trim($keyword);
        if ($keyword !== '') {
            $sql .= ' AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)';
            $kw = '%' . $keyword . '%';
            $params = [$kw, $kw, $kw];
        }
        $sql .= ' ORDER BY id DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public function toggleActive(int $id): void { $this->db->prepare('UPDATE users SET is_active = 1 - is_active WHERE id=?')->execute([$id]); }
    public function findById(int $id): ?array {
        $this->ensureSchema();
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id=? AND deleted_at IS NULL');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    public function updateProfile(int $id, string $name, string $phone, string $address, ?string $avatarPath = null): void {
        $this->ensureSchema();
        if ($avatarPath !== null) {
            $stmt = $this->db->prepare('UPDATE users SET name=?, phone=?, address=?, avatar=? WHERE id=?');
            $stmt->execute([$name,$phone,$address,$avatarPath,$id]);
        } else {
            $stmt = $this->db->prepare('UPDATE users SET name=?, phone=?, address=? WHERE id=?');
            $stmt->execute([$name,$phone,$address,$id]);
        }
    }

    public function delete(int $id): void {
        $this->ensureSchema();
        // Không cho phép xóa admin
        $stmt = $this->db->prepare("UPDATE users SET deleted_at=NOW(), is_active=0 WHERE id=? AND role!='admin'");
        $stmt->execute([$id]);
    }
}
