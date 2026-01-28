<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../models/Service.php';

class ServiceAdminController extends Controller {
    private Service $serviceModel;
    public function __construct(){ Auth::requireAdmin(); $this->serviceModel = new Service(); }

    public function index() {
        $services = $this->serviceModel->all();
        $search = trim($_GET['q'] ?? '');
        $serviceId = isset($_GET['service_id']) && $_GET['service_id'] !== '' ? (int)$_GET['service_id'] : null;
        $bookings = $this->serviceModel->bookings($search, $serviceId);
        $this->view('admin/service/index', [
            'services' => $services,
            'bookings' => $bookings,
            'search' => $search,
            'serviceId' => $serviceId
        ]);
    }

    public function store() {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $sla = trim($_POST['sla'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        if ($name === '') {
            $_SESSION['flash_error'] = 'Vui lòng nhập tên dịch vụ.';
            $this->redirect('/admin.php/services');
        }
        if ($this->serviceModel->existsByName($name)) {
            $_SESSION['flash_error'] = 'Tên dịch vụ đã tồn tại.';
            $this->redirect('/admin.php/services');
        }
        $this->serviceModel->create($name, $description, $sla, $price);
        $_SESSION['flash_success'] = 'Đã thêm dịch vụ.';
        $this->redirect('/admin.php/services');
    }

    public function update($id) {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $sla = trim($_POST['sla'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $id = (int)$id;
        if ($name === '') {
            $_SESSION['flash_error'] = 'Vui lòng nhập tên dịch vụ.';
            $this->redirect('/admin.php/services?edit=' . $id);
        }
        if ($this->serviceModel->existsByName($name, $id)) {
            $_SESSION['flash_error'] = 'Tên dịch vụ đã tồn tại.';
            $this->redirect('/admin.php/services?edit=' . $id);
        }
        $this->serviceModel->update($id, $name, $description, $sla, $price);
        $_SESSION['flash_success'] = 'Đã cập nhật dịch vụ.';
        $this->redirect('/admin.php/services');
    }

    public function delete($id) {
        $this->serviceModel->delete((int)$id);
        $this->redirect('/admin.php/services');
    }
}
