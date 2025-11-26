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
        self::$extraColumnsEnsured = true;
    }

    public function create(int $userId, array $cart, array $customer, ?array $voucher = null, string $paymentMethod = 'cod'): int {
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
        $stmt = $this->db->prepare('INSERT INTO orders(user_id,code,total_amount,status,payment_method,customer_name,customer_phone,customer_email,customer_address,note,user_unread,created_at) VALUES(?,?,?,?,?,?,?,?,?,?,1,NOW())');
        $stmt->execute([$userId, $code, $total, 'pending', $paymentMethod, $customer['name'], $customer['phone'], $customer['email'], $customer['address'], $customer['note']]);
        $orderId = (int)$this->db->lastInsertId();
        $itemStmt = $this->db->prepare('INSERT INTO order_items(order_id,product_id,quantity,unit_price,total_price) VALUES(?,?,?,?,?)');
        foreach ($cart as $item) {
            $itemStmt->execute([$orderId, $item['product']['id'], $item['qty'], $item['product']['price'], $item['qty'] * $item['product']['price']]);
        }
        $this->db->commit();
        return $orderId;
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
    public function all(): array {
        return $this->db->query('SELECT * FROM orders ORDER BY created_at DESC')->fetchAll();
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
