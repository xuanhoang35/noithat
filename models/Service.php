<?php
require_once __DIR__ . '/../core/Model.php';

class Service extends Model {
    public function __construct() {
        parent::__construct();
        $this->ensureSchema();
    }
    public function existsByName(string $name, ?int $excludeId = null): bool {
        $sql = 'SELECT 1 FROM services WHERE is_booking = 0 AND LOWER(name) = LOWER(?)';
        $params = [$name];
        if ($excludeId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $excludeId;
        }
        $sql .= ' LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetchColumn();
    }

    private function ensureSchema(): void {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS services (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                description TEXT,
                sla VARCHAR(100),
                price DECIMAL(12,2) DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
        try { $this->db->exec("ALTER TABLE services ADD COLUMN is_booking TINYINT(1) DEFAULT 0"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE services ADD COLUMN parent_service_id INT NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE services ADD COLUMN customer_name VARCHAR(150) NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE services ADD COLUMN customer_phone VARCHAR(30) NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE services ADD COLUMN customer_email VARCHAR(150) NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE services ADD COLUMN customer_address VARCHAR(255) NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE services ADD COLUMN schedule_at DATETIME NULL"); } catch (\Throwable $e) {}
        try { $this->db->exec("ALTER TABLE services ADD COLUMN note TEXT NULL"); } catch (\Throwable $e) {}
        $this->seedDefault();
    }

    public function createBooking(int $serviceId, string $name, string $phone, string $email, string $address, string $scheduleAt, string $note): void {
        $base = $this->find($serviceId);
        $serviceName = $base ? $base['name'] : 'Dịch vụ';
        $price = $base ? (float)$base['price'] : 0;
        $stmt = $this->db->prepare('
            INSERT INTO services(parent_service_id, name, description, sla, price, is_booking, customer_name, customer_phone, customer_email, customer_address, schedule_at, note, created_at)
            VALUES(?,?,?,?,?,1,?,?,?,?,?,?,NOW())
        ');
        $stmt->execute([$serviceId, $serviceName, $base['description'] ?? '', $base['sla'] ?? '', $price, $name, $phone, $email, $address, $scheduleAt, $note]);
    }

    public function bookings(string $keyword = '', ?int $serviceId = null): array {
        $sql = '
            SELECT b.*, s.name as service_name
            FROM services b
            LEFT JOIN services s ON b.parent_service_id = s.id
            WHERE b.is_booking = 1
        ';
        $params = [];
        $keyword = trim($keyword);
        if ($keyword !== '') {
            $sql .= ' AND (b.customer_name LIKE ? OR b.customer_phone LIKE ? OR b.customer_email LIKE ?)';
            $kw = '%' . $keyword . '%';
            $params[] = $kw;
            $params[] = $kw;
            $params[] = $kw;
        }
        if ($serviceId) {
            $sql .= ' AND b.parent_service_id = ?';
            $params[] = $serviceId;
        }
        $sql .= ' ORDER BY b.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function all(bool $onlyBase = true, string $keyword = ''): array {
        $sql = 'SELECT * FROM services';
        $where = [];
        $params = [];
        if ($onlyBase) {
            $where[] = 'is_booking = 0';
        }
        $kw = trim($keyword);
        if ($kw !== '') {
            $where[] = '(name LIKE ? OR description LIKE ?)';
            $params[] = '%' . $kw . '%';
            $params[] = '%' . $kw . '%';
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY id DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(string $name, string $description, string $sla, float $price): void {
        $stmt = $this->db->prepare('INSERT INTO services(name, description, sla, price) VALUES(?,?,?,?)');
        $stmt->execute([$name, $description, $sla, $price]);
    }

    public function find(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM services WHERE id=?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function update(int $id, string $name, string $description, string $sla, float $price): void {
        $stmt = $this->db->prepare('UPDATE services SET name=?, description=?, sla=?, price=? WHERE id=? AND is_booking = 0');
        $stmt->execute([$name,$description,$sla,$price,$id]);
    }

    public function delete(int $id): void {
        $this->db->prepare('DELETE FROM services WHERE parent_service_id=?')->execute([$id]);
        $stmt = $this->db->prepare('DELETE FROM services WHERE id=? AND is_booking = 0');
        $stmt->execute([$id]);
    }

    private function seedDefault(): void {
        $seed = [
            ['Lắp đặt & cấu hình', 'Đội ngũ kỹ thuật lắp đặt nội thất, thiết bị gia dụng tận nơi, kiểm tra vận hành. Lên lịch theo giờ bạn muốn; dụng cụ chuyên nghiệp, gọn gàng; bàn giao kèm hướng dẫn sử dụng.', 'Trong 24h', 0],
            ['Bảo trì & sửa chữa', 'Kiểm tra định kỳ, thay thế linh kiện, sửa chữa nhanh khi hỏng hóc. Đặt lịch online có kỹ thuật đến tận nơi; linh kiện chính hãng, báo giá minh bạch; bảo hành sau sửa chữa.', 'Đặt lịch trước', 0],
            ['Vận chuyển & hỗ trợ', 'Giao hàng nhanh, hỗ trợ nâng - lắp - thu gom bao bì, dọn dẹp sạch sẽ. Miễn phí trong bán kính quy định; có bảo hiểm vận chuyển; tư vấn miễn phí qua hotline/chat.', 'Trong ngày', 0],
            ['Tư vấn thiết kế không gian', 'Đề xuất bố trí nội thất, phối màu, chọn thiết bị phù hợp ngân sách. Gặp online/đến tận nơi, gửi bản vẽ đề xuất nhanh.', 'Theo lịch hẹn', 0],
            ['Vệ sinh & bảo dưỡng định kỳ', 'Lau chùi, bảo dưỡng thiết bị gia dụng, nội thất; kiểm tra hao mòn để phòng hỏng hóc.', 'Đặt lịch trước', 0],
            ['Kiểm tra an toàn điện/nước', 'Rà soát hệ thống điện, nước cho thiết bị gia dụng; tư vấn nâng cấp và thay thế khi cần.', 'Trong 48h', 0],
        ];
        $check = $this->db->prepare("SELECT COUNT(*) FROM services WHERE name=? AND is_booking = 0");
        $insert = $this->db->prepare('INSERT INTO services(name, description, sla, price) VALUES(?,?,?,?)');
        foreach ($seed as $s) {
            $check->execute([$s[0]]);
            if ((int)$check->fetchColumn() === 0) {
                $insert->execute($s);
            }
        }
    }
}
