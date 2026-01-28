<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/Category.php';

class ProductAdminController extends Controller {
    private Product $productModel; private Category $categoryModel;
    public function __construct(){ Auth::requireAdmin(); $this->productModel=new Product(); $this->categoryModel=new Category(); }
    public function index(){
        $search = trim($_GET['q'] ?? '');
        $categoryId = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int)$_GET['category_id'] : null;
        $products=$this->productModel->all($categoryId ?: null, $search);
        $categories=$this->categoryModel->all();
        // Nếu chưa có danh mục, buộc tạo trước
        if (empty($categories)) {
            $this->redirect('/admin.php/categories');
        }
        $this->view('admin/product/index',[
            'products'=>$products,
            'categories'=>$categories,
            'search'=>$search,
            'categoryId'=>$categoryId
        ]);
    }
    public function store(){
        $data=[
            'name'=>$_POST['name']??'',
            'slug'=>strtolower(preg_replace('/\\s+/','-', $_POST['name']??'')),
            'category_id'=>(int)($_POST['category_id']??0),
            'price'=>(float)($_POST['price']??0),
            'stock'=>(int)($_POST['stock']??0),
            'description'=>$_POST['description']??'',
            'image'=>''
        ];
        $data['name'] = trim($data['name']);
        if ($data['name'] === '') {
            $_SESSION['flash_error'] = 'Vui lòng nhập tên sản phẩm.';
            $this->redirect('/admin.php/products');
        }
        if ($data['category_id']<=0 || !$this->categoryModel->find($data['category_id'])) {
            // Không có danh mục hợp lệ -> về trang danh mục để tạo
            $this->redirect('/admin.php/categories');
        }
        if ($this->productModel->existsByNameOrSlug($data['name'], $data['slug'])) {
            $_SESSION['flash_error'] = 'Tên sản phẩm này đã tồn tại.';
            $this->redirect('/admin.php/products');
        }
        // Upload file ảnh nếu có
        if (!empty($_FILES['image_file']['tmp_name']) && is_uploaded_file($_FILES['image_file']['tmp_name'])) {
            $uploadDir = __DIR__ . '/../../public/uploads';
            if (!is_dir($uploadDir) && !@mkdir($uploadDir, 0777, true)) {
                $_SESSION['flash_error'] = 'Không tạo được thư mục lưu ảnh sản phẩm. Vui lòng kiểm tra quyền ghi.';
                $this->redirect('/admin.php/products');
            }
            $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($_FILES['image_file']['name'], PATHINFO_FILENAME));
            $filename = $safeName . '_' . time() . ($ext ? '.' . $ext : '');
            $dest = $uploadDir . '/' . $filename;
            if (is_dir($uploadDir) && is_writable($uploadDir) && move_uploaded_file($_FILES['image_file']['tmp_name'], $dest)) {
                $data['image'] = 'public/uploads/' . $filename;
            } else {
                $_SESSION['flash_error'] = 'Tải ảnh sản phẩm thất bại. Vui lòng thử ảnh khác hoặc kiểm tra quyền ghi.';
                $this->redirect('/admin.php/products');
            }
        }
        // Nếu không upload, dùng link ảnh nếu có
        if (!$data['image'] && !empty($_POST['image_url'])) {
            $data['image'] = trim($_POST['image_url']);
        }
        $this->productModel->create($data);
        $_SESSION['flash_success'] = 'Đã thêm sản phẩm mới.';
        $this->redirect('/admin.php/products');
    }

    public function edit($id){
        $product = $this->productModel->find((int)$id);
        if (!$product) { http_response_code(404); echo 'Product not found'; return; }
        $categories = $this->categoryModel->all();
        $this->view('admin/product/edit', compact('product','categories'));
    }

    public function update($id){
        $product = $this->productModel->find((int)$id);
        if (!$product) { http_response_code(404); echo 'Product not found'; return; }
        $oldImage = $product['image'] ?? '';
        $data=[
            'name'=>$_POST['name']??'',
            'slug'=>strtolower(preg_replace('/\\s+/','-', $_POST['name']??'')),
            'category_id'=>(int)($_POST['category_id']??0),
            'price'=>(float)($_POST['price']??0),
            'stock'=>(int)($_POST['stock']??0),
            'description'=>$_POST['description']??'',
            'image'=>$product['image'] ?? ''
        ];
        $data['name'] = trim($data['name']);
        if ($data['name'] === '') {
            $_SESSION['flash_error'] = 'Vui lòng nhập tên sản phẩm.';
            $this->redirect('/admin.php/products/edit/' . (int)$id);
        }
        if ($data['category_id']<=0 || !$this->categoryModel->find($data['category_id'])) {
            $this->redirect('/admin.php/categories');
        }
        if ($this->productModel->existsByNameOrSlug($data['name'], $data['slug'], (int)$id)) {
            $_SESSION['flash_error'] = 'Tên sản phẩm này đã tồn tại.';
            $this->redirect('/admin.php/products/edit/' . (int)$id);
        }
        if (!empty($_FILES['image_file']['tmp_name']) && is_uploaded_file($_FILES['image_file']['tmp_name'])) {
            $uploadDir = __DIR__ . '/../../public/uploads';
            if (!is_dir($uploadDir) && !@mkdir($uploadDir, 0777, true)) {
                $_SESSION['flash_error'] = 'Không tạo được thư mục lưu ảnh sản phẩm. Vui lòng kiểm tra quyền ghi.';
                $this->redirect('/admin.php/products');
            }
            $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($_FILES['image_file']['name'], PATHINFO_FILENAME));
            $filename = $safeName . '_' . time() . ($ext ? '.' . $ext : '');
            $dest = $uploadDir . '/' . $filename;
            if (is_dir($uploadDir) && is_writable($uploadDir) && move_uploaded_file($_FILES['image_file']['tmp_name'], $dest)) {
                $data['image'] = 'public/uploads/' . $filename;
                $this->deleteLocalFile($oldImage);
            } else {
                $_SESSION['flash_error'] = 'Tải ảnh sản phẩm thất bại. Vui lòng thử ảnh khác hoặc kiểm tra quyền ghi.';
                $this->redirect('/admin.php/products');
            }
        }
        if (!$data['image'] && !empty($_POST['image_url'])) {
            $data['image'] = trim($_POST['image_url']);
            if ($data['image'] !== $oldImage) {
                $this->deleteLocalFile($oldImage);
            }
        }
        $this->productModel->update((int)$id, $data);
        $_SESSION['flash_success'] = 'Đã cập nhật sản phẩm.';
        $this->redirect('/admin.php/products');
    }

    public function delete($id){
        $this->productModel->delete((int)$id);
        $this->redirect('/admin.php/products');
    }

    private function deleteLocalFile(?string $path): void {
        $path = trim((string)$path);
        if ($path === '' || preg_match('#^(?:https?:)?//#', $path)) {
            return;
        }
        $full = realpath(__DIR__ . '/../../' . ltrim($path, '/'));
        if ($full && is_file($full)) {
            @unlink($full);
        }
    }
}
