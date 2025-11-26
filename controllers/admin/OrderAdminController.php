<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../models/Order.php';

class OrderAdminController extends Controller {
    private Order $orderModel;
    public function __construct(){ Auth::requireAdmin(); $this->orderModel=new Order(); }
    public function index(){ $orders=$this->orderModel->all(); $this->view('admin/order/index',compact('orders')); }
    public function updateStatus($id){ $status=$_POST['status']??'pending'; $this->orderModel->updateStatus((int)$id,$status); $this->redirect('/admin.php/orders'); }
}
