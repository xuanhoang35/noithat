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
}
