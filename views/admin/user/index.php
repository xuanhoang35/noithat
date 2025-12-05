<?php ob_start(); ?>
<div class="bg-white rounded-2xl shadow-sm p-4">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-3">
        <div>
            <h1 class="text-xl font-semibold">Khách hàng</h1>
            <p class="text-slate-500 text-sm">Quản lý tài khoản</p>
        </div>
        <form method="get" action="<?php echo base_url('admin.php/users'); ?>" class="flex items-center gap-2 w-full md:w-auto md:min-w-[360px]">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>" placeholder="Tìm tên, email, số điện thoại..." class="flex-1 px-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring focus:ring-blue-200" />
            <?php if (!empty($querySeen)): ?>
                <input type="hidden" name="seen" value="<?php echo htmlspecialchars($querySeen); ?>">
            <?php endif; ?>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 flex items-center gap-1">
                <span>Tìm</span>
            </button>
        </form>
    </div>
    <div class="overflow-auto">
        <table class="min-w-full text-sm">
            <tr class="bg-slate-100 text-left">
                <th class="p-3">ID</th><th class="p-3">Tên</th><th class="p-3">Email</th><th class="p-3">Điện thoại</th><th class="p-3">Mật khẩu đã cấp</th><th class="p-3">Role</th><th class="p-3">Trạng thái</th><th class="p-3">Online</th><th class="p-3">Kích hoạt</th><th class="p-3">Xóa</th>
            </tr>
            <?php foreach ($users as $u): ?>
            <tr class="border-b hover:bg-slate-50">
                <td class="p-3"><?php echo $u['id']; ?></td>
                <td class="p-3"><?php echo $u['name']; ?></td>
                <td class="p-3"><?php echo $u['email']; ?></td>
                <td class="p-3"><?php echo $u['phone']; ?></td>
                <td class="p-3">
                    <?php
                        $resetInfo = $resetMap[$u['id']] ?? null;
                        $pw = $resetInfo['new_password_plain'] ?? '';
                    ?>
                    <?php if ($pw !== ''): ?>
                        <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold inline-flex items-center gap-2">
                            <span class="text-slate-600">PW:</span> <span class="font-semibold text-emerald-700"><?php echo htmlspecialchars($pw); ?></span>
                        </span>
                        <?php if (!empty($resetInfo['completed_at'])): ?>
                            <div class="text-[11px] text-slate-400 mt-1">Cấp: <?php echo $resetInfo['completed_at']; ?></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="text-xs text-slate-400">Chưa cấp</span>
                    <?php endif; ?>
                </td>
                <td class="p-3"><?php echo $u['role']; ?></td>
                <td class="p-3 align-middle">
                    <?php $active = (int)$u['is_active'] === 1; ?>
                    <span class="inline-flex items-center h-8 px-3 text-xs rounded-full <?php echo $active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700'; ?>">
                        <?php echo $active ? 'Đang mở' : 'Đang khóa'; ?>
                    </span>
                </td>
                <td class="p-3 align-middle">
                    <?php $online = (int)($u['is_online'] ?? 0) === 1; ?>
                    <span class="inline-flex items-center h-8 px-3 text-xs rounded-full <?php echo $online ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600'; ?>">
                        <?php echo $online ? 'Online' : 'Offline'; ?>
                    </span>
                </td>
                <td class="p-3 align-middle">
                    <form method="post" action="<?php echo base_url('admin.php/users/toggle/' . $u['id']); ?>">
                        <button class="h-8 px-3 mt-3 inline-flex items-center justify-center text-xs leading-none rounded bg-amber-50 text-amber-700 hover:bg-amber-100">
                            <?php echo $active ? 'Khóa' : 'Kích hoạt'; ?>
                        </button>
                    </form>
                </td>
                <td class="p-3 align-middle">
                    <?php if ($u['role'] !== 'admin'): ?>
                        <form method="post" action="<?php echo base_url('admin.php/users/delete/' . $u['id']); ?>" onsubmit="return confirm('Xóa khách hàng này?');">
                            <button class="h-8 px-3 mt-3 inline-flex items-center justify-center text-xs leading-none rounded bg-red-50 text-red-600 hover:bg-red-100">Xóa</button>
                        </form>
                    <?php else: ?>
                        <span class="text-xs text-slate-400">--</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="mt-6 pt-4 border-t border-slate-100">
        <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between mb-3">
            <div>
                <h2 class="text-lg font-semibold">Quản lý mật khẩu khách hàng</h2>
                <p class="text-slate-500 text-sm">Tiếp nhận yêu cầu quên mật khẩu và cấp mật khẩu mới.</p>
            </div>
        </div>
        <div class="overflow-auto">
            <table class="min-w-full text-sm">
                <tr class="bg-slate-100 text-left">
                    <th class="p-3">Khách hàng</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">SĐT</th>
                    <th class="p-3">Trạng thái</th>
                    <th class="p-3">Hành động</th>
                </tr>
                <?php if (!empty($resets)): ?>
                    <?php foreach ($resets as $reset): ?>
                        <tr class="border-b hover:bg-slate-50">
                            <td class="p-3"><?php echo htmlspecialchars($reset['name'] ?? 'Khách hàng'); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($reset['email']); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($reset['phone']); ?></td>
                            <td class="p-3">
                                <?php
                                    $status = $reset['status'];
                                    $badgeClass = [
                                        'pending' => 'bg-amber-50 text-amber-700',
                                        'completed' => 'bg-emerald-50 text-emerald-700',
                                        'delivered' => 'bg-slate-100 text-slate-600'
                                    ][$status] ?? 'bg-slate-100 text-slate-600';
                                    $label = [
                                        'pending' => 'Chờ xử lý',
                                        'completed' => 'Đã cấp mật khẩu',
                                        'delivered' => 'Khách đã nhận'
                                    ][$status] ?? ucfirst($status);
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo $badgeClass; ?>">
                                    <?php echo $label; ?>
                                </span>
                            </td>
                            <td class="p-3">
                                <?php if ($status === 'pending'): ?>
                                    <form method="post" action="<?php echo base_url('admin.php/users/reset-password/' . $reset['id']); ?>" class="flex flex-col md:flex-row gap-2">
                                        <input type="text" name="new_password" class="px-3 py-2 border rounded text-sm" placeholder="Nhập mật khẩu mới" required>
                                        <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Gửi mật khẩu</button>
                                    </form>
                                <?php elseif ($status === 'completed' || $status === 'delivered'): ?>
                                    <div class="text-sm">
                                        <p class="text-slate-500">Mật khẩu đã cấp:</p>
                                        <p class="text-lg font-semibold text-emerald-600"><?php echo htmlspecialchars($reset['new_password_plain']); ?></p>
                                        <?php if (!empty($reset['delivered_at'])): ?>
                                            <p class="text-xs text-slate-400 mt-1">Khách đã nhận: <?php echo $reset['delivered_at']; ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="p-4 text-center text-slate-500">Chưa có yêu cầu quên mật khẩu nào.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
