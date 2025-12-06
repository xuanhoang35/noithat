<?php ob_start(); ?>
<div class="bg-white rounded-2xl shadow-sm p-4">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-3">
        <div>
            <h1 class="text-xl font-semibold">Khách hàng</h1>
            <p class="text-slate-500 text-sm">Quản lý tài khoản (Online <span data-online-count><?php echo (int)($onlineCount ?? 0); ?></span> · Offline <span data-offline-count><?php echo max(0, count($users) - (int)($onlineCount ?? 0)); ?></span>)</p>
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
    <div class="overflow-auto max-h-[320px] overflow-y-auto">
        <table class="min-w-full text-sm">
            <tr class="bg-slate-100 text-left">
                <th class="p-3">ID</th><th class="p-3">Tên</th><th class="p-3">Email</th><th class="p-3">Điện thoại</th><th class="p-3">Địa chỉ</th><th class="p-3">Online</th><th class="p-3">Trạng thái</th><th class="p-3">Mật khẩu</th><th class="p-3">Hành động</th>
            </tr>
            <?php foreach ($users as $u): ?>
            <tr class="border-b hover:bg-slate-50">
                <td class="p-3"><?php echo $u['id']; ?></td>
                <td class="p-3">
                    <input form="user-save-<?php echo $u['id']; ?>" name="name" class="w-full px-2 py-1 border rounded text-sm" value="<?php echo htmlspecialchars($u['name']); ?>" required>
                </td>
                <td class="p-3">
                    <input form="user-save-<?php echo $u['id']; ?>" name="email" type="email" class="w-full px-2 py-1 border rounded text-sm" value="<?php echo htmlspecialchars($u['email']); ?>" required>
                </td>
                <td class="p-3">
                    <input form="user-save-<?php echo $u['id']; ?>" name="phone" class="w-full px-2 py-1 border rounded text-sm" value="<?php echo htmlspecialchars($u['phone']); ?>" required>
                </td>
                <td class="p-3">
                    <input form="user-save-<?php echo $u['id']; ?>" name="address" class="w-full px-2 py-1 border rounded text-sm" value="<?php echo htmlspecialchars($u['address'] ?? ''); ?>" placeholder="(trống)">
                </td>
                <td class="p-3">
                    <?php $online = (int)($u['is_online'] ?? 0) === 1; ?>
                    <span data-online="<?php echo $u['id']; ?>" class="inline-flex items-center h-8 px-3 text-xs rounded-full <?php echo $online ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600'; ?>">
                        <?php echo $online ? 'Online' : 'Offline'; ?>
                    </span>
                </td>
                <td class="p-3">
                    <?php $active = (int)($u['is_active'] ?? 1) === 1; ?>
                    <div class="flex items-center gap-2">
                        <span data-active="<?php echo $u['id']; ?>" class="inline-flex items-center h-8 px-3 text-xs rounded-full <?php echo $active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700'; ?>">
                            <?php echo $active ? 'Đang mở' : 'Đang khóa'; ?>
                        </span>
                        <?php if ($u['role'] !== 'admin'): ?>
                            <form method="post" action="<?php echo base_url('admin.php/users/toggle/' . $u['id']); ?>">
                                <button type="submit" data-toggle="<?php echo $u['id']; ?>" class="h-8 px-3 inline-flex items-center justify-center text-xs leading-none rounded <?php echo $active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-blue-50 text-blue-700 hover:bg-blue-100'; ?>">
                                    <?php echo $active ? 'Khóa' : 'Mở'; ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="p-3 space-y-1" data-password="<?php echo $u['id']; ?>">
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
                    <?php if ($u['role'] !== 'admin'): ?>
                        <input form="user-save-<?php echo $u['id']; ?>" name="password" type="text" class="w-full px-2 py-1 border rounded text-sm" value="<?php echo htmlspecialchars($pw); ?>" placeholder="Nhập mật khẩu mới">
                    <?php else: ?>
                        <?php if ($pw !== ''): ?>
                            <span data-password-text class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold inline-flex items-center gap-2">
                                <span class="text-slate-600">PW:</span> <span class="font-semibold text-emerald-700"><?php echo htmlspecialchars($pw); ?></span>
                            </span>
                        <?php else: ?>
                            <span data-password-text class="text-xs text-slate-400">Chưa cấp</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td class="p-3 align-middle">
                    <?php if ($u['role'] !== 'admin'): ?>
                        <div class="flex gap-2">
                            <form id="user-save-<?php echo $u['id']; ?>" method="post" action="<?php echo base_url('admin.php/users/edit/' . $u['id']); ?>" class="hidden">
                                <input type="hidden" name="role" value="<?php echo htmlspecialchars($u['role']); ?>">
                                <input type="hidden" name="is_active" value="<?php echo (int)($u['is_active'] ?? 1); ?>">
                            </form>
                            <button type="submit" form="user-save-<?php echo $u['id']; ?>" class="h-8 px-3 mt-3 inline-flex items-center justify-center text-xs leading-none rounded bg-blue-50 text-blue-700 hover:bg-blue-100">Lưu</button>
                            <form method="post" action="<?php echo base_url('admin.php/users/delete/' . $u['id']); ?>" onsubmit="return confirm('Xóa khách hàng này?');">
                                <button class="h-8 px-3 mt-3 inline-flex items-center justify-center text-xs leading-none rounded bg-red-50 text-red-700 hover:bg-red-100">Xóa</button>
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
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-2">
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-semibold">Quản lý mật khẩu khách hàng</h2>
            </div>
            <p class="text-slate-500 text-sm">Tiếp nhận yêu cầu quên mật khẩu và cấp mật khẩu mới.</p>
        </div>
        <p class="text-slate-500 text-sm mb-3">Có <span data-reset-count><?php echo count(array_filter($resets, function($r){ return ($r['status'] ?? '') === 'pending'; })); ?></span> yêu cầu cấp mật khẩu mới</p>
        <div class="overflow-auto max-h-[300px] overflow-y-auto">
            <table class="min-w-full text-sm">
                <tr class="bg-slate-100 text-left">
                    <th class="p-3">Khách hàng</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Điện thoại</th>
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
                        <div class="flex flex-col md:flex-row gap-2">
                            <form method="post" action="<?php echo base_url('admin.php/users/reset-password/' . $reset['id']); ?>" class="flex flex-col md:flex-row gap-2">
                                <input type="text" name="new_password" class="px-3 py-2 border rounded text-sm" placeholder="Nhập mật khẩu mới" required>
                                <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Gửi mật khẩu</button>
                            </form>
                            <form method="post" action="<?php echo base_url('admin.php/users/reset-reject/' . $reset['id']); ?>" class="flex">
                                <button type="submit" class="px-4 py-2 text-sm bg-red-50 text-red-700 rounded hover:bg-red-100 w-full">Từ chối yêu cầu</button>
                            </form>
                        </div>
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
        let onlineCount = 0;
        users.forEach(u => {
            const onlineEl = document.querySelector('[data-online="'+u.id+'"]');
            const activeEl = document.querySelector('[data-active="'+u.id+'"]');
            if (onlineEl) {
                const online = parseInt(u.is_online, 10) === 1;
                if (online) onlineCount++;
                onlineEl.textContent = online ? 'Online' : 'Offline';
                onlineEl.className = 'inline-flex items-center h-8 px-3 text-xs rounded-full ' + (online ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600');
            }
            if (activeEl) {
                const active = parseInt(u.is_active, 10) === 1;
                activeEl.textContent = active ? 'Đang mở' : 'Đang khóa';
                activeEl.className = 'inline-flex items-center h-8 px-3 text-xs rounded-full ' + (active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700');
            }
            const toggleBtn = document.querySelector('[data-toggle="'+u.id+'"]');
            if (toggleBtn) {
                const active = parseInt(u.is_active, 10) === 1;
                toggleBtn.textContent = active ? 'Khóa' : 'Mở';
                toggleBtn.className = 'h-8 px-3 inline-flex items-center justify-center text-xs leading-none rounded ' + (active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-blue-50 text-blue-700 hover:bg-blue-100');
            }
            const activeInput = document.querySelector('#user-save-'+u.id+' input[name=\"is_active\"]');
            if (activeInput) {
                activeInput.value = parseInt(u.is_active, 10) === 1 ? '1' : '0';
            }
            const pwEl = document.querySelector('[data-password="'+u.id+'"]');
            if (pwEl) {
                const textEl = pwEl.querySelector('[data-password-text]');
                if (textEl) {
                    textEl.textContent = u.password_plain ? u.password_plain : 'Chưa cấp';
                    textEl.className = u.password_plain ? 'font-semibold text-emerald-700' : 'text-slate-400';
                }
                const inputEl = pwEl.querySelector('input[name="password"]');
                if (inputEl && !document.activeElement.isSameNode(inputEl)) {
                    inputEl.value = u.password_plain || '';
                }
            }
        });
        const onlineCountEl = document.querySelector('[data-online-count]');
        if (onlineCountEl) onlineCountEl.textContent = onlineCount;
        const offlineCountEl = document.querySelector('[data-offline-count]');
        if (offlineCountEl) offlineCountEl.textContent = Math.max(0, users.length - onlineCount);
    };
    const renderResets = (resets) => {
        const body = document.getElementById('reset-body');
        if (!body) return;
        if (window.__resetInputFocus) return; // tránh ghi đè khi đang nhập
        const resetCountEl = document.querySelector('[data-reset-count]');
        if (resetCountEl) {
            resetCountEl.textContent = Array.isArray(resets) ? resets.length : 0;
        }
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
                        <div class="flex flex-col md:flex-row gap-2">
                            <form method="post" action="<?php echo base_url('admin.php/users/reset-password/'); ?>${r.id}" class="flex flex-col md:flex-row gap-2">
                                <input type="text" name="new_password" class="px-3 py-2 border rounded text-sm" placeholder="Nhập mật khẩu mới" required onfocus="window.__resetInputFocus=true;" onblur="window.__resetInputFocus=false;">
                                <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Gửi mật khẩu</button>
                            </form>
                            <form method="post" action="<?php echo base_url('admin.php/users/reset-reject/'); ?>${r.id}" class="flex">
                                <button type="submit" class="px-4 py-2 text-sm bg-red-50 text-red-700 rounded hover:bg-red-100 w-full">Từ chối yêu cầu</button>
                            </form>
                        </div>
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
