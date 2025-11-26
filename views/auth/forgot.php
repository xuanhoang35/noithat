<?php ob_start(); ?>
<?php $hideFooter = true; ?>
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-5 space-y-4">
        <div class="text-center">
            <h1 class="text-xl font-semibold">Quên mật khẩu</h1>
            <p class="text-sm text-slate-500 mt-1">Nhập email và số điện thoại đã đăng ký để gửi yêu cầu cấp mật khẩu mới.</p>
        </div>
        <?php if (!empty($error)): ?>
            <div class="p-3 bg-red-50 text-red-600 rounded"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" action="<?php echo base_url('forgot'); ?>" class="space-y-3">
            <div>
                <label class="text-sm text-slate-500 block mb-1">Email</label>
                <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-blue-200" type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>
            <div>
                <label class="text-sm text-slate-500 block mb-1">Số điện thoại</label>
                <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-blue-200" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>" required>
            </div>
            <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Gửi yêu cầu</button>
        </form>
        <div class="text-right text-sm">
            <a class="text-blue-600 hover:underline" href="<?php echo base_url('login'); ?>">Quay lại đăng nhập</a>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
