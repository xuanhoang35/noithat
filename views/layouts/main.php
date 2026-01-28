<script>
// Shield lỗi extension (chạy TRƯỚC mọi thứ)
window.addEventListener('error', function(e) {
    const src = e.filename || '';
    if (src.startsWith('chrome-extension://')) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }
}, true);

window.addEventListener('unhandledrejection', function(e) {
    const reason = (e.reason || '').toString();
    if (reason.includes('chrome-extension://') || reason === 'undefined') {
        e.preventDefault();
        return false;
    }
}, true);
</script>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <?php
        $appCfg = include __DIR__ . '/../../config/config.php';
        $appName = $appCfg['app_name'] ?? 'Nội Thất Store - Cửa hàng nội thất và thiết bị gia dụng';
        $brandShort = 'Nội Thất Store';
        $brandTagline = 'Cửa hàng nội thất và thiết bị gia dụng';
        $appDesc = 'Chào mừng quý khách đến với ' . $brandShort . ' — ' . $brandTagline . '. Chúc quý khách lựa chọn được sản phẩm ưng ý và nâng tầm không gian sống!';
        $appUrl = base_url('');
        $appImage = asset_url('public/bank/noithat_logo.png');
    ?>
    <title><?php echo htmlspecialchars($appName); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($appDesc); ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($brandShort); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($appName); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($appDesc); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($appUrl); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($appImage); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body { margin:0 !important; padding:0 !important; }
        * { box-sizing: border-box; }
        @keyframes scroll-text {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .animate-scroll-text {
            display:inline-block;
            white-space:nowrap;
            animation: scroll-text 14s linear infinite;
        }
        .ticker-dynamic {
            display:inline-block;
            white-space:nowrap;
        }
        .page-bg {
            position: relative;
        }
        .page-bg::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: url('<?php echo asset_url('public/bank/background_2026.png'); ?>');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center top;
            background-attachment: scroll;
            background-color: #f5f7fb;
            opacity: 1;
            pointer-events: none;
            z-index: 0;
        }
        .page-bg > * {
            position: relative;
            z-index: 1;
        }
    </style>
    <style>
        .chat-panel {
            opacity: 0;
            transform: translateY(12px) scale(0.96);
            transition: opacity 0.3s ease, transform 0.3s ease;
            pointer-events: none;
        }
        .chat-panel.is-active {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }
    </style>
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo asset_url('public/favicon.png'); ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo asset_url('public/favicon.png'); ?>">
    <link rel="apple-touch-icon" href="<?php echo asset_url('public/apple-touch-icon.png'); ?>">
    <link rel="shortcut icon" href="<?php echo asset_url('public/favicon.png'); ?>">
    <?php
        $cssPath = __DIR__ . '/../../public/assets/css/style.css';
        $cssVer = file_exists($cssPath) ? filemtime($cssPath) : time();
    ?>
    <link rel="stylesheet" href="<?php echo asset_url('public/assets/css/style.css') . '?v=' . $cssVer; ?>">
</head>
<!-- Shield chống lỗi extension (chrome-extension://...) -->
<body class="relative min-h-screen flex flex-col text-slate-900">
<?php
$chatHasUnread = false;
$orderHasUnread = false;
$cartCount = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += (int)($item['qty'] ?? 0);
    }
}
if (Auth::check()) {
    require_once __DIR__ . '/../../models/Chat.php';
    require_once __DIR__ . '/../../models/Order.php';
    $chatModelLayout = new Chat();
    $orderModelLayout = new Order();
    $chatHasUnread = $chatModelLayout->hasUnreadForUser(Auth::user()['id']);
    $orderHasUnread = $orderModelLayout->hasUnread(Auth::user()['id']);
}
$defaultAvatarPath = 'public/Profile/user-iconprofile.png';
$defaultAvatarUrl = asset_url($defaultAvatarPath);
$hideFooter = $hideFooter ?? false;
$hideHeader = $hideHeader ?? false;
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isActive = function ($path) use ($currentPath) {
    if ($path === '/') {
        return $currentPath === '/' ? 'bg-white/15 text-white font-semibold' : '';
    }
    return strpos($currentPath, $path) === 0 ? 'bg-white/15 text-white font-semibold' : '';
};
$sessionUser = Auth::check() ? Auth::user() : null;
// Thông báo kết quả yêu cầu cấp mật khẩu
if ($sessionUser && (($sessionUser['reset_status'] ?? '') === 'delivered')) {
    require_once __DIR__ . '/../../models/User.php';
    $userModelLayout = new User();
    if (($sessionUser['password_plain'] ?? '') === '__REJECTED__') {
        $_SESSION['flash_error'] = 'Yêu cầu cấp mật khẩu của bạn bị từ chối, vui lòng liên hệ quản trị viên để được hỗ trợ.';
        $userModelLayout->clearResetFlag((int)$sessionUser['id']);
        $sessionUser['reset_status'] = null;
        $sessionUser['password_plain'] = null;
    } elseif (!empty($sessionUser['password_plain'])) {
        $_SESSION['flash_success'] = 'Mật khẩu của quý khách là: ' . $sessionUser['password_plain'] . '. Vui lòng đăng nhập và đổi mật khẩu sau khi sử dụng.';
        $userModelLayout->clearResetMetaKeepPassword((int)$sessionUser['id']);
        $sessionUser['reset_status'] = null;
        $sessionUser['reset_token'] = null;
        $sessionUser['reset_password_plain'] = null;
    }
    $_SESSION['user'] = $sessionUser;
}
?>
<?php if (!$hideHeader): ?>
<header class="fixed top-0 left-0 right-0 z-30 text-white">
    <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-blue-900 to-blue-700"></div>
    <div class="absolute inset-0 opacity-40 bg-[radial-gradient(circle_at_20%_20%,rgba(59,130,246,0.5),transparent_25%),radial-gradient(circle_at_80%_0%,rgba(14,165,233,0.45),transparent_25%)]"></div>
    <div class="max-w-7xl mx-auto px-4 py-1.5 relative space-y-1.5">
        <div class="flex items-center text-xs text-white/80 gap-3">
            <div class="flex-1 min-h-[20px] overflow-hidden">
                <?php if (!empty($_SESSION['welcome_message'])): ?>
                    <div class="whitespace-nowrap ticker-dynamic text-white" data-ticker-cycle="<?php echo htmlspecialchars($_SESSION['welcome_message']); ?>|<?php echo htmlspecialchars($_SESSION['welcome_message']); ?>" data-ticker-speed="12">
                        <?php echo htmlspecialchars($_SESSION['welcome_message']); unset($_SESSION['welcome_message']); ?>
                    </div>
                <?php elseif (Auth::check()): ?>
                    <div class="whitespace-nowrap ticker-dynamic text-white" data-ticker-cycle="Khám phá Nội Thất Store – hơn 2.000 sản phẩm nội thất & thiết bị gia dụng cao cấp, thiết kế độc đáo, vật liệu bền bỉ, giao nhanh – lắp đặt tận nơi, tư vấn 24/7, đổi trả linh hoạt, bảo hành minh bạch và ưu đãi thành viên hấp dẫn để nâng tầm không gian sống của bạn.|Mua hàng đi, đọc gì mà đọc lắm thế!" data-ticker-speed="20|3">
                        Khám phá Nội Thất Store – hơn 2.000 sản phẩm nội thất & thiết bị gia dụng cao cấp, thiết kế độc đáo, vật liệu bền bỉ, giao nhanh – lắp đặt tận nơi, tư vấn 24/7, đổi trả linh hoạt, bảo hành minh bạch và ưu đãi thành viên hấp dẫn để nâng tầm không gian sống của bạn.
                    </div>
                <?php else: ?>
                    <div class="whitespace-nowrap ticker-dynamic text-white" data-ticker-cycle="Chào mừng quý khách đến với Nội Thất Store — Chúc quý khách lựa chọn được sản phẩm ưng ý và nâng tầm không gian sống!|Chào mừng quý khách đến với Nội Thất Store — Chúc quý khách lựa chọn được sản phẩm ưng ý và nâng tầm không gian sống!" data-ticker-speed="12">
                        Chào mừng quý khách đến với Nội Thất Store — Chúc quý khách lựa chọn được sản phẩm ưng ý và nâng tầm không gian sống!
                    </div>
                <?php endif; ?>
            </div>
            <div class="hidden md:flex items-center gap-3 flex-shrink-0 pl-2">
                <span class="px-3 py-1 rounded-full bg-white/10">Hotline: <strong>0974.734.668</strong></span>
                <span class="text-white/70">Hỗ trợ nhanh 24/7</span>
            </div>
        </div>
        <div class="rounded-2xl px-4 py-2.5 flex items-center gap-4 shadow-none bg-transparent border border-white/50 backdrop-blur-sm">
            <a href="<?php echo base_url(); ?>" class="flex items-center gap-3">
                <span class="inline-flex w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white border border-white/60 overflow-hidden shadow-sm">
                    <img src="<?php echo asset_url('public/bank/noithat1_logo.png'); ?>" alt="Nội Thất Store" class="w-full h-full object-contain">
                </span>
                <span class="flex flex-col leading-tight bg-clip-text text-transparent bg-gradient-to-r from-white via-blue-100 to-blue-300">
                    <span class="text-xl sm:text-2xl font-bold tracking-tight"><?php echo htmlspecialchars($brandShort); ?></span>
                    <span class="text-xs sm:text-sm font-medium text-white/90"><?php echo htmlspecialchars('Cửa hàng nội thất và thiết bị gia dụng'); ?></span>
                </span>
            </a>
            <nav class="flex-1 flex flex-wrap items-center gap-2 text-sm font-semibold">
                <a class="px-3 py-2 rounded-lg hover:bg-white/10 <?php echo $isActive('/') ?: ''; ?>" href="<?php echo base_url(); ?>">Trang chủ</a>
                <a class="px-3 py-2 rounded-lg hover:bg-white/10 <?php echo $isActive('/products'); ?>" href="<?php echo base_url('products'); ?>">Sản phẩm</a>
                <a class="px-3 py-2 rounded-lg hover:bg-white/10 <?php echo $isActive('/services'); ?>" href="<?php echo base_url('services'); ?>">Dịch vụ</a>
                <a class="px-3 py-2 rounded-lg hover:bg-white/10 <?php echo $isActive('/about'); ?>" href="<?php echo base_url('about'); ?>">Về chúng tôi</a>
                <a class="px-3 py-2 rounded-lg hover:bg-white/10 flex items-center gap-2 relative <?php echo $isActive('/cart'); ?>" href="<?php echo base_url('cart'); ?>">
                    <span class="inline-flex w-8 h-8 rounded-full bg-white/10 items-center justify-center text-sm font-semibold">GH</span>
                    <span class="hidden sm:inline">Giỏ hàng</span>
                    <?php if ($cartCount > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[11px] min-w-[22px] h-[22px] rounded-full flex items-center justify-center font-bold shadow"><?php echo $cartCount; ?></span>
                    <?php endif; ?>
                </a>
                <?php if (Auth::check()): ?>
                    <a class="px-3 py-2 rounded-lg hover:bg-white/10 relative inline-flex items-center gap-2 <?php echo $isActive('/orders'); ?>" href="<?php echo base_url('orders'); ?>">
                        <span>Đơn hàng</span>
                        <span data-order-indicator class="inline-flex w-2 h-2 rounded-full bg-red-400 <?php echo $orderHasUnread ? '' : 'hidden'; ?>"></span>
                    </a>
                    <?php if (Auth::isAdmin()): ?><a class="px-3 py-2 rounded-lg hover:bg-white/10" href="<?php echo base_url('admin.php'); ?>">Admin</a><?php endif; ?>
                <?php endif; ?>
            </nav>
            <div class="flex items-center gap-2">
            <?php if (Auth::check()): ?>
                <?php
                    $sessionUser = Auth::user();
                    $avatarSource = !empty($sessionUser['avatar']) ? $sessionUser['avatar'] : $defaultAvatarPath;
                    $avatar = asset_url($avatarSource);
                ?>
                <a class="px-3 py-2 text-sm bg-white/10 rounded-full hover:bg-white/20 flex items-center gap-2" href="<?php echo base_url('profile'); ?>">
                    <img src="<?php echo $avatar; ?>" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-white/30" onerror="this.src='<?php echo $defaultAvatarUrl; ?>';">
                    <span class="hidden sm:inline">Hi, <?php echo $sessionUser['name']; ?></span>
                </a>
                <a class="px-3 py-2 text-sm bg-white text-blue-700 rounded-full hover:bg-slate-100" href="<?php echo base_url('logout'); ?>">Đăng xuất</a>
            <?php else: ?>
                <a class="px-3 py-2 text-sm bg-white text-blue-700 rounded-full hover:bg-slate-100" href="<?php echo base_url('login'); ?>">Đăng nhập</a>
                <a class="px-3 py-2 text-sm border border-white text-white rounded-full hover:bg-white/10" href="<?php echo base_url('register'); ?>">Đăng ký</a>
            <?php endif; ?>
            </div>
        </div>
    </div>
</header>
<?php endif; ?>
<?php
$flashBag = [];
$flashStyles = [
    'flash_success' => 'bg-emerald-50 border border-emerald-200 text-emerald-800',
    'flash_error' => 'bg-red-50 border border-red-200 text-red-700',
    'flash_info' => 'bg-blue-50 border border-blue-200 text-blue-700'
];
foreach ($flashStyles as $flashKey => $class) {
    if (!empty($_SESSION[$flashKey])) {
        $flashBag[] = [
            'message' => $_SESSION[$flashKey],
            'class' => $class
        ];
        unset($_SESSION[$flashKey]);
    }
}
?>
<?php $mainTop = $hideHeader ? 'pt-6' : 'pt-[132px] md:pt-[124px]'; ?>
<main class="flex-1 <?php echo $mainTop; ?> pb-10 relative page-bg">
    <div class="absolute inset-x-0 top-0 h-64 tile-grid opacity-70 pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 relative z-10">
        <?php foreach ($flashBag as $flash): ?>
            <div data-flash-message class="mb-4 rounded-xl px-4 py-3 text-sm transition-opacity duration-500 <?php echo $flash['class']; ?>">
                <?php echo htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endforeach; ?>
        <?php echo $content ?? ''; ?>
    </div>
</main>
<?php if (!$hideFooter): ?>
<footer class="mt-auto bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-slate-100 relative overflow-hidden">
    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_20%_20%,rgba(59,130,246,0.35),transparent_30%),radial-gradient(circle_at_80%_10%,rgba(14,165,233,0.35),transparent_30%)]"></div>
    <div class="max-w-7xl mx-auto px-4 py-6 grid md:grid-cols-4 gap-6 text-sm relative z-10">
        <div class="space-y-2">
            <div class="text-lg font-semibold"><?php echo htmlspecialchars($brandShort); ?></div>
            <p class="text-slate-300"><?php echo htmlspecialchars('Nội Thất Store - Cửa hàng nội thất và thiết bị gia dụng.'); ?></p>
            <div class="space-y-1 text-slate-200">
                <div>Hotline: <strong>0974.734.668</strong></div>
                <div>Email: <strong>huyendothi.79@gmail.com</strong></div>
                <div>Showroom: Phương Canh, Nam Từ Liêm, Hà Nội</div>
                <div>Giờ mở cửa: 8:00 - 22:00 (T2 - CN)</div>
            </div>
        </div>
        <div class="space-y-2">
            <div class="font-semibold text-slate-50">Liên kết nhanh</div>
            <div class="flex flex-col gap-1 text-slate-200">
                <a class="hover:text-white" href="<?php echo base_url('products'); ?>">Sản phẩm</a>
                <a class="hover:text-white" href="<?php echo base_url('services'); ?>">Dịch vụ</a>
                <a class="hover:text-white" href="<?php echo base_url('about'); ?>">Về chúng tôi</a>
                <a class="hover:text-white flex items-center gap-1" href="<?php echo base_url('cart'); ?>"><i class="fas fa-shopping-cart"></i><span class="hidden sm:inline">Giỏ hàng</span></a>
                <a class="hover:text-white" href="<?php echo base_url('orders'); ?>">Đơn hàng của tôi</a>
            </div>
        </div>
        <div class="space-y-2">
            <div class="font-semibold text-slate-50">Chính sách</div>
            <div class="flex flex-col gap-1 text-slate-200">
                <span>Giao hàng & lắp đặt</span>
                <span>Bảo hành & đổi trả</span>
                <span>Bảo mật thông tin</span>
                <span>FAQ & hỗ trợ</span>
            </div>
            <div class="mt-2">
                <div class="font-semibold text-slate-50 mb-1">Thanh toán</div>
                <div class="flex gap-2 text-xs text-slate-200">
                    <span class="px-2 py-1 rounded bg-slate-700">Visa/Master</span>
                    <span class="px-2 py-1 rounded bg-slate-700">VNPay</span>
                    <span class="px-2 py-1 rounded bg-slate-700">MoMo</span>
                    <span class="px-2 py-1 rounded bg-slate-700">COD</span>
                </div>
            </div>
            <div class="mt-2">
                <div class="font-semibold text-slate-50 mb-1">Vận chuyển</div>
                <div class="flex gap-2 text-xs text-slate-200">
                    <span class="px-2 py-1 rounded bg-slate-700">Giao nhanh</span>
                    <span class="px-2 py-1 rounded bg-slate-700">Bảo hiểm</span>
                    <span class="px-2 py-1 rounded bg-slate-700">Lắp đặt</span>
                </div>
            </div>
        </div>
        <div class="space-y-2">
            <div class="font-semibold text-slate-50">Kết nối</div>
            <div class="flex gap-2 text-xs text-slate-200">
                <a href="https://www.facebook.com/DoHuyen2003/" target="_blank" rel="noopener noreferrer" class="px-3 py-2 rounded bg-blue-600 hover:bg-blue-500">Facebook</a>
                <a href="https://zalo.me/0974734668" target="_blank" rel="noopener noreferrer" class="px-3 py-2 rounded bg-emerald-600 hover:bg-emerald-500">Zalo</a>
                <a href="https://www.instagram.com/hnxams/" target="_blank" rel="noopener noreferrer" class="px-3 py-2 rounded bg-pink-600 hover:bg-pink-500">Instagram</a>
            </div>
            <div class="mt-2">
                <div class="font-semibold text-slate-50 mb-1 text-sm">Đăng ký nhận tin</div>
                <form class="flex gap-2">
                    <input class="flex-1 px-3 py-2 rounded bg-slate-800 border border-slate-700 text-slate-100 placeholder-slate-500" placeholder="Email của bạn">
                    <button class="px-3 py-2 rounded bg-blue-600 hover:bg-blue-500 text-white text-sm">Gửi</button>
                </form>
            </div>
        </div>
    </div>
    <div class="border-t border-slate-800 relative z-10">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between text-xs text-slate-300">
            <span>© 2025 <?php echo htmlspecialchars($appName); ?>. All rights reserved.</span>
            <span>Thiết kế & vận hành bởi <?php echo htmlspecialchars($brandShort); ?> Team</span>
        </div>
    </div>
</footer>
<?php endif; ?>
<?php if (Auth::check()): ?>
<div class="fixed right-5 bottom-5 z-40 flex flex-col items-end gap-3">
    <div id="chat-panel" data-chat-panel class="hidden chat-panel w-[360px]" aria-hidden="true">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 bg-blue-600 text-white flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest text-blue-100">Chat hỗ trợ</p>
                    <p class="text-sm font-semibold"><?php echo htmlspecialchars($brandShort); ?></p>
                </div>
                <button type="button" data-chat-close class="text-white/80 hover:text-white text-xl leading-none">&times;</button>
            </div>
            <iframe data-chat-frame class="w-full h-[520px] border-0" title="Chat hỗ trợ"></iframe>
        </div>
    </div>
    <button type="button" data-chat-toggle class="relative shadow-lg rounded-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 flex items-center gap-2">
        <span class="inline-block w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
        <span class="text-sm font-semibold">Chat hỗ trợ</span>
        <span data-chat-indicator class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-red-600 shadow <?php echo $chatHasUnread ? '' : 'hidden'; ?>"></span>
    </button>
</div>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const flashes = document.querySelectorAll('[data-flash-message]');
    const isLoggedIn = <?php echo Auth::check() ? 'true' : 'false'; ?>;
    if (flashes.length) {
        setTimeout(() => {
            flashes.forEach(el => {
                el.classList.add('opacity-0');
                setTimeout(() => el.remove(), 600);
            });
        }, 2000);
    }
    const chatToggle = document.querySelector('[data-chat-toggle]');
    const chatPanel = document.querySelector('[data-chat-panel]');
    const chatFrame = document.querySelector('[data-chat-frame]');
    const chatClose = document.querySelector('[data-chat-close]');
    const chatIndicator = document.querySelector('[data-chat-indicator]');
    const orderIndicator = document.querySelector('[data-order-indicator]');
    let chatOpen = false;
    const openChat = () => {
        if (!chatPanel) return;
        chatPanel.classList.remove('hidden');
        requestAnimationFrame(() => chatPanel.classList.add('is-active'));
        chatPanel.setAttribute('aria-hidden','false');
        if (chatFrame && !chatFrame.getAttribute('src')) {
            chatFrame.setAttribute('src', '<?php echo base_url('chat?embed=1'); ?>');
        }
        if (chatIndicator) {
            chatIndicator.classList.add('hidden');
        }
        chatOpen = true;
    };
    const closeChat = () => {
        if (!chatPanel || !chatOpen) return;
        chatPanel.classList.remove('is-active');
        chatPanel.addEventListener('transitionend', () => {
            chatPanel.classList.add('hidden');
            chatPanel.setAttribute('aria-hidden','true');
        }, { once: true });
        chatOpen = false;
    };
    const toggleChat = () => chatOpen ? closeChat() : openChat();
    if (chatToggle) chatToggle.addEventListener('click', toggleChat);
    if (chatClose) chatClose.addEventListener('click', closeChat);

    document.querySelectorAll('[data-ticker-cycle]').forEach(el => {
        const container = el.parentElement;
        const messages = (el.getAttribute('data-ticker-cycle') || '').split('|').map(s => s.trim()).filter(Boolean);
        if (messages.length <= 1 || !container) return;
        const speedsRaw = (el.getAttribute('data-ticker-speed') || '').split('|').map(s => parseFloat(s)).filter(n => !isNaN(n));
        const getSpeed = idx => (speedsRaw[idx] || speedsRaw[0] || 12) * 1000;
        const gap = 12;
        let idx = 0;
        let current;
        const play = () => {
            const msg = messages[idx];
            el.textContent = msg;
            if (current) current.cancel();
            requestAnimationFrame(() => {
                const start = container.offsetWidth + gap;
                const end = -el.scrollWidth - gap;
                el.style.transform = `translateX(${start}px)`;
                el.style.willChange = 'transform';
                // force reflow để tránh giật khung khi đổi text
                void el.offsetWidth;
                let speed = getSpeed(idx);
                current = el.animate([
                    { transform: `translateX(${start}px)` },
                    { transform: `translateX(${end}px)` }
                ], {
                    duration: speed,
                    easing: 'linear',
                    fill: 'forwards'
                });
                current.onfinish = () => {
                    idx = (idx + 1) % messages.length;
                    requestAnimationFrame(() => play());
                };
            });
        };
        play();
    });

    // Poll thông báo user (đơn hàng, chat) mỗi 1s
    let accountLockedRedirected = false;
    const pollNotify = () => {
        if (!isLoggedIn) return;
        fetch('<?php echo base_url('notify/poll'); ?>', { cache: 'no-store' })
            .then(r => r.json())
            .then(data => {
                if (data.account_locked && !accountLockedRedirected) {
                    accountLockedRedirected = true;
                    const msg = 'Hết phiên đăng nhập, vui lòng đăng nhập lại để tiếp tục.';
                    let banner = document.querySelector('[data-lockout-banner]');
                    if (!banner) {
                        banner = document.createElement('div');
                        banner.setAttribute('data-lockout-banner','1');
                        banner.className = 'fixed left-1/2 -translate-x-1/2 top-4 z-[9999] max-w-xl w-[90%] bg-red-600 text-white px-4 py-3 rounded-xl shadow-lg flex flex-col gap-1 text-sm';
                        document.body.appendChild(banner);
                    }
                    banner.innerHTML = '<div class="font-semibold">Thông báo</div><div>'+msg+'</div><div data-lockout-countdown class="text-red-100 text-xs">Đăng xuất sau 3s...</div>';
                    let countdown = 3;
                    const tick = () => {
                        const el = banner.querySelector('[data-lockout-countdown]');
                        if (el) el.textContent = 'Đăng xuất sau ' + countdown + 's...';
                        countdown -= 1;
                        if (countdown < 0) {
                            fetch('<?php echo base_url('logout'); ?>', { credentials: 'same-origin' }).finally(() => {
                                window.location.href = '<?php echo base_url('/'); ?>';
                            });
                        } else {
                            setTimeout(tick, 1000);
                        }
                    };
                    setTimeout(tick, 0);
                    return;
                }
                if (orderIndicator) {
                    if (data.orders_unread) orderIndicator.classList.remove('hidden');
                    else orderIndicator.classList.add('hidden');
                }
                if (chatIndicator) {
                    if (data.chat_unread) chatIndicator.classList.remove('hidden');
                    else chatIndicator.classList.add('hidden');
                }
            })
            .catch(() => {});
    };
    pollNotify();
    if (isLoggedIn) {
        setInterval(pollNotify, 1000);
    }
});
</script>
</body>
</html>
