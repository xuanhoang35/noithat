<?php
require_once __DIR__ . '/../core/Model.php';

class Service extends Model {
    public function __construct() {
        parent::__construct();
        $this->ensureSchema();
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
        $this->seedDefault();
    }

    public function all(): array {
        return $this->db->query('SELECT * FROM services ORDER BY id DESC')->fetchAll();
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
        $stmt = $this->db->prepare('UPDATE services SET name=?, description=?, sla=?, price=? WHERE id=?');
        $stmt->execute([$name,$description,$sla,$price,$id]);
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare('DELETE FROM services WHERE id=?');
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
        $check = $this->db->prepare("SELECT COUNT(*) FROM services WHERE name=?");
        $insert = $this->db->prepare('INSERT INTO services(name, description, sla, price) VALUES(?,?,?,?)');
        foreach ($seed as $s) {
            $check->execute([$s[0]]);
            if ((int)$check->fetchColumn() === 0) {
                $insert->execute($s);
            }
        }
    }
}
