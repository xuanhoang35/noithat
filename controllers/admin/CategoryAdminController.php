<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../models/Category.php';

class CategoryAdminController extends Controller {
    private Category $categoryModel;
    public function __construct(){ Auth::requireAdmin(); $this->categoryModel=new Category(); }
    public function index(){ $categories=$this->categoryModel->all(); $this->view('admin/category/index',compact('categories')); }
    public function store(){ $name=trim($_POST['name']??''); $slug=strtolower(preg_replace('/\\s+/','-', $name)); $this->categoryModel->create($name,$slug); $this->redirect('/admin.php/categories'); }
    public function update($id){ $name=trim($_POST['name']??''); if($name!==''){ $slug=strtolower(preg_replace('/\\s+/','-', $name)); $this->categoryModel->update((int)$id,$name,$slug);} $this->redirect('/admin.php/categories'); }
    public function delete($id){ $this->categoryModel->delete((int)$id); $this->redirect('/admin.php/categories'); }
}
