<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/ServiceBooking.php';

class ServiceController extends Controller {
    private ServiceBooking $bookingModel;
    public function __construct(){ $this->bookingModel = new ServiceBooking(); }

    public function book() {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $schedule_date = trim($_POST['schedule_date'] ?? '');
        $schedule_time = trim($_POST['schedule_time'] ?? '');
        $service_id = (int)($_POST['service_id'] ?? 0);
        $note = trim($_POST['note'] ?? '');

        $schedule_at = '';
        if ($schedule_date !== '' && $schedule_time !== '') {
            $dt = \DateTime::createFromFormat('Y-m-d H:i', $schedule_date . ' ' . $schedule_time);
            if ($dt) {
                $schedule_at = $dt->format('Y-m-d H:i:s');
            }
        }

        if ($name && $phone && $address && $schedule_at && $service_id > 0) {
            $this->bookingModel->create($service_id, $name, $phone, $email, $address, $schedule_at, $note);
            $this->redirect('/services?success=1');
            return;
        }
        $this->redirect('/services?success=0');
    }
}
