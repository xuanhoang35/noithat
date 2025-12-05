<?php
require_once __DIR__ . '/../core/Model.php';
class User extends Model {
    private static bool $checkedPlain = false;
    private static bool $hasPlain = false;
    private function reseedAutoIncrement(): void {
        try {
            $next = (int)$this->db->query('SELECT COALESCE(MAX(id), 0) + 1 FROM users')->fetchColumn();
            $next = max(1, $next);
            $this->db->exec('ALTER TABLE users AUTO_INCREMENT = ' . $next);
        } catch (\Throwable $e) {
            // ignore
        }
    }

    private function checkPlainColumn(): void {
        if (self::$checkedPlain) return;
        try {
            $stmt = $this->db->query("SHOW COLUMNS FROM users LIKE 'password_plain'");
            self::$hasPlain = (bool)$stmt->fetch();
        } catch (\Throwable $e) {
            self::$hasPlain = false;
        }
        self::$checkedPlain = true;
    }
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
        try {
            $this->db->exec("ALTER TABLE users ADD COLUMN is_online TINYINT(1) DEFAULT 0");
        } catch (\Throwable $e) {
            // đã có
        }
        try {
            $this->db->exec("ALTER TABLE users ADD COLUMN password_plain VARCHAR(255) NULL");
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

    public function updateAdmin(int $id, array $data): void {
        $this->ensureSchema();
        $fields = ['name','email','phone','address','role','is_active','password','password_plain'];
        $set = [];
        $params = [];
        foreach ($fields as $f) {
            if (array_key_exists($f, $data)) {
                $set[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        if (empty($set)) return;
        $params[] = $id;
        $sql = 'UPDATE users SET ' . implode(',', $set) . ' WHERE id = ? AND deleted_at IS NULL';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }
    public function create(string $name,string $email,string $phone,string $password): void {
        $this->ensureSchema();
        $this->checkPlainColumn();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if (self::$hasPlain) {
            try {
                $stmt=$this->db->prepare('INSERT INTO users(name,email,phone,password,password_plain,role,is_active) VALUES(?,?,?,?,?,?,1)');
                $stmt->execute([$name,$email,$phone,$hash,$password,'user']);
                return;
            } catch (\Throwable $e) {
                // fallback nếu thiếu cột
                self::$hasPlain = false;
            }
        }
        $stmt=$this->db->prepare('INSERT INTO users(name,email,phone,password,role,is_active) VALUES(?,?,?,?,?,1)');
        $stmt->execute([$name,$email,$phone,$hash,'user']);
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
        $stmt = $this->db->prepare("DELETE FROM users WHERE id=? AND role!='admin'");
        $stmt->execute([$id]);
        $this->reseedAutoIncrement();
    }

    public function setOnline(int $id, bool $online): void {
        $this->ensureSchema();
        $stmt = $this->db->prepare('UPDATE users SET is_online=? WHERE id=?');
        $stmt->execute([$online ? 1 : 0, $id]);
    }
}
