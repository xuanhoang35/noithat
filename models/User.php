<?php
require_once __DIR__ . '/../core/Model.php';
class User extends Model {
    private static bool $checkedPlain = false;
    private static bool $hasPlain = false;
    public function __construct() {
        parent::__construct();
        $this->ensureSchema();
    }

    private function ensureResetSchema(): void {
        try { $this->db->exec("ALTER TABLE users ADD COLUMN reset_token VARCHAR(100) NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE users ADD COLUMN reset_status ENUM('pending','completed','delivered') NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE users ADD COLUMN reset_password_plain VARCHAR(255) NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE users ADD COLUMN reset_requested_at DATETIME NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE users ADD COLUMN reset_completed_at DATETIME NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE users ADD COLUMN reset_delivered_at DATETIME NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE users ADD COLUMN reset_email VARCHAR(150) NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE users ADD COLUMN reset_phone VARCHAR(50) NULL"); } catch (\Throwable $e) {}
    }
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
        $this->ensureResetSchema();
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
    public function findByPhone(string $phone): ?array {
        $this->ensureSchema();
        $stmt=$this->db->prepare('SELECT * FROM users WHERE phone = ? AND deleted_at IS NULL LIMIT 1');
        $stmt->execute([$phone]);
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

    public function clearResetFlag(int $id): void {
        $this->ensureSchema();
        $stmt = $this->db->prepare('UPDATE users SET reset_token=NULL, reset_status=NULL, reset_password_plain=NULL, reset_requested_at=NULL, reset_completed_at=NULL, reset_delivered_at=NULL, reset_email=NULL, reset_phone=NULL WHERE id=?');
        $stmt->execute([$id]);
    }

    public function clearResetMetaKeepPassword(int $id): void {
        $this->ensureSchema();
        $stmt = $this->db->prepare('UPDATE users SET reset_token=NULL, reset_status=NULL, reset_requested_at=NULL, reset_completed_at=NULL, reset_delivered_at=NULL, reset_email=NULL, reset_phone=NULL WHERE id=?');
        $stmt->execute([$id]);
    }

    private function generateToken(): string {
        return (string)(time() . rand(1000, 9999));
    }

    public function requestPasswordReset(int $userId, string $email, string $phone): string {
        $this->ensureSchema();
        $token = $this->generateToken();
        $stmt = $this->db->prepare('UPDATE users SET reset_token=?, reset_status="pending", reset_password_plain=NULL, reset_requested_at=NOW(), reset_completed_at=NULL, reset_delivered_at=NULL, reset_email=?, reset_phone=? WHERE id=?');
        $stmt->execute([$token, $email, $phone, $userId]);
        return $token;
    }

    public function passwordResets(): array {
        $this->ensureSchema();
        $stmt = $this->db->query('SELECT id AS user_id, name, email, reset_token AS id, reset_status AS status, reset_password_plain AS new_password_plain, reset_requested_at AS created_at, reset_completed_at AS completed_at, reset_delivered_at AS delivered_at, reset_email, reset_phone FROM users WHERE reset_token IS NOT NULL ORDER BY reset_requested_at DESC');
        return $stmt->fetchAll();
    }

    public function findPasswordReset(string $token): ?array {
        $this->ensureSchema();
        $stmt = $this->db->prepare('SELECT id AS user_id, reset_token AS id, reset_status AS status, reset_password_plain AS new_password_plain, reset_requested_at AS created_at, reset_completed_at AS completed_at, reset_delivered_at AS delivered_at, reset_email AS email, reset_phone AS phone FROM users WHERE reset_token=? LIMIT 1');
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function completePasswordReset(string $token, string $newPassword): void {
        $reset = $this->findPasswordReset($token);
        if (!$reset || $reset['status'] === 'delivered') {
            return;
        }
        $this->db->beginTransaction();
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateUser = $this->db->prepare('UPDATE users SET password=?, password_plain=?, reset_status="completed", reset_password_plain=?, reset_completed_at=NOW() WHERE reset_token=?');
        $updateUser->execute([$hash, $newPassword, $newPassword, $token]);
        $this->db->commit();
    }

    public function markResetDelivered(string $token): void {
        $this->ensureSchema();
        $stmt = $this->db->prepare('UPDATE users SET reset_status="delivered", reset_delivered_at=COALESCE(reset_delivered_at,NOW()) WHERE reset_token=?');
        $stmt->execute([$token]);
    }

    public function resendPasswordReset(string $token): ?string {
        $reset = $this->findPasswordReset($token);
        if (!$reset) {
            return null;
        }
        return $this->requestPasswordReset((int)$reset['user_id'], $reset['email'], $reset['phone']);
    }

    public function rejectPasswordReset(string $token): void {
        $this->ensureSchema();
        $stmt = $this->db->prepare('UPDATE users SET reset_status="delivered", reset_password_plain="__REJECTED__", reset_completed_at=NOW(), reset_delivered_at=NOW() WHERE reset_token=?');
        $stmt->execute([$token]);
    }
}
