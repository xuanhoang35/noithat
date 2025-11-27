<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Service.php';

class PageController extends Controller {
    public function about() {
        $this->view('about/index');
    }

    public function services() {
        $serviceModel = new Service();
        $services = $serviceModel->all();
        $success = isset($_GET['success']) && $_GET['success'] == '1';
        $this->view('services/index', compact('services','success'));
    }

    public function maintenancePage() {
        $configFile = __DIR__ . '/../config/maintenance.json';
        $data = [
            'title' => 'Chúng tôi đang bảo trì',
            'subtitle' => 'Sẽ trở lại sớm nhất',
            'message' => 'Xin lỗi vì sự bất tiện.',
            'image' => '',
            'video' => ''
        ];
        if (file_exists($configFile)) {
            $json = file_get_contents($configFile);
            $cfg = json_decode($json, true);
            if (is_array($cfg)) $data = array_merge($data, $cfg);
        }
        $this->view('maintenance', $data);
    }

    public function blocked() {
        $message = $_SESSION['blocked_message'] ?? 'Phiên làm việc đã kết thúc. Bạn sẽ được đăng xuất.';
        unset($_SESSION['blocked_message']);
        $this->view('auth/blocked', ['message' => $message]);
    }
}
