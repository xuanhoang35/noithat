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
        // Nếu bắt đầu phiên mới: xóa lịch sử, mở lại
        if ($forceNew) {
            $this->chatModel->clearMessages((int)$userId);
            $this->chatModel->updateStatus((int)$userId, 'open');
            $this->chatModel->addMessage((int)$userId, (int)$userId, false, 'Khách hàng bắt đầu cuộc trò chuyện mới.', 'open');
            $thread = $this->chatModel->findThread((int)$userId);
        }
        $this->chatModel->markUserRead((int)$userId);
        $messages = $this->chatModel->messages((int)$userId);
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
            $this->chatModel->clearMessages((int)$userId);
            // Ghi nhận cho admin biết khách đã kết thúc
            $this->chatModel->addMessage((int)$userId, (int)$userId, true, 'Khách đã kết thúc hội thoại.', 'closed');
            $this->chatModel->updateStatus((int)$userId, 'closed');
            $this->chatModel->markUserRead((int)$userId);
            $this->redirect($redirectTo); return;
        } elseif ($content !== '') {
            // Nếu trước đó đã đóng, làm sạch lịch sử và mở lại
            if (($thread['status'] ?? '') === 'closed') {
                $this->chatModel->clearMessages((int)$userId);
            }
            $this->chatModel->addMessage((int)$userId, (int)$userId, false, $content, 'open');
            $this->chatModel->updateStatus((int)$userId, 'open');
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
