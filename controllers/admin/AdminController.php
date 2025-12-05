<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../config/database.php';

class AdminController extends Controller {
    public function __construct(){ Auth::requireAdmin(); }
    public function index(){
        $db = Database::connection();
        $counts = [
            'users' => (int)$db->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'orders' => (int)$db->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
            'service_bookings' => (int)$db->query('SELECT COUNT(*) FROM services WHERE is_booking = 1')->fetchColumn(),
            'chats' => (int)$db->query('SELECT COUNT(DISTINCT user_id) FROM chat_messages')->fetchColumn(),
            'complaints' => (int)$db->query('SELECT COUNT(*) FROM complaints')->fetchColumn(),
        ];
        $this->view('admin/index', compact('counts'));
    }
}
