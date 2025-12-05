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
                <th class="p-3">ID</th><th class="p-3">Tên</th><th class="p-3">Email</th><th class="p-3">Điện thoại</th><th class="p-3">Mật khẩu</th><th class="p-3">Role</th><th class="p-3">Trạng thái</th><th class="p-3">Online</th><th class="p-3">Kích hoạt</th><th class="p-3">Xóa</th>
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
                        $pw = $u['password_plain'] ?? '';
                        $timeIssued = '';
                        if (!$pw && $resetInfo) {
                            $pw = $resetInfo['new_password_plain'] ?? '';
                            $timeIssued = $resetInfo['completed_at'] ?? '';
                        }
                        if ($pw && !$timeIssued && $resetInfo) {
                            $timeIssued = $resetInfo['completed_at'] ?? '';
                        }
                    ?>
                    <?php if ($pw !== ''): ?>
                        <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold inline-flex items-center gap-2">
                            <span class="text-slate-600">PW:</span> <span class="font-semibold text-emerald-700"><?php echo htmlspecialchars($pw); ?></span>
                        </span>
                        <?php if (!empty($timeIssued)): ?>
                            <div class="text-[11px] text-slate-400 mt-1">Cấp: <?php echo $timeIssued; ?></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="text-xs text-slate-400">Chưa cấp</span>
                    <?php endif; ?>
                </td>
                <td class="p-3"><?php echo $u['role']; ?></td>
                <td class="p-3 align-middle">
                    <?php $active = (int)$u['is_active'] === 1; ?>
                    <span data-active="<?php echo $u['id']; ?>" class="inline-flex items-center h-8 px-3 text-xs rounded-full <?php echo $active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700'; ?>">
                        <?php echo $active ? 'Đang mở' : 'Đang khóa'; ?>
                    </span>
                </td>
                <td class="p-3 align-middle">
                    <?php $online = (int)($u['is_online'] ?? 0) === 1; ?>
                    <span data-online="<?php echo $u['id']; ?>" class="inline-flex items-center h-8 px-3 text-xs rounded-full <?php echo $online ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600'; ?>">
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
                        <div class="flex gap-2">
                            <button type="button" class="h-8 px-3 mt-3 inline-flex items-center justify-center text-xs leading-none rounded bg-slate-100 text-slate-700 hover:bg-slate-200" onclick="alert('Chức năng chỉnh sửa sẽ sớm được bổ sung.');">Sửa</button>
                            <form method="post" action="<?php echo base_url('admin.php/users/delete/' . $u['id']); ?>" onsubmit="return confirm('Xóa khách hàng này?');">
                                <button class="h-8 px-3 mt-3 inline-flex items-center justify-center text-xs leading-none rounded bg-red-50 text-red-600 hover:bg-red-100">Xóa</button>
                            </form>
                        </div>
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
                <tbody id="reset-body">
                <?php if (!empty($resets)): ?>
                    <?php foreach ($resets as $reset): if(($reset['status'] ?? '')!=='pending') continue; ?>
                        <tr class="border-b hover:bg-slate-50">
                            <td class="p-3"><?php echo htmlspecialchars($reset['name'] ?? 'Khách hàng'); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($reset['email']); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($reset['phone']); ?></td>
                            <td class="p-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">Chờ xử lý</span>
                            </td>
                            <td class="p-3">
                                <form method="post" action="<?php echo base_url('admin.php/users/reset-password/' . $reset['id']); ?>" class="flex flex-col md:flex-row gap-2">
                                    <input type="text" name="new_password" class="px-3 py-2 border rounded text-sm" placeholder="Nhập mật khẩu mới" required>
                                    <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Gửi mật khẩu</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="p-4 text-center text-slate-500">Chưa có yêu cầu quên mật khẩu nào.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const statusUrl = '<?php echo base_url('admin.php/users/status'); ?>';
    const resetUrl = '<?php echo base_url('admin.php/users/resets'); ?>';
    const applyStatus = (users) => {
        users.forEach(u => {
            const onlineEl = document.querySelector('[data-online="'+u.id+'"]');
            const activeEl = document.querySelector('[data-active="'+u.id+'"]');
            if (onlineEl) {
                const online = parseInt(u.is_online, 10) === 1;
                onlineEl.textContent = online ? 'Online' : 'Offline';
                onlineEl.className = 'inline-flex items-center h-8 px-3 text-xs rounded-full ' + (online ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600');
            }
            if (activeEl) {
                const active = parseInt(u.is_active, 10) === 1;
                activeEl.textContent = active ? 'Đang mở' : 'Đang khóa';
                activeEl.className = 'inline-flex items-center h-8 px-3 text-xs rounded-full ' + (active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700');
            }
            const pwEl = document.querySelector('[data-password="'+u.id+'"]');
            if (pwEl) {
                const textEl = pwEl.querySelector('[data-password-text]');
                if (textEl) {
                    textEl.textContent = u.password_plain ? u.password_plain : 'Chưa cấp';
                    textEl.className = u.password_plain ? 'font-semibold text-emerald-700' : 'text-slate-400';
                }
            }
        });
    };
    const renderResets = (resets) => {
        const body = document.getElementById('reset-body');
        if (!body) return;
        if (!Array.isArray(resets) || resets.length === 0) {
            body.innerHTML = '<tr><td colspan="5" class="p-4 text-center text-slate-500">Chưa có yêu cầu quên mật khẩu nào.</td></tr>';
            return;
        }
        body.innerHTML = resets.map(r => {
            return `
                <tr class="border-b hover:bg-slate-50">
                    <td class="p-3">${r.name ?? 'Khách hàng'}</td>
                    <td class="p-3">${r.email ?? ''}</td>
                    <td class="p-3">${r.phone ?? ''}</td>
                    <td class="p-3"><span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">Chờ xử lý</span></td>
                    <td class="p-3">
                        <form method="post" action="<?php echo base_url('admin.php/users/reset-password/'); ?>${r.id}" class="flex flex-col md:flex-row gap-2">
                            <input type="text" name="new_password" class="px-3 py-2 border rounded text-sm" placeholder="Nhập mật khẩu mới" required>
                            <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Gửi mật khẩu</button>
                        </form>
                    </td>
                </tr>
            `;
        }).join('');
    };
    const pollStatus = () => {
        fetch(statusUrl, { cache: 'no-store' })
            .then(r => r.json())
            .then(data => { if (Array.isArray(data)) applyStatus(data); })
            .catch(() => {});
    };
    const pollResets = () => {
        fetch(resetUrl, { cache: 'no-store' })
            .then(r => r.json())
            .then(data => { renderResets(data); })
            .catch(() => {});
    };
    pollStatus();
    pollResets();
    setInterval(pollStatus, 2000);
    setInterval(pollResets, 2000);
});
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
