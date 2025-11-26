<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Chat.php';

class ChatController extends Controller {
    private Chat $chatModel;
    public function __construct(){ $this->chatModel = new Chat(); }

    public function index() {
        Auth::requireLogin();
        $userId = $_SESSION['user']['id'] ?? 0;
        $forceNew = isset($_GET['new']) && $_GET['new'] == '1';
        $isEmbed = isset($_GET['embed']) && $_GET['embed'] == '1';
        $thread = $this->chatModel->getOrCreateThread((int)$userId, $forceNew);
        if (!$thread) { $this->redirect('/login'); return; }
        if ($forceNew) {
            // thêm thông báo để admin thấy phiên mới
            $this->chatModel->addMessage($thread['id'], (int)$userId, false, 'Khách hàng bắt đầu cuộc trò chuyện mới.');
        }
        $this->chatModel->markUserRead($thread['id']);
        $messages = $this->chatModel->messages($thread['id']);
        $view = $isEmbed ? 'chat/embed' : 'chat/index';
        $this->view($view, compact('thread','messages'));
    }

    public function send() {
        Auth::requireLogin();
        $content = trim($_POST['content'] ?? '');
        $userId = $_SESSION['user']['id'] ?? 0;
        $thread = $this->chatModel->getOrCreateThread((int)$userId);
        if (!$thread) { $this->redirect('/login'); return; }
        $action = $_POST['action'] ?? 'send';
        $redirectTo = !empty($_POST['embed']) ? '/chat?embed=1' : '/chat';
        if ($action === 'end') {
            // khách kết thúc: đóng phiên và thêm thông báo
            $this->chatModel->clearMessages($thread['id']);
            $this->chatModel->updateStatus($thread['id'], 'closed');
            $this->chatModel->markUserRead($thread['id']);
            $this->redirect($redirectTo); return;
        } elseif ($content !== '') {
            $this->chatModel->addMessage($thread['id'], (int)$userId, false, $content);
            $this->chatModel->updateStatus($thread['id'], 'open');
        }
        $this->redirect($redirectTo);
    }

    public function poll() {
        Auth::requireLogin();
        header('Content-Type: application/json');
        $threadId = (int)($_GET['thread_id'] ?? 0);
        $lastId = (int)($_GET['last_id'] ?? 0);
        if ($threadId <= 0) { echo json_encode([]); return; }
        $messages = $this->chatModel->messagesSince($threadId, $lastId);
        $this->chatModel->markUserRead($threadId);
        echo json_encode($messages);
    }
}
