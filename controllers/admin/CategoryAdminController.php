<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../models/Category.php';

class CategoryAdminController extends Controller {
    private Category $categoryModel;
    public function __construct(){ Auth::requireAdmin(); $this->categoryModel=new Category(); }
    public function index(){
        $search = trim($_GET['q'] ?? '');
        $categories=$this->categoryModel->all($search);
        $this->view('admin/category/index',compact('categories','search'));
    }
    public function store(){
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $_SESSION['flash_error'] = 'Vui lòng nhập tên danh mục.';
            $this->redirect('/admin.php/categories');
        }
        $slug = strtolower(preg_replace('/\\s+/', '-', $name));
        if ($this->categoryModel->existsByNameOrSlug($name, $slug)) {
            $_SESSION['flash_error'] = 'Tên danh mục này đã tồn tại.';
            $this->redirect('/admin.php/categories');
        }
        $this->categoryModel->create($name, $slug);
        $_SESSION['flash_success'] = 'Đã thêm danh mục mới.';
        $this->redirect('/admin.php/categories');
    }
    public function update($id){
        $name = trim($_POST['name'] ?? '');
        $id = (int)$id;
        if ($name === '') {
            $_SESSION['flash_error'] = 'Vui lòng nhập tên danh mục.';
            $this->redirect('/admin.php/categories?edit=' . $id);
        }
        $slug = strtolower(preg_replace('/\\s+/', '-', $name));
        if ($this->categoryModel->existsByNameOrSlug($name, $slug, $id)) {
            $_SESSION['flash_error'] = 'Tên danh mục này đã tồn tại.';
            $this->redirect('/admin.php/categories?edit=' . $id);
        }
        $this->categoryModel->update($id, $name, $slug);
        $_SESSION['flash_success'] = 'Đã cập nhật danh mục.';
        $this->redirect('/admin.php/categories');
    }
    public function delete($id){ $this->categoryModel->delete((int)$id); $this->redirect('/admin.php/categories'); }
}
