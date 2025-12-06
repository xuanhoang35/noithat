<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Service.php';

class ServiceController extends Controller {
    private Service $serviceModel;
    public function __construct(){ $this->serviceModel = new Service(); }

    public function book() {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $schedule_date = trim($_POST['schedule_date'] ?? '');
        $schedule_time = trim($_POST['schedule_time'] ?? '');
        $service_id = (int)($_POST['service_id'] ?? 0);
        $note = trim($_POST['note'] ?? '');

        $errors = [];
        if ($name === '' || mb_strlen($name) > 30) {
            $errors[] = 'Họ tên không được để trống và tối đa 30 ký tự.';
        }
        if ($phone === '' || !preg_match('/^0\\d{9}$/', $phone)) {
            $errors[] = 'Số điện thoại phải bắt đầu bằng 0 và đủ 10 chữ số.';
        }
        if ($email !== '') {
            if (mb_strlen($email) > 30) {
                $errors[] = 'Email tối đa 30 ký tự.';
            } elseif (!preg_match('/^[A-Za-z0-9._%+-]+@(gmail|email)[A-Za-z0-9.-]*\\.[A-Za-z0-9.-]+$/', $email)) {
                $errors[] = 'Email phải chứa @gmail hoặc @email.';
            }
        }

        $schedule_at = '';
        if ($schedule_date !== '' && $schedule_time !== '') {
            $dt = \DateTime::createFromFormat('Y-m-d H:i', $schedule_date . ' ' . $schedule_time);
            if ($dt) {
                $schedule_at = $dt->format('Y-m-d H:i:s');
            }
        }

        if ($errors) {
            $_SESSION['flash_error'] = implode(' ', $errors);
            $this->redirect('/services');
            return;
        }
        if ($name && $phone && $address && $schedule_at && $service_id > 0) {
            $this->serviceModel->createBooking($service_id, $name, $phone, $email, $address, $schedule_at, $note);
            $this->redirect('/services?success=1');
            return;
        }
        $this->redirect('/services?success=0');
    }
}
