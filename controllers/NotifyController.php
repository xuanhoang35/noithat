<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Chat.php';
require_once __DIR__ . '/../models/Order.php';

class NotifyController extends Controller {
    public function poll() {
        header('Content-Type: application/json');
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        $accountLocked = false;
        try {
            require_once __DIR__ . '/../models/User.php';
            $u = (new User())->findById($userId);
            $accountLocked = !$u || (int)($u['is_active'] ?? 1) !== 1;
        } catch (\Throwable $e) {
            $accountLocked = false;
        }
        if ($accountLocked) {
            $_SESSION['flash_error'] = 'Tài khoản đã bị khóa. Vui lòng đăng nhập lại.';
            unset($_SESSION['user']);
        }
        $chat = new Chat();
        $order = new Order();
        echo json_encode([
            'orders_unread' => $userId > 0 ? $order->hasUnread($userId) : false,
            'chat_unread' => $userId > 0 ? $chat->hasUnreadForUser($userId) : false,
            'account_locked' => $accountLocked
        ]);
    }
}
