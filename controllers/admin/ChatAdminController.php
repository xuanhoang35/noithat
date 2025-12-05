<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../models/Chat.php';

class ChatAdminController extends Controller {
    private Chat $chatModel;
    public function __construct(){ Auth::requireAdmin(); $this->chatModel = new Chat(); }

    public function index(){
        $threads = $this->chatModel->threads();
        $this->view('admin/chat/index', compact('threads'));
    }

    public function show($id){
        $thread = $this->chatModel->findThread((int)$id);
        if (!$thread) { http_response_code(404); echo 'Thread not found'; return; }
        $messages = $this->chatModel->messages((int)$id);
        $this->chatModel->markAdminRead((int)$id);
        $this->view('admin/chat/show', compact('thread','messages'));
    }

    public function reply($id){
        $thread = $this->chatModel->findThread((int)$id);
        if (!$thread) { http_response_code(404); echo 'Thread not found'; return; }
        if (($thread['status'] ?? '') === 'closed') {
            $this->redirect("/admin.php/chats/show/$id");
            return;
        }
        $content = trim($_POST['content'] ?? '');
        $status = $_POST['status'] ?? $thread['status'];
        $adminId = $_SESSION['user']['id'] ?? $thread['id'];
        if ($content !== '') {
            $this->chatModel->addMessage((int)$id, (int)$adminId, true, $content, $status);
        }
        $this->chatModel->updateStatus((int)$id, $status);
        $this->redirect("/admin.php/chats/show/$id");
    }

    public function poll($id) {
        Auth::requireAdmin();
        header('Content-Type: application/json');
        $threadId = (int)$id;
        $lastId = (int)($_GET['last_id'] ?? 0);
        if ($threadId <= 0) { echo json_encode([]); return; }
        $messages = $this->chatModel->messagesSince($threadId, $lastId);
        $this->chatModel->markAdminRead($threadId);
        echo json_encode($messages);
    }
}
