<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Complaint.php';
require_once __DIR__ . '/../models/Order.php';

class ComplaintController extends Controller {
    private Complaint $complaintModel;
    private Order $orderModel;
    public function __construct(){
        $this->complaintModel = new Complaint();
        $this->orderModel = new Order();
    }

    public function createForm($orderId){
        Auth::requireLogin();
        $order = $this->orderModel->findByIdForUser((int)$orderId, $_SESSION['user']['id']);
        if (!$order) { http_response_code(404); echo 'Order not found'; return; }
        $this->view('complaint/create', compact('order'));
    }

    public function store($orderId){
        Auth::requireLogin();
        $order = $this->orderModel->findByIdForUser((int)$orderId, $_SESSION['user']['id']);
        if (!$order) { http_response_code(404); echo 'Order not found'; return; }
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if ($title === '' || $content === '') {
            $error = 'Vui lòng nhập tiêu đề và nội dung';
            $this->view('complaint/create', compact('order','error'));
            return;
        }
        $this->complaintModel->create($_SESSION['user']['id'], (int)$orderId, $title, $content);
        $this->redirect('/orders');
    }

    public function reply($id){
        Auth::requireLogin();
        $complaints = $this->complaintModel->byUser($_SESSION['user']['id']);
        $target = null;
        foreach ($complaints as $c) { if ($c['id'] == (int)$id) { $target = $c; break; } }
        if (!$target) { http_response_code(404); echo 'Complaint not found'; return; }
        if ($target['status'] === 'resolved') { $this->redirect('/profile'); return; }
        $content = trim($_POST['content'] ?? '');
        if ($content !== '') {
            $this->complaintModel->addReply((int)$id, $_SESSION['user']['id'], false, $content);
        }
        $this->redirect('/profile');
    }
}
