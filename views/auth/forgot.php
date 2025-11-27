<?php ob_start(); ?>
<?php $hideFooter = true; ?>
<div class="max-w-lg mx-auto">
    <div class="glass-panel rounded-3xl shadow-xl p-6 space-y-4">
        <div class="text-center space-y-1">
            <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Đặt lại mật khẩu</p>
            <h1 class="text-2xl font-bold">Quên mật khẩu</h1>
            <p class="text-sm text-slate-500">Nhập email và số điện thoại đã đăng ký để gửi yêu cầu cấp mật khẩu mới.</p>
        </div>
        <?php if (!empty($error)): ?>
            <div class="p-3 bg-red-50 text-red-600 rounded text-center"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" action="<?php echo base_url('forgot'); ?>" class="space-y-3">
            <div>
                <label class="text-sm text-slate-500 block mb-1">Email</label>
                <input class="w-full px-3 py-3 border rounded-xl focus:outline-none focus:ring focus:ring-blue-200" type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>
            <div>
                <label class="text-sm text-slate-500 block mb-1">Số điện thoại</label>
                <input class="w-full px-3 py-3 border rounded-xl focus:outline-none focus:ring focus:ring-blue-200" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>" required>
            </div>
            <button class="w-full px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-semibold">Gửi yêu cầu</button>
        </form>
        <div class="text-right text-sm">
            <a class="text-blue-600 hover:underline" href="<?php echo base_url('login'); ?>">Quay lại đăng nhập</a>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
