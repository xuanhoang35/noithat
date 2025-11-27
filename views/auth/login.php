<?php ob_start(); ?>
<?php $hideFooter = true; ?>
<div class="max-w-lg mx-auto space-y-4">
    <?php
        $msg = $success ?? ($_SESSION['welcome_message'] ?? '');
        $target = $redirect ?? base_url('');
    ?>
    <?php if (!empty($msg)): ?>
        <div class="p-3 mb-3 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded text-center">
            <?php echo htmlspecialchars($msg); ?>
        </div>
        <script>setTimeout(function(){ window.location.href='<?php echo $target; ?>'; }, 1000);</script>
        <?php unset($_SESSION['welcome_message']); ?>
    <?php endif; ?>
    <div class="glass-panel rounded-3xl shadow-xl p-6 space-y-4">
        <div class="text-center space-y-1">
            <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Chào mừng quý khách đến với Nội Thất Store</p>
            <h1 class="text-2xl font-bold">Đăng nhập</h1>
            <p class="text-sm text-slate-500">Tiếp tục mua sắm và theo dõi đơn hàng của bạn.</p>
        </div>
        <?php if (!empty($error)) echo '<div class="p-3 mb-2 bg-red-50 text-red-600 rounded text-center">'.$error.'</div>'; ?>
        <form method="post" action="<?php echo base_url('login'); ?>" class="grid gap-3">
            <input class="px-3 py-3 border rounded-xl" name="email" placeholder="Email" required>
            <input class="px-3 py-3 border rounded-xl" type="password" name="password" placeholder="Mật khẩu" required>
            <button class="px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 shadow font-semibold">Đăng nhập</button>
        </form>
        <div class="flex justify-between text-sm text-slate-600">
            <a class="text-blue-600 hover:underline" href="<?php echo base_url('forgot'); ?>">Quên mật khẩu?</a>
            <a class="text-slate-700 hover:underline" href="<?php echo base_url('register'); ?>">Chưa có tài khoản?</a>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
