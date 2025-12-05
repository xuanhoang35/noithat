<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../models/Order.php';

class OrderAdminController extends Controller {
    private Order $orderModel;
    public function __construct(){ Auth::requireAdmin(); $this->orderModel=new Order(); }
    public function index(){
        $search = trim($_GET['q'] ?? '');
        $orders=$this->orderModel->all($search);
        $this->view('admin/order/index',compact('orders','search'));
    }
    public function updateStatus($id){ $status=$_POST['status']??'pending'; $this->orderModel->updateStatus((int)$id,$status); $this->redirect('/admin.php/orders'); }
}
