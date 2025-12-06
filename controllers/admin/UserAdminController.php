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
        $onlineCount = 0;
        foreach ($users as $u) {
            if (!empty($u['is_online'])) {
                $onlineCount++;
            }
        }
        $resetMap = [];
        foreach ($resets as $r) {
            $resetMap[$r['user_id']] = $r;
        }
        $this->view('admin/user/index',[
            'users' => $users,
            'search' => $search,
            'querySeen' => $_GET['seen'] ?? '',
            'resets' => $resets,
            'resetMap' => $resetMap,
            'onlineCount' => $onlineCount,
        ]);
    }

    public function resetsCount() {
        header('Content-Type: application/json');
        $count = 0;
        try {
            $db = Database::connection();
            $stmt = $db->query("SELECT COUNT(*) FROM users WHERE reset_token IS NOT NULL");
            $count = (int)$stmt->fetchColumn();
        } catch (\Throwable $e) { $count = 0; }
        echo json_encode(['resets' => $count]);
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

    public function rejectReset($id) {
        $token = (string)$id;
        $this->passwordResetModel->reject($token);
        $_SESSION['flash_info'] = 'Đã từ chối yêu cầu cấp mật khẩu.';
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

    // API: trạng thái online/active realtime
    public function status() {
        header('Content-Type: application/json');
        $rows = $this->userModel->all(); // đã lọc deleted_at
        $data = [];
        foreach ($rows as $u) {
            $data[] = [
                'id' => (int)$u['id'],
                'is_online' => (int)($u['is_online'] ?? 0),
                'is_active' => (int)($u['is_active'] ?? 1),
                'password_plain' => $u['password_plain'] ?? ''
            ];
        }
        echo json_encode($data);
    }

    public function resets() {
        header('Content-Type: application/json');
        $list = $this->passwordResetModel->all();
        $pending = array_values(array_filter($list, function($r){
            return ($r['status'] ?? '') === 'pending';
        }));
        echo json_encode($pending);
    }

    public function edit($id) {
        http_response_code(404);
        echo 'Not supported';
    }

    public function update($id) {
        $user = $this->userModel->findById((int)$id);
        if (!$user) { http_response_code(404); echo 'User not found'; return; }
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $role = $_POST['role'] ?? $user['role'];
        $isActive = isset($_POST['is_active']) && $_POST['is_active'] === '1' ? 1 : 0;
        $newPassword = trim($_POST['password'] ?? '');
        if ($name === '' || $email === '' || $phone === '') {
            $_SESSION['flash_error'] = 'Vui lòng nhập đủ tên, email, số điện thoại.';
            $this->redirect('/admin.php/users/edit/' . $id);
        }
        // check email dup
        $existing = $this->userModel->findByEmail($email);
        if ($existing && (int)$existing['id'] !== (int)$id) {
            $_SESSION['flash_error'] = 'Email đã tồn tại trên hệ thống.';
            $this->redirect('/admin.php/users/edit/' . $id);
        }
        $payload = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'role' => $role,
            'is_active' => $isActive
        ];
        if ($newPassword !== '' && $newPassword !== ($user['password_plain'] ?? '')) {
            $payload['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
            $payload['password_plain'] = $newPassword;
        }
        $this->userModel->updateAdmin((int)$id, $payload);
        $this->redirect('/admin.php/users');
    }
}
