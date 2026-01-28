<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller {
    public function loginForm() { $this->view('auth/login'); }
    public function registerForm() { $this->view('auth/register'); }
    public function forgotForm() { $this->view('auth/forgot'); }

    public function register() {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $config = include __DIR__ . '/../config/config.php';
        if ($name === '' || $email === '' || $phone === '' || $password === '') {
            $error = 'Vui lòng nhập đầy đủ họ tên, email, số điện thoại và mật khẩu.';
            $this->view('auth/register', compact('error', 'name', 'email', 'phone'));
            return;
        }
        if (mb_strlen($name) > 30) {
            $error = 'Tên tối đa 30 ký tự.';
            $this->view('auth/register', compact('error', 'name', 'email', 'phone'));
            return;
        }
        if (mb_strlen($email) > 30) {
            $error = 'Email tối đa 30 ký tự.';
            $this->view('auth/register', compact('error', 'name', 'email', 'phone'));
            return;
        }
        if (strlen($password) > 30) {
            $error = 'Mật khẩu tối đa 30 ký tự.';
            $this->view('auth/register', compact('error', 'name', 'email', 'phone'));
            return;
        }
        if (!preg_match('/^[A-Za-z0-9]{1,30}$/', $password)) {
            $error = 'Mật khẩu chỉ gồm chữ không dấu và số (không ký tự đặc biệt), tối đa 30 ký tự.';
            $this->view('auth/register', compact('error', 'name', 'email', 'phone'));
            return;
        }
        if (!preg_match('/^0\\d{9}$/', $phone)) {
            $error = 'Số điện thoại phải bắt đầu bằng 0 và có đúng 10 chữ số.';
            $this->view('auth/register', compact('error', 'name', 'email', 'phone'));
            return;
        }
        // email phải chứa @gmail hoặc @email, không dùng ký tự có dấu, đuôi miền tự do (.com, .edu, .yahoo, ...)
        $emailPattern = '/^[A-Za-z0-9._%+-]+@(gmail|email)[A-Za-z0-9.-]*\\.[A-Za-z0-9.-]+$/';
        if (!preg_match($emailPattern, $email)) {
            $error = 'Email không hợp lệ (chỉ chữ không dấu/số, chứa @gmail hoặc @email).';
            $this->view('auth/register', compact('error', 'name', 'email', 'phone'));
            return;
        }
        // Không cho đăng ký trùng email admin cố định
        if (strcasecmp($email, $config['admin']['email']) === 0) {
            $error = 'Email này đã được dùng cho tài khoản admin cố định.';
            $this->view('auth/register', compact('error'));
            return;
        }
        $userModel = new User();
        if ($userModel->findByEmail($email)) { $error='Email đã tồn tại'; $this->view('auth/register', compact('error')); return; }
        if ($userModel->findByPhone($phone)) { $error='Số điện thoại đã tồn tại'; $this->view('auth/register', compact('error')); return; }
        $userModel->create($name,$email,$phone,$password);
        $user = $userModel->findByEmail($email);
        $_SESSION['welcome_message'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
        // Không tự đăng nhập; quay về trang đăng nhập sau 1s
        $success = $_SESSION['welcome_message'];
        $redirect = base_url('login');
        $this->view('auth/register', compact('success', 'redirect'));
    }

    public function login() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $config = include __DIR__ . '/../config/config.php';

        // Check admin cố định
        if (strcasecmp($email, $config['admin']['email']) === 0 && $password === $config['admin']['password']) {
            $adminUser = [
                'id' => 0,
                'name' => 'Admin',
                'email' => $config['admin']['email'],
                'role' => 'admin'
            ];
            Auth::login($adminUser);
            $this->redirect('/admin.php');
            return;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            if ((int)($user['is_active'] ?? 1) !== 1) {
                $error = 'Tài khoản của bạn đang tạm khóa. Vui lòng liên hệ quản trị viên để được hỗ trợ mở khóa.';
                $this->view('auth/login', compact('error'));
                return;
            }
            // Nếu không phải email admin cố định thì vẫn cho user; nếu bản ghi DB role admin nhưng email khác, hạ xuống user
            if ($user['role'] === 'admin' && strcasecmp($user['email'], $config['admin']['email']) !== 0) {
                $user['role'] = 'user';
            }
            Auth::login($user);
            $_SESSION['welcome_message'] = 'Chào mừng quý khách đến với Nội Thất Store';
            if ($user['role'] === 'admin') {
                $this->redirect('/admin.php');
            } else {
                $this->redirect('/'); // chuyển thẳng về trang chủ, ticker xử lý display
            }
            return;
        }
        $error='Sai thông tin đăng nhập';
        $this->view('auth/login', compact('error'));
    }

    public function forgotSubmit() {
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        if ($email === '' || $phone === '') {
            $error = 'Vui lòng nhập đầy đủ email và số điện thoại.';
            $this->view('auth/forgot', compact('error', 'email', 'phone'));
            return;
        }
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        if (!$user || trim((string)$user['phone']) !== $phone) {
            $error = 'Thông tin không khớp với bất kỳ tài khoản nào.';
            $this->view('auth/forgot', compact('error', 'email', 'phone'));
            return;
        }
        if ((int)($user['is_active'] ?? 1) !== 1) {
            $error = 'Tài khoản đang bị khóa. Vui lòng liên hệ quản trị viên.';
            $this->view('auth/forgot', compact('error', 'email', 'phone'));
            return;
        }
        $passwordReset = new User();
        $requestId = $passwordReset->requestPasswordReset((int)$user['id'], $email, $phone);
        $_SESSION['reset_request_id'] = $requestId;
        $this->redirect('/forgot/wait/' . $requestId);
    }

    public function forgotWait($id = null) {
        $id = (string)$id;
        $saved = (string)($_SESSION['reset_request_id'] ?? '');
        if ($id === '' || $saved !== $id) {
            $this->redirect('/forgot');
            return;
        }
        $this->view('auth/forgot_wait', ['requestId' => $id]);
    }

    public function forgotStatus($id) {
        header('Content-Type: application/json');
        $id = (string)$id;
        $saved = (string)($_SESSION['reset_request_id'] ?? '');
        if ($id === '' || $saved !== $id) {
            echo json_encode(['status' => 'invalid']);
            return;
        }
        $passwordReset = new User();
        $reset = $passwordReset->findPasswordReset($id);
        if (!$reset) {
            echo json_encode(['status' => 'missing']);
            return;
        }
        if ($reset['status'] === 'completed' || $reset['status'] === 'delivered') {
            $passwordReset->markResetDelivered($id);
            unset($_SESSION['reset_request_id']);
            if (($reset['new_password_plain'] ?? '') === '__REJECTED__') {
                echo json_encode(['status' => 'rejected']);
            } else {
                echo json_encode(['status' => 'completed', 'password' => $reset['new_password_plain']]);
            }
            return;
        }
        echo json_encode(['status' => 'pending']);
    }

    public function forgotResend($id) {
        $id = (string)$id;
        $saved = (string)($_SESSION['reset_request_id'] ?? '');
        if ($id === '' || $saved !== $id) {
            $this->redirect('/forgot');
            return;
        }
        $passwordReset = new User();
        $newId = $passwordReset->resendPasswordReset($id);
        if ($newId) {
            $_SESSION['reset_request_id'] = $newId;
            $this->redirect('/forgot/wait/' . $newId);
            return;
        }
        $this->redirect('/forgot');
    }

    public function logout() { Auth::logout(); $this->redirect('/'); }
}
