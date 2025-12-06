<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Chat.php';

class NotifyAdminController extends Controller {
    public function poll() {
        Auth::requireAdmin();
        header('Content-Type: application/json');
        $db = Database::connection();
        $stats = [];
        $tables = [
            'orders' => 'orders',
            'complaints' => 'complaints',
            'services' => 'services',
            'users' => 'users'
        ];
        $seen = $_SESSION['admin_seen'] ?? [];
        $maxCreated = function($table) use ($db) {
            try {
                if ($table === 'services') {
                    return $db->query("SELECT COALESCE(MAX(schedule_at), MAX(created_at)) FROM services WHERE is_booking = 1")->fetchColumn() ?: date('Y-m-d H:i:s');
                }
                return $db->query("SELECT COALESCE(MAX(updated_at), MAX(created_at)) FROM {$table}")->fetchColumn() ?: date('Y-m-d H:i:s');
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
                if ($table === 'services') {
                    $stmt = $db->prepare("SELECT COUNT(*) FROM services WHERE is_booking = 1 AND created_at > ?");
                    $stmt->execute([$ts]);
                    $count = (int)$stmt->fetchColumn();
                } elseif ($table === 'users') {
                    // khách mới + yêu cầu reset mới (theo mốc seen users)
                    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE created_at > ?");
                    $stmt->execute([$ts]);
                    $newUsers = (int)$stmt->fetchColumn();
                    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE reset_token IS NOT NULL AND reset_requested_at > ?");
                    $stmt->execute([$ts]);
                    $newResets = (int)$stmt->fetchColumn();
                    $count = $newUsers + $newResets;
                } else {
                    $stmt = $db->prepare("SELECT COUNT(*) FROM {$table} WHERE created_at > ?");
                    $stmt->execute([$ts]);
                    $count = (int)$stmt->fetchColumn();
                }
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
        // chats: đếm hội thoại chưa đọc theo chat_messages
        try {
            $chatModel = new Chat();
            $chatUnread = $chatModel->hasUnreadForAdmin();
            $chatLatest = $db->query("SELECT COALESCE(MAX(updated_at), MAX(created_at)) FROM chat_messages")->fetchColumn();
        } catch (\Throwable $e) {
            $chatUnread = 0;
            $chatLatest = null;
        }
        $stats['chats'] = ['count' => $chatUnread, 'latest' => $chatLatest];
        $_SESSION['admin_seen'] = $seen;
        echo json_encode($stats);
    }
}
