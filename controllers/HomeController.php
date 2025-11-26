<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Slider.php';

class HomeController extends Controller {
    public function index() {
        $productModel = new Product();
        $categoryModel = new Category();
        $sliderModel = new Slider();
        $featured = $productModel->featured();
        $categories = $categoryModel->all();
        $sliderImages = array_map(function($row) {
            return $row['image'];
        }, $sliderModel->all(true));

        // Fallback nếu chưa có slider được cấu hình
        if (empty($sliderImages)) {
            $config = include __DIR__ . '/../config/config.php';
            $baseUrl = rtrim($config['base_url'] ?? '', '/');
            $rootDir = realpath(__DIR__ . '/..');
            $imageDir = $rootDir ? $rootDir . '/public/assets/img' : null;
            if ($imageDir && is_dir($imageDir)) {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($imageDir, FilesystemIterator::SKIP_DOTS));
                foreach ($iterator as $file) {
                    if ($file->isFile() && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file->getFilename())) {
                        $relative = str_replace($rootDir, '', $file->getPathname());
                        $relative = str_replace('\\', '/', $relative);
                        $sliderImages[] = $baseUrl . $relative;
                    }
                }
            }
            if (empty($sliderImages)) {
                $sliderImages = [
                    'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1200&q=60',
                ];
            }
        }

        $this->view('home/index', compact('featured', 'categories', 'sliderImages'));
    }
}
