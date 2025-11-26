<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

class ProductController extends Controller {
    private Product $productModel;
    private Category $categoryModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    public function index() {
        $categoryId = $_GET['category'] ?? null;
        $keyword = trim($_GET['q'] ?? '');
        $priceSort = $_GET['price_sort'] ?? '';
        $priceRange = $_GET['price_range'] ?? '';
        $priceMin = null;
        $priceMax = null;
        if ($priceRange === 'custom') {
            if (isset($_GET['price_min']) && $_GET['price_min'] !== '') {
                $priceMin = max(0, (int)$_GET['price_min']);
            }
            if (isset($_GET['price_max']) && $_GET['price_max'] !== '') {
                $priceMax = max(0, (int)$_GET['price_max']);
            }
            if ($priceMin !== null && $priceMax !== null && $priceMin > $priceMax) {
                [$priceMin, $priceMax] = [$priceMax, $priceMin];
            }
        }
        $products = $this->productModel->all($categoryId, $keyword, $priceSort, $priceRange, $priceMin, $priceMax);
        $categories = $this->categoryModel->all();
        $this->view('product/list', [
            'products' => $products,
            'categories' => $categories,
            'categoryId' => $categoryId,
            'keyword' => $keyword,
            'priceSort' => $priceSort,
            'priceRange' => $priceRange,
            'priceMin' => $priceMin,
            'priceMax' => $priceMax,
        ]);
    }

    public function show($id) {
        $product = $this->productModel->find((int)$id);
        if (!$product) {
            http_response_code(404);
            echo 'Product not found';
            return;
        }
        $this->view('product/detail', compact('product'));
    }

    public function searchJson() {
        $keyword = $_GET['q'] ?? '';
        $categoryId = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null;
        $results = $this->productModel->quickSearch($keyword, $categoryId);
        header('Content-Type: application/json');
        echo json_encode(array_map(function($item){
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => number_format($item['price']) . ' đ',
                'image' => asset_url(!empty($item['image']) ? $item['image'] : 'public/assets/img/placeholder.svg')
            ];
        }, $results));
    }
}
