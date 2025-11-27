<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/PasswordReset.php';

class UserAdminController extends Controller {
    private User $userModel;
    private PasswordReset $passwordResetModel;
    public function __construct(){
        Auth::requireAdmin();
        $this->userModel=new User();
        $this->passwordResetModel = new PasswordReset();
    }
    public function index(){
        $search = trim($_GET['search'] ?? '');
        $users=$this->userModel->all($search);
        $resets = $this->passwordResetModel->all();
        $this->view('admin/user/index',[
            'users' => $users,
            'search' => $search,
            'querySeen' => $_GET['seen'] ?? '',
            'resets' => $resets,
        ]);
    }
    public function toggleActive($id){
        $this->userModel->toggleActive((int)$id);
        $user = $this->userModel->findById((int)$id);
        if ($user && (int)($user['is_active'] ?? 1) !== 1) {
            $this->userModel->setOnline((int)$id, false);
        }
        $this->redirect('/admin.php/users');
    }
    public function resetPassword($id){
        $newPassword = trim($_POST['new_password'] ?? '');
        if ($newPassword !== '') {
            $this->passwordResetModel->complete((int)$id, $newPassword);
        }
        $this->redirect('/admin.php/users');
    }

    public function delete($id) {
        $id = (int)$id;
        // tránh tự xóa chính mình
        if ($id > 0 && $id !== (int)($_SESSION['user']['id'] ?? 0)) {
            $this->userModel->delete($id);
        }
        $this->redirect('/admin.php/users');
    }
}
