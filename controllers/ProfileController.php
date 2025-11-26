<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Complaint.php';

class ProfileController extends Controller {
    private User $userModel;
    private Complaint $complaintModel;
    public function __construct(){
        $this->userModel = new User();
        $this->complaintModel = new Complaint();
    }
    public function show() {
        Auth::requireLogin();
        $user = $this->userModel->findById($_SESSION['user']['id']);
        if (!$user) {
            // Nếu không tìm thấy user (ví dụ admin cố định id=0), buộc đăng nhập lại
            $this->redirect('/login');
        }
        $complaints = $this->complaintModel->byUser($_SESSION['user']['id']);
        $this->view('profile/index', compact('user','complaints'));
    }
    public function update() {
        Auth::requireLogin();
        $currentUser = $this->userModel->findById($_SESSION['user']['id']);
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $oldAvatar = $currentUser['avatar'] ?? null;
        $avatarPath = null;
        if (!empty($_FILES['avatar']['tmp_name'] ?? '')) {
            $file = $_FILES['avatar'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $ext = $ext ? strtolower($ext) : 'jpg';
            $dir = __DIR__ . '/../public/uploads/profile';
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $fileName = 'avatar_' . $_SESSION['user']['id'] . '_' . time() . '.' . $ext;
            $target = $dir . '/' . $fileName;
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $avatarPath = 'public/uploads/profile/' . $fileName;
                $this->deleteOldFile($oldAvatar, 'public/Profile/user-iconprofile.png');
            }
        }
        $this->userModel->updateProfile($_SESSION['user']['id'], $name, $phone, $address, $avatarPath);
        // cập nhật session
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['phone'] = $phone;
        $_SESSION['user']['address'] = $address;
        if ($avatarPath) {
            $_SESSION['user']['avatar'] = $avatarPath;
        }
        $_SESSION['flash_success'] = 'Cập nhật thông tin thành công';
        $this->redirect('/profile');
    }

    private function deleteOldFile(?string $path, string $skip = ''): void {
        $path = trim((string)$path);
        if ($path === '' || $path === $skip) return;
        if (preg_match('#^(?:https?:)?//#', $path)) return;
        $full = realpath(__DIR__ . '/../' . ltrim($path, '/'));
        if ($full && is_file($full)) {
            @unlink($full);
        }
    }
}
