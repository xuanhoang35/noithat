<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../config/database.php';

class NotifyAdminController extends Controller {
    public function poll() {
        Auth::requireAdmin();
        header('Content-Type: application/json');
        $db = Database::connection();
        $stats = [];
        $tables = [
            'orders' => 'orders',
            'complaints' => 'complaints',
            'services' => 'service_bookings',
            'users' => 'users'
        ];
        $seen = $_SESSION['admin_seen'] ?? [];
        $maxCreated = function($table) use ($db) {
            try {
                return $db->query("SELECT COALESCE(MAX(updated_at), MAX(created_at), MAX(schedule_at), MAX(completed_at), MAX(delivered_at)) FROM {$table}")->fetchColumn() ?: date('Y-m-d H:i:s');
            } catch (\Throwable $e) {
                return date('Y-m-d H:i:s');
            }
        };
        foreach ($tables as $key => $table) {
            if (!isset($seen[$key])) {
                $seen[$key] = strtotime($maxCreated($table));
            }
            $ts = date('Y-m-d H:i:s', $seen[$key]);
            try {
                $stmt = $db->prepare("SELECT COUNT(*) FROM {$table} WHERE created_at > ?");
                $stmt->execute([$ts]);
                $count = (int)$stmt->fetchColumn();
                $latest = $maxCreated($table);
            } catch (\Throwable $e) {
                $count = 0;
                $latest = null;
            }
            $stats[$key] = [
                'count' => $count,
                'latest' => $latest
            ];
        }
        // chats: đếm số phiên có tin nhắn chưa đọc cho admin (tồn tại tới khi admin mở)
        try {
            $chatUnread = (int)$db->query("SELECT COUNT(*) FROM chat_threads WHERE admin_unread=1")->fetchColumn();
            $chatLatest = $db->query("SELECT COALESCE(MAX(updated_at), MAX(created_at)) FROM chat_threads")->fetchColumn();
        } catch (\Throwable $e) {
            $chatUnread = 0;
            $chatLatest = null;
        }
        $stats['chats'] = ['count' => $chatUnread, 'latest' => $chatLatest];
        $_SESSION['admin_seen'] = $seen;
        echo json_encode($stats);
    }
}
