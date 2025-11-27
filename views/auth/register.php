<?php ob_start(); ?>
<?php $hideFooter = true; ?>
<div class="max-w-lg mx-auto space-y-4">
    <?php
        $msg = $success ?? ($_SESSION['welcome_message'] ?? '');
        $target = $redirect ?? base_url('login');
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
            <p class="section-title text-slate-500 justify-center">Chào mừng</p>
            <h1 class="text-2xl font-bold">Tạo tài khoản</h1>
            <p class="text-sm text-slate-500">Đặt hàng nhanh hơn, theo dõi đơn và nhận ưu đãi thành viên.</p>
        </div>
        <?php if (!empty($error)) echo '<div class="p-3 mb-2 bg-red-50 text-red-600 rounded text-center">'.$error.'</div>'; ?>
        <form method="post" action="<?php echo base_url('register'); ?>" class="grid gap-3">
            <input class="px-3 py-3 border rounded-xl" name="name" placeholder="Họ tên" required maxlength="30" value="<?php echo htmlspecialchars($name ?? ''); ?>">
            <input class="px-3 py-3 border rounded-xl" type="email" name="email" placeholder="Email" required maxlength="30" pattern="[A-Za-z0-9._%+\-]+@(gmail|email)[A-Za-z0-9.\-]*\.[A-Za-z0-9.\-]+" title="Chỉ chữ không dấu/số, chứa @gmail hoặc @email, đuôi miền tùy ý, tối đa 30 ký tự" value="<?php echo htmlspecialchars($email ?? ''); ?>">
            <input class="px-3 py-3 border rounded-xl" type="tel" inputmode="numeric" name="phone" placeholder="SĐT" required maxlength="10" pattern="0[0-9]{9}" title="Chỉ nhập số, bắt đầu bằng 0 và đủ 10 chữ số" value="<?php echo htmlspecialchars($phone ?? ''); ?>">
            <input class="px-3 py-3 border rounded-xl" type="password" name="password" placeholder="Mật khẩu" required maxlength="30" pattern="[A-Za-z0-9]{1,30}" title="Chỉ chữ không dấu và số, tối đa 30 ký tự">
            <button class="px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 shadow font-semibold">Đăng ký</button>
        </form>
        <div class="text-center text-sm text-slate-600">
            Đã có tài khoản? <a class="text-blue-600 hover:underline" href="<?php echo base_url('login'); ?>">Đăng nhập</a>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
