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
            'chats' => 'chat_threads',
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
        echo json_encode($stats);
    }
}
