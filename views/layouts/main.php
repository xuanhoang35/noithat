<?php
    // Layout chính cho trang người dùng
    $config = @include __DIR__ . '/../../config/config.php';
    $brandName = $config['app_name'] ?? 'Noithat Store';
    $logo = asset_url('public/bank/noithat_logo.png');
    $pageTitle = $pageTitle ?? ($title ?? $brandName);
    $pageDescription = $pageDescription ?? 'Chào mừng quý khách đến với Nội Thất Store, lựa chọn sản phẩm ưng ý và nâng tầm không gian sống.';
    $cartCount = 0;
    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cartCount += (int)($item['qty'] ?? 0);
        }
    }
    $user = class_exists('Auth') ? Auth::user() : ($_SESSION['user'] ?? null);
    $isLoggedIn = class_exists('Auth') ? Auth::check() : !empty($user);
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $canonical = $scheme . '://' . $host . $path;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <link rel="icon" type="image/png" href="<?php echo $logo; ?>">
    <link rel="apple-touch-icon" href="<?php echo $logo; ?>">
    <link rel="shortcut icon" href="<?php echo $logo; ?>">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($brandName); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:image" content="<?php echo $logo; ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta name="twitter:image" content="<?php echo $logo; ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo asset_url('public/assets/css/style.css'); ?>">
</head>
<body class="min-h-screen bg-transparent text-slate-900">
    <div class="relative min-h-screen flex flex-col">
        <header class="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-slate-200">
            <div class="max-w-6xl mx-auto px-4 py-3 flex items-center gap-3">
                <a href="<?php echo base_url('/'); ?>" class="flex items-center gap-3">
                    <img src="<?php echo $logo; ?>" alt="<?php echo htmlspecialchars($brandName); ?> logo" class="h-11 w-11 rounded-full object-cover shadow-md" onerror="this.style.display='none'">
                    <div class="leading-tight">
                        <div class="text-[11px] uppercase tracking-[0.24em] text-blue-600 font-semibold">Noithat Store</div>
                        <div class="text-base font-semibold text-slate-900">Nâng tầm không gian sống</div>
                    </div>
                </a>
                <nav class="ml-auto hidden md:flex items-center gap-4 text-sm text-slate-700 font-medium">
                    <a href="<?php echo base_url('/'); ?>" class="hover:text-blue-600">Trang chủ</a>
                    <a href="<?php echo base_url('products'); ?>" class="hover:text-blue-600">Sản phẩm</a>
                    <a href="<?php echo base_url('services'); ?>" class="hover:text-blue-600">Dịch vụ</a>
                    <a href="<?php echo base_url('about'); ?>" class="hover:text-blue-600">Về chúng tôi</a>
                    <a href="<?php echo base_url('cart'); ?>" class="relative inline-flex items-center gap-1 hover:text-blue-600">
                        <span>Giỏ hàng</span>
                        <?php if ($cartCount > 0): ?>
                            <span class="px-2 py-0.5 rounded-full bg-blue-600 text-white text-[11px] leading-none"><?php echo $cartCount; ?></span>
                        <?php endif; ?>
                    </a>
                </nav>
                <div class="ml-3 flex items-center gap-2 text-sm">
                    <?php if ($isLoggedIn): ?>
                        <span class="hidden sm:inline text-slate-600">Hi, <?php echo htmlspecialchars($user['name'] ?? ''); ?></span>
                        <a href="<?php echo base_url('profile'); ?>" class="px-3 py-1.5 rounded-full border border-slate-200 hover:border-blue-500 text-slate-700">Tài khoản</a>
                        <a href="<?php echo base_url('logout'); ?>" class="px-3 py-1.5 rounded-full bg-blue-600 text-white hover:bg-blue-700">Đăng xuất</a>
                    <?php else: ?>
                        <a href="<?php echo base_url('login'); ?>" class="px-3 py-1.5 rounded-full border border-slate-200 hover:border-blue-500 text-slate-700">Đăng nhập</a>
                        <a href="<?php echo base_url('register'); ?>" class="px-3 py-1.5 rounded-full bg-blue-600 text-white hover:bg-blue-700">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
            <nav class="md:hidden max-w-6xl mx-auto px-4 pb-3 flex flex-wrap gap-3 text-sm text-slate-700 font-medium">
                <a href="<?php echo base_url('/'); ?>" class="hover:text-blue-600">Trang chủ</a>
                <a href="<?php echo base_url('products'); ?>" class="hover:text-blue-600">Sản phẩm</a>
                <a href="<?php echo base_url('services'); ?>" class="hover:text-blue-600">Dịch vụ</a>
                <a href="<?php echo base_url('about'); ?>" class="hover:text-blue-600">Về chúng tôi</a>
                <a href="<?php echo base_url('cart'); ?>" class="hover:text-blue-600">Giỏ hàng<?php echo $cartCount > 0 ? ' (' . $cartCount . ')' : ''; ?></a>
            </nav>
        </header>
        <main class="flex-1 max-w-6xl mx-auto w-full px-4 py-6">
            <?php echo $content ?? ''; ?>
        </main>
        <footer class="border-t border-slate-200 bg-white/80 text-sm text-slate-600">
            <div class="max-w-6xl mx-auto px-4 py-4 flex flex-wrap gap-3 justify-between items-center">
                <div>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($brandName); ?>.</div>
                <div class="flex gap-3">
                    <a href="tel:0974734668" class="text-blue-700 hover:underline">Hotline: 0974.734.668</a>
                    <a href="mailto:huyendothi.79@gmail.com" class="text-blue-700 hover:underline">Email: huyendothi.79@gmail.com</a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
