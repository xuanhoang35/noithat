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
            'services' => 'service_bookings'
        ];
        foreach ($tables as $key => $table) {
            try {
                $count = (int)$db->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
                $latest = $db->query("SELECT COALESCE(MAX(updated_at), MAX(created_at), MAX(schedule_at), MAX(completed_at), MAX(delivered_at)) FROM {$table}")->fetchColumn();
            } catch (\Throwable $e) {
                $count = 0;
                $latest = null;
            }
            $stats[$key] = [
                'count' => $count,
                'latest' => $latest
            ];
        }
        // chats: đếm số phiên có tin nhắn chưa đọc cho admin
        try {
            $chatUnread = (int)$db->query("SELECT COUNT(*) FROM chat_threads WHERE admin_unread=1")->fetchColumn();
            $chatLatest = $db->query("SELECT COALESCE(MAX(updated_at), MAX(created_at)) FROM chat_threads")->fetchColumn();
        } catch (\Throwable $e) {
            $chatUnread = 0;
            $chatLatest = null;
        }
        $stats['chats'] = ['count' => $chatUnread, 'latest' => $chatLatest];
        echo json_encode($stats);
    }
}
