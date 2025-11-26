<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../models/Service.php';
require_once __DIR__ . '/../../models/ServiceBooking.php';

class ServiceAdminController extends Controller {
    private Service $serviceModel;
    private ServiceBooking $bookingModel;
    public function __construct(){ Auth::requireAdmin(); $this->serviceModel = new Service(); $this->bookingModel = new ServiceBooking(); }

    public function index() {
        $services = $this->serviceModel->all();
        $search = trim($_GET['q'] ?? '');
        $serviceId = isset($_GET['service_id']) && $_GET['service_id'] !== '' ? (int)$_GET['service_id'] : null;
        $bookings = $this->bookingModel->filter($search, $serviceId);
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
        if ($name !== '') {
            $this->serviceModel->create($name, $description, $sla, $price);
        }
        $this->redirect('/admin.php/services');
    }

    public function update($id) {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $sla = trim($_POST['sla'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        if ($name !== '') {
            $this->serviceModel->update((int)$id, $name, $description, $sla, $price);
        }
        $this->redirect('/admin.php/services');
    }

    public function delete($id) {
        $this->serviceModel->delete((int)$id);
        $this->redirect('/admin.php/services');
    }
}
