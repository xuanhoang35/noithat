<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../models/Complaint.php';

class ComplaintAdminController extends Controller {
    private Complaint $complaintModel;
    public function __construct(){ Auth::requireAdmin(); $this->complaintModel = new Complaint(); }
    public function index(){
        $complaints = $this->complaintModel->all();
        $this->view('admin/complaint/index', compact('complaints'));
    }
    public function updateStatus($id){
        $status = $_POST['status'] ?? 'new';
        $response = $_POST['response'] ?? null;
        $this->complaintModel->updateStatus((int)$id, $status, $response);
        $this->redirect('/admin.php/complaints');
    }

    public function reply($id){
        $status = $_POST['status'] ?? 'in_progress';
        $response = trim($_POST['response'] ?? '');
        if ($response !== '') {
            $this->complaintModel->addReply((int)$id, $_SESSION['user']['id'] ?? 0, true, $response);
        }
        // nếu chọn resolved thì đóng luôn
        $this->complaintModel->updateStatus((int)$id, $status, null);
        $this->redirect('/admin.php/complaints');
    }
}
