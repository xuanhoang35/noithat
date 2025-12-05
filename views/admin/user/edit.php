<?php ob_start(); ?>
<div class="bg-white rounded-2xl shadow-sm p-5 max-w-3xl">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-semibold">Chỉnh sửa khách hàng</h1>
            <p class="text-slate-500 text-sm">Cập nhật thông tin tài khoản</p>
        </div>
        <a class="px-4 py-2 rounded bg-slate-100 text-slate-700 hover:bg-slate-200 text-sm" href="<?php echo base_url('admin.php/users'); ?>">← Quay lại</a>
    </div>
    <?php if (!empty($error)): ?>
        <div class="mb-3 px-3 py-2 rounded bg-red-50 text-red-700 border border-red-100 text-sm"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo base_url('admin.php/users/edit/' . $user['id']); ?>" class="grid gap-3">
        <div class="grid md:grid-cols-2 gap-3">
            <div>
                <label class="text-sm text-slate-600">Họ tên</label>
                <input name="name" class="w-full px-3 py-2 border rounded" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div>
                <label class="text-sm text-slate-600">Email</label>
                <input name="email" type="email" class="w-full px-3 py-2 border rounded" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-3">
            <div>
                <label class="text-sm text-slate-600">Số điện thoại</label>
                <input name="phone" class="w-full px-3 py-2 border rounded" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
            <div>
                <label class="text-sm text-slate-600">Địa chỉ</label>
                <input name="address" class="w-full px-3 py-2 border rounded" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
            </div>
        </div>
        <div class="grid md:grid-cols-3 gap-3">
            <div>
                <label class="text-sm text-slate-600">Vai trò</label>
                <select name="role" class="w-full px-3 py-2 border rounded">
                    <option value="user" <?php echo ($user['role'] ?? 'user') === 'user' ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo ($user['role'] ?? 'user') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <div class="flex items-center gap-2 pt-6">
                <input type="hidden" name="is_active" value="0">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" <?php echo (int)($user['is_active'] ?? 1) === 1 ? 'checked' : ''; ?>>
                    <span class="text-sm text-slate-700">Kích hoạt</span>
                </label>
            </div>
        </div>
        <div class="flex gap-2 pt-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Lưu</button>
            <a class="px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200" href="<?php echo base_url('admin.php/users'); ?>">Hủy</a>
        </div>
    </form>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
