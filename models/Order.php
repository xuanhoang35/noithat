<?php
require_once __DIR__ . '/../core/Model.php';
class Order extends Model {
    private static bool $extraColumnsEnsured = false;

    private function ensureColumns(): void {
        if (self::$extraColumnsEnsured) return;
        try {
            $this->db->exec("ALTER TABLE orders ADD COLUMN payment_method VARCHAR(30) DEFAULT 'cod'");
        } catch (\Throwable $e) {}
        try {
            $this->db->exec("ALTER TABLE orders ADD COLUMN user_unread TINYINT(1) DEFAULT 0");
        } catch (\Throwable $e) {}
        try {
            $this->db->exec("ALTER TABLE orders ADD COLUMN items_json LONGTEXT NULL");
        } catch (\Throwable $e) {}
        self::$extraColumnsEnsured = true;
    }

    public function create(int $userId, array $cart, array $customer, ?array $voucher = null, string $paymentMethod = 'cod'): array {
        $this->ensureColumns();
        if ($userId <= 0) { throw new \InvalidArgumentException('User không hợp lệ'); }
        // đảm bảo user tồn tại trước khi tạo order
        $exists = $this->db->prepare('SELECT 1 FROM users WHERE id=?');
        $exists->execute([$userId]);
        if (!$exists->fetchColumn()) { throw new \RuntimeException('User không tồn tại'); }
        $this->db->beginTransaction();
        $total = 0;
        $discountTotal = 0;
        $voucherPercent = $voucher ? (int)$voucher['discount_percent'] : 0;
        $voucherCategory = $voucher['category_id'] ?? null;
        foreach ($cart as $item) {
            $line = $item['product']['price'] * $item['qty'];
            $total += $line;
            if ($voucher && (empty($voucherCategory) || (int)$item['product']['category_id'] === (int)$voucherCategory)) {
                $discountTotal += round($line * $voucherPercent / 100);
            }
        }
        if ($voucher) {
            $total = max(0, $total - $discountTotal);
        }
        $code = 'OR' . date('YmdHis') . rand(100, 999);
        $items = [];
        foreach ($cart as $item) {
            $items[] = [
                'product_id' => $item['product']['id'],
                'name' => $item['product']['name'],
                'price' => $item['product']['price'],
                'qty' => $item['qty'],
                'total' => $item['qty'] * $item['product']['price']
            ];
        }
        $stmt = $this->db->prepare('INSERT INTO orders(user_id,code,total_amount,status,payment_method,customer_name,customer_phone,customer_email,customer_address,note,user_unread,items_json,created_at) VALUES(?,?,?,?,?,?,?,?,?,?,1,?,NOW())');
        $stmt->execute([$userId, $code, $total, 'pending', $paymentMethod, $customer['name'], $customer['phone'], $customer['email'], $customer['address'], $customer['note'], json_encode($items, JSON_UNESCAPED_UNICODE)]);
        $orderId = (int)$this->db->lastInsertId();
        // Trừ tồn kho
        $updateStock = $this->db->prepare('UPDATE products SET stock = GREATEST(stock - ?, 0) WHERE id = ?');
        foreach ($items as $it) {
            $updateStock->execute([(int)$it['qty'], (int)$it['product_id']]);
        }
        $this->db->commit();
        return ['id' => $orderId, 'code' => $code];
    }

    public function byUser(int $userId): array {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    public function findByIdForUser(int $orderId,int $userId): ?array {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE id=? AND user_id=? LIMIT 1');
        $stmt->execute([$orderId,$userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    public function all(string $keyword = ''): array {
        $sql = 'SELECT * FROM orders';
        $params = [];
        $kw = trim($keyword);
        if ($kw !== '') {
            $sql .= ' WHERE code LIKE ? OR customer_name LIKE ? OR customer_phone LIKE ? OR customer_email LIKE ?';
            $like = '%' . $kw . '%';
            $params = [$like, $like, $like, $like];
        }
        $sql .= ' ORDER BY created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public function updateStatus(int $id, string $status): void {
        $this->ensureColumns();
        $timeCol = ['processing' => 'confirmed_at', 'shipping' => 'shipping_at', 'completed' => 'completed_at', 'cancelled' => 'cancelled_at'];
        $col = $timeCol[$status] ?? null;
        if ($col) {
            $stmt = $this->db->prepare("UPDATE orders SET status=?, {$col}=NOW(), user_unread=1 WHERE id=?");
            $stmt->execute([$status, $id]);
        } else {
            $stmt = $this->db->prepare('UPDATE orders SET status=?, user_unread=1 WHERE id=?');
            $stmt->execute([$status, $id]);
        }
    }

    public function markAllRead(int $userId): void {
        $this->ensureColumns();
        $stmt = $this->db->prepare('UPDATE orders SET user_unread=0 WHERE user_id=?');
        $stmt->execute([$userId]);
    }

    public function hasUnread(int $userId): bool {
        $this->ensureColumns();
        $stmt = $this->db->prepare('SELECT 1 FROM orders WHERE user_id=? AND user_unread=1 LIMIT 1');
        $stmt->execute([$userId]);
        return (bool)$stmt->fetchColumn();
    }
}
