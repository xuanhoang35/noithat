<?php
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/Service.php';

class ServiceBooking extends Model {
    private Service $serviceModel;

    public function __construct() {
        parent::__construct();
        $this->serviceModel = new Service();
    }

    public function create(int $serviceId, string $name, string $phone, string $email, string $address, string $scheduleAt, string $note): void {
        $base = $this->serviceModel->find($serviceId);
        $serviceName = $base ? $base['name'] : 'Dịch vụ';
        $price = $base ? (float)$base['price'] : 0;
        $stmt = $this->db->prepare('
            INSERT INTO services(parent_service_id, name, description, sla, price, is_booking, customer_name, customer_phone, customer_email, customer_address, schedule_at, note, created_at)
            VALUES(?,?,?,?,?,1,?,?,?,?,?,?,NOW())
        ');
        $stmt->execute([$serviceId, $serviceName, $base['description'] ?? '', $base['sla'] ?? '', $price, $name, $phone, $email, $address, $scheduleAt, $note]);
    }

    public function all(): array {
        return $this->filter();
    }

    public function filter(string $keyword = '', ?int $serviceId = null): array {
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
}
