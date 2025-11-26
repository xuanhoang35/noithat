<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Chat.php';
require_once __DIR__ . '/../models/Order.php';

class NotifyController extends Controller {
    public function poll() {
        Auth::requireLogin();
        header('Content-Type: application/json');
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode(['orders_unread' => false, 'chat_unread' => false]);
            return;
        }
        $chat = new Chat();
        $order = new Order();
        echo json_encode([
            'orders_unread' => $order->hasUnread($userId),
            'chat_unread' => $chat->hasUnreadForUser($userId)
        ]);
    }
}
