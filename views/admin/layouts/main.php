<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin - Nội Thất Store - Cửa hàng nội thất và thiết bị gia dụng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body { margin:0 !important; padding:0 !important; }
        * { box-sizing: border-box; }
    </style>
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo asset_url('public/favicon.png'); ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo asset_url('public/favicon.png'); ?>">
    <link rel="apple-touch-icon" href="<?php echo asset_url('public/apple-touch-icon.png'); ?>">
    <link rel="shortcut icon" href="<?php echo asset_url('public/favicon.png'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('public/assets/css/style.css'); ?>">
    <script>
        (function () {
            function suppressCFError(event) {
                const source = (event.filename || event?.target?.src || '').toString();
                if (source.includes('/cdn-cgi/rum/onboarding.js')) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    return false;
                }
            }
            window.addEventListener('error', suppressCFError, true);
            window.addEventListener('unhandledrejection', function (event) {
                const reason = (event.reason || '').toString();
                if (reason.toLowerCase().includes('onboarding') || reason === 'undefined') {
                    event.preventDefault();
                    return false;
                }
            }, true);
        })();
    </script>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col">
<?php
    // Badge chỉ xuất hiện khi có bản ghi mới hơn mốc đã xem; khi bấm mục đó reset mốc = max(created_at).
    if (!session_id()) session_start();
    $keys = ['users','orders','services','chats','complaints','resets'];
    $seen = $_SESSION['admin_seen'] ?? [];
    $maxCreated = function($table) {
        try {
            $db = Database::connection();
            if ($table === 'services') {
                $ts = $db->query("SELECT COALESCE(MAX(created_at), NOW()) FROM services WHERE is_booking = 1")->fetchColumn();
            } elseif ($table === 'chat_messages') {
                $ts = $db->query("SELECT COALESCE(MAX(created_at), NOW()) FROM chat_messages")->fetchColumn();
            } elseif ($table === 'resets') {
                $ts = $db->query("SELECT COALESCE(MAX(reset_requested_at), NOW()) FROM users WHERE reset_token IS NOT NULL")->fetchColumn();
            } else {
                $ts = $db->query("SELECT COALESCE(MAX(created_at), NOW()) FROM {$table}")->fetchColumn();
            }
            return $ts ?: date('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return date('Y-m-d H:i:s');
        }
    };
    if (isset($_GET['seen']) && in_array($_GET['seen'], $keys)) {
        $k = $_GET['seen'];
        $seen[$k] = strtotime($maxCreated(match ($k) {
            'users' => 'users',
            'orders' => 'orders',
            'services' => 'services',
            'chats' => 'chat_messages',
            'complaints' => 'complaints',
            default => 'users'
        }));
        // Khi xem Khách hàng thì coi như đã xem yêu cầu mật khẩu
        if ($k === 'users') {
            $seen['resets'] = strtotime($maxCreated('resets'));
        }
        $_SESSION['admin_seen'] = $seen;
    }
    foreach ($keys as $k) {
        if (!isset($seen[$k])) {
            $seen[$k] = strtotime($maxCreated(match ($k) {
                'users' => 'users',
                'orders' => 'orders',
                'services' => 'services',
                'chats' => 'chat_messages',
                'complaints' => 'complaints',
                'resets' => 'resets',
                default => 'users'
            }));
        }
    }
    $_SESSION['admin_seen'] = $seen;
    $getCount = function($table, $seenKey) use ($seen) {
        try {
            $db = Database::connection();
            $last = $seen[$seenKey] ?? time();
            $ts = date('Y-m-d H:i:s', $last);
            if ($table === 'services') {
                $stmt = $db->prepare("SELECT COUNT(*) FROM services WHERE is_booking = 1 AND created_at > ?");
                $stmt->execute([$ts]);
                return (int)$stmt->fetchColumn();
            }
            if ($table === 'chat_messages') {
                $stmt = $db->prepare("SELECT COUNT(*) FROM chat_messages WHERE created_at > ?");
                $stmt->execute([$ts]);
                return (int)$stmt->fetchColumn();
            }
            if ($table === 'resets') {
                $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE reset_token IS NOT NULL AND reset_requested_at > ?");
                $stmt->execute([$ts]);
                return (int)$stmt->fetchColumn();
            }
            $stmt = $db->prepare("SELECT COUNT(*) FROM {$table} WHERE created_at > ?");
            $stmt->execute([$ts]);
            return (int)$stmt->fetchColumn();
        } catch (\Throwable $e) { return 0; }
    };
    $sidebarCounts = [
        // Gộp thêm yêu cầu mật khẩu vào Khách hàng
        'users' => $getCount('users','users') + $getCount('resets','resets'),
        'orders' => $getCount('orders','orders'),
        'services' => $getCount('services','services'),
        'chats' => $getCount('chat_messages','chats'),
        'complaints' => $getCount('complaints','complaints'),
        'resets' => $getCount('resets','resets'),
    ];
    $badge = function($num, $key) {
        $n = (int)$num;
        $classes = 'inline-flex min-w-[20px] h-5 px-1 items-center justify-center rounded-full bg-red-500 text-white text-[10px]';
        $hidden = $n > 0 ? '' : ' style="display:none"';
        return '<span data-badge="'.$key.'" class="'.$classes.'"'.$hidden.'>'.$n.'</span>';
    };
    $adminBase = base_url('admin.php');
    $adminUrl = function(string $path = '') use ($adminBase) {
        $base = rtrim($adminBase, '/');
        if ($path === '' || $path === '/') {
            return $base;
        }
        return $base . '/' . ltrim($path, '/');
    };
?>
<nav class="fixed top-0 left-0 right-0 z-30 bg-slate-900 text-white shadow">
    <div class="max-w-6xl mx-auto px-4 py-2 flex items-center justify-between">
        <a class="text-lg font-semibold tracking-tight" href="<?php echo $adminUrl(); ?>">Admin</a>
        <a class="text-sm px-3 py-2 bg-white/15 rounded hover:bg-white/25" href="<?php echo base_url('logout'); ?>">Đăng xuất</a>
    </div>
</nav>
<div class="flex pt-16">
    <aside class="fixed left-0 top-12 w-64 h-[calc(100vh-3rem)] bg-white shadow-sm border-r overflow-y-auto">
        <div class="px-4 py-3 text-xs uppercase text-slate-500 border-b">Quản trị</div>
        <div class="flex flex-col">
            <a class="px-4 py-3 hover:bg-slate-100" href="<?php echo $adminUrl(); ?>">Dashboard</a>
            <a class="px-4 py-3 hover:bg-slate-100 flex items-center justify-between gap-2" href="<?php echo $adminUrl('users'); ?>?seen=users">
                <span>Khách hàng</span><?php echo $badge($sidebarCounts['users'] ?? 0, 'users'); ?>
            </a>
            <a class="px-4 py-3 hover:bg-slate-100" href="<?php echo $adminUrl('categories'); ?>">Danh mục</a>
            <a class="px-4 py-3 hover:bg-slate-100" href="<?php echo $adminUrl('products'); ?>">Sản phẩm</a>
            <a class="px-4 py-3 hover:bg-slate-100" href="<?php echo $adminUrl('sliders'); ?>">Slide banner</a>
            <a class="px-4 py-3 hover:bg-slate-100 flex items-center justify-between gap-2" href="<?php echo $adminUrl('orders'); ?>?seen=orders">
                <span>Đơn hàng</span><?php echo $badge($sidebarCounts['orders'] ?? 0, 'orders'); ?>
            </a>
            <a class="px-4 py-3 hover:bg-slate-100 flex items-center justify-between gap-2" href="<?php echo $adminUrl('services'); ?>?seen=services">
                <span>Dịch vụ</span><?php echo $badge($sidebarCounts['services'] ?? 0, 'services'); ?>
            </a>
            <a class="px-4 py-3 hover:bg-slate-100" href="<?php echo $adminUrl('vouchers'); ?>">Mã giảm giá</a>
            <a class="px-4 py-3 hover:bg-slate-100 flex items-center justify-between gap-2" href="<?php echo $adminUrl('chats'); ?>?seen=chats">
                <span>Tư vấn khách</span><?php echo $badge($sidebarCounts['chats'] ?? 0, 'chats'); ?>
            </a>
            <a class="px-4 py-3 hover:bg-slate-100 flex items-center justify-between gap-2" href="<?php echo $adminUrl('complaints'); ?>?seen=complaints">
                <span>Khiếu nại khách</span><?php echo $badge($sidebarCounts['complaints'] ?? 0, 'complaints'); ?>
            </a>
            <a class="px-4 py-3 hover:bg-slate-100" href="<?php echo $adminUrl('maintenance'); ?>">Bảo trì web</a>
        </div>
    </aside>
    <main class="flex-1 ml-64 px-6 py-6">
        <?php echo $content ?? ''; ?>
    </main>
</div>
<script>
// Realtime badge cho yêu cầu mật khẩu (gộp vào Khách hàng)
(function(){
    const badgeUsers = document.querySelector('[data-badge="users"]');
    if (!badgeUsers) return;
    const pollResets = () => {
        fetch('<?php echo base_url('admin.php/users/resets'); ?>', { cache: 'no-store', credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                const count = Array.isArray(data) ? data.length : 0;
                if (count > 0) {
                    badgeUsers.textContent = count;
                    badgeUsers.style.display = '';
                } else {
                    badgeUsers.style.display = 'none';
                }
            })
            .catch(() => {});
    };
    pollResets();
    setInterval(pollResets, 5000);
})();
</script>
<script>
(function(){
    const badges = {
        users: document.querySelector('[data-badge="users"]'),
        orders: document.querySelector('[data-badge="orders"]'),
        services: document.querySelector('[data-badge="services"]'),
        chats: document.querySelector('[data-badge="chats"]'),
        complaints: document.querySelector('[data-badge="complaints"]')
    };
    let lastLatest = {};
    let lastCount = {};
    const currentPath = window.location.pathname + window.location.search;
    const showToast = (msg) => {
        let toast = document.querySelector('[data-admin-toast]');
        if (!toast) {
            toast = document.createElement('div');
            toast.setAttribute('data-admin-toast','1');
            toast.className = 'fixed right-4 bottom-4 z-50 bg-slate-900 text-white px-4 py-3 rounded-lg shadow-lg text-sm';
            document.body.appendChild(toast);
        }
        toast.textContent = msg;
        toast.style.opacity = '1';
        setTimeout(() => { toast.style.opacity = '0'; }, 2000);
    };
    const hotReloadMain = () => {
        const main = document.querySelector('main');
        if (!main) return;
        fetch(window.location.href, { cache: 'no-store' })
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newMain = doc.querySelector('main');
                if (newMain) {
                    main.innerHTML = newMain.innerHTML;
                } else {
                    location.reload();
                }
            })
            .catch(() => { location.reload(); });
    };
    const poll = () => {
        fetch('<?php echo $adminUrl('notify/poll'); ?>', { cache: 'no-store' })
            .then(r => r.json())
            .then(data => {
                ['orders','complaints','chats','services','users'].forEach(key => {
                    const info = data[key];
                    const badge = badges[key];
                    if (!info || !badge) return;
                    const count = parseInt(info.count, 10) || 0;
                    badge.textContent = count;
                    badge.style.display = count > 0 ? 'inline-flex' : 'none';
                    // Không nháy badge nữa, chỉ cập nhật số
                    lastLatest[key] = info.latest || lastLatest[key];
                    if (typeof lastCount[key] !== 'undefined' && count > lastCount[key]) {
                        showToast(`Có cập nhật mới: ${key}`);
                        if ((key === 'orders' && currentPath.includes('/admin.php/orders')) ||
                            (key === 'chats' && currentPath.includes('/admin.php/chats')) ||
                            (key === 'complaints' && currentPath.includes('/admin.php/complaints')) ||
                            (key === 'services' && currentPath.includes('/admin.php/services'))) {
                            hotReloadMain();
                        }
                    }
                    lastCount[key] = count;
                });
            })
            .catch(() => {});
    };
    poll(); // chạy ngay lần đầu
    setInterval(poll, 1000);
})();
</script>
</body>
</html>
