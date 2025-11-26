<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin - Noithat Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body { margin:0 !important; padding:0 !important; }
        * { box-sizing: border-box; }
    </style>
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
    $keys = ['users','orders','services','chats','complaints'];
    $seen = $_SESSION['admin_seen'] ?? [];
    $maxCreated = function($table) {
        try {
            $db = Database::connection();
            $ts = $db->query("SELECT COALESCE(MAX(created_at), NOW()) FROM {$table}")->fetchColumn();
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
            'services' => 'service_bookings',
            'chats' => 'chat_threads',
            'complaints' => 'complaints',
            default => 'users'
        }));
        $_SESSION['admin_seen'] = $seen;
    }
    foreach ($keys as $k) {
        if (!isset($seen[$k])) {
            $seen[$k] = strtotime($maxCreated(match ($k) {
                'users' => 'users',
                'orders' => 'orders',
                'services' => 'service_bookings',
                'chats' => 'chat_threads',
                'complaints' => 'complaints',
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
            $stmt = $db->prepare("SELECT COUNT(*) FROM {$table} WHERE created_at > ?");
            $stmt->execute([$ts]);
            return (int)$stmt->fetchColumn();
        } catch (\Throwable $e) { return 0; }
    };
    $sidebarCounts = [
        'users' => $getCount('users','users'),
        'orders' => $getCount('orders','orders'),
        'services' => $getCount('service_bookings','services'),
        'chats' => $getCount('chat_threads','chats'),
        'complaints' => $getCount('complaints','complaints'),
    ];
    $badge = function($num) {
        if (empty($num)) return '';
        $n = (int)$num;
        return '<span class="inline-flex min-w-[20px] h-5 px-1 items-center justify-center rounded-full bg-red-500 text-white text-[10px]">'.$n.'</span>';
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
                <span>Khách hàng</span><?php echo $badge($sidebarCounts['users'] ?? 0); ?>
            </a>
            <a class="px-4 py-3 hover:bg-slate-100" href="<?php echo $adminUrl('categories'); ?>">Danh mục</a>
            <a class="px-4 py-3 hover:bg-slate-100" href="<?php echo $adminUrl('products'); ?>">Sản phẩm</a>
            <a class="px-4 py-3 hover:bg-slate-100" href="<?php echo $adminUrl('sliders'); ?>">Slide banner</a>
            <a class="px-4 py-3 hover:bg-slate-100 flex items-center justify-between gap-2" href="<?php echo $adminUrl('orders'); ?>?seen=orders">
                <span>Đơn hàng</span><?php echo $badge($sidebarCounts['orders'] ?? 0); ?>
            </a>
            <a class="px-4 py-3 hover:bg-slate-100 flex items-center justify-between gap-2" href="<?php echo $adminUrl('services'); ?>?seen=services">
                <span>Dịch vụ</span><?php echo $badge($sidebarCounts['services'] ?? 0); ?>
            </a>
            <a class="px-4 py-3 hover:bg-slate-100" href="<?php echo $adminUrl('vouchers'); ?>">Mã giảm giá</a>
            <a class="px-4 py-3 hover:bg-slate-100 flex items-center justify-between gap-2" href="<?php echo $adminUrl('chats'); ?>?seen=chats">
                <span>Tư vấn khách</span><?php echo $badge($sidebarCounts['chats'] ?? 0); ?>
            </a>
            <a class="px-4 py-3 hover:bg-slate-100 flex items-center justify-between gap-2" href="<?php echo $adminUrl('complaints'); ?>?seen=complaints">
                <span>Khiếu nại khách</span><?php echo $badge($sidebarCounts['complaints'] ?? 0); ?>
            </a>
        </div>
    </aside>
    <main class="flex-1 ml-64 px-6 py-6">
        <?php echo $content ?? ''; ?>
    </main>
</div>
</body>
</html>
