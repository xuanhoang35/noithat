<?php ob_start(); ?>
<?php $hideFooter = true; ?>
<div class="max-w-md mx-auto">
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
    <div class="bg-white rounded-xl shadow-sm p-4">
        <h1 class="text-xl font-semibold text-center mb-3">Đăng ký</h1>
        <?php if (!empty($error)) echo '<div class="p-3 mb-2 bg-red-50 text-red-600 rounded">'.$error.'</div>'; ?>
        <form method="post" action="<?php echo base_url('register'); ?>" class="grid gap-2">
            <input class="px-3 py-2 border rounded" name="name" placeholder="Họ tên" required maxlength="30" value="<?php echo htmlspecialchars($name ?? ''); ?>">
            <input class="px-3 py-2 border rounded" type="email" name="email" placeholder="Email" required maxlength="30" pattern="[A-Za-z0-9._%+\-]+@(gmail|gmai|email)[A-Za-z0-9.\-]*\.[A-Za-z0-9.\-]+" title="Chỉ chữ không dấu/số, chứa @gmail/@gmai/@email, đuôi miền tùy ý, tối đa 30 ký tự" value="<?php echo htmlspecialchars($email ?? ''); ?>">
            <input class="px-3 py-2 border rounded" type="tel" inputmode="numeric" name="phone" placeholder="SĐT" required maxlength="10" pattern="0[0-9]{9}" title="Chỉ nhập số, bắt đầu bằng 0 và đủ 10 chữ số" value="<?php echo htmlspecialchars($phone ?? ''); ?>">
            <input class="px-3 py-2 border rounded" type="password" name="password" placeholder="Mật khẩu" required maxlength="30" pattern="[A-Za-z0-9]{1,30}" title="Chỉ chữ không dấu và số, tối đa 30 ký tự">
            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Đăng ký</button>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
