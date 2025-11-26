<?php
require_once __DIR__ . '/../core/Model.php';

class ServiceBooking extends Model {
    public function __construct() {
        parent::__construct();
        $this->ensureSchema();
    }

    private function ensureSchema(): void {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS service_bookings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                service_id INT NOT NULL,
                name VARCHAR(150) NOT NULL,
                phone VARCHAR(30) NOT NULL,
                email VARCHAR(150),
                address VARCHAR(255) NOT NULL,
                schedule_at DATETIME NOT NULL,
                note TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (service_id) REFERENCES services(id)
            );
        ");
    }

    public function create(int $serviceId, string $name, string $phone, string $email, string $address, string $scheduleAt, string $note): void {
        $stmt = $this->db->prepare('INSERT INTO service_bookings(service_id,name,phone,email,address,schedule_at,note) VALUES(?,?,?,?,?,?,?)');
        $stmt->execute([$serviceId,$name,$phone,$email,$address,$scheduleAt,$note]);
    }

    public function all(): array {
        return $this->db->query('
            SELECT sb.*, s.name as service_name
            FROM service_bookings sb
            JOIN services s ON sb.service_id = s.id
            ORDER BY sb.created_at DESC
        ')->fetchAll();
    }

    public function filter(string $keyword = '', ?int $serviceId = null): array {
        $sql = '
            SELECT sb.*, s.name as service_name
            FROM service_bookings sb
            JOIN services s ON sb.service_id = s.id
            WHERE 1=1
        ';
        $params = [];
        $keyword = trim($keyword);
        if ($keyword !== '') {
            $sql .= ' AND (sb.name LIKE ? OR sb.phone LIKE ? OR sb.email LIKE ?)';
            $kw = '%' . $keyword . '%';
            $params[] = $kw;
            $params[] = $kw;
            $params[] = $kw;
        }
        if ($serviceId) {
            $sql .= ' AND sb.service_id = ?';
            $params[] = $serviceId;
        }
        $sql .= ' ORDER BY sb.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
