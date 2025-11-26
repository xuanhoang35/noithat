<?php ob_start(); ?>
<?php $hideFooter = true; ?>
<div class="max-w-md mx-auto">
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
    <div class="bg-white rounded-xl shadow-sm p-4">
        <h1 class="text-xl font-semibold text-center mb-3">Đăng nhập</h1>
        <?php if (!empty($error)) echo '<div class="p-3 mb-2 bg-red-50 text-red-600 rounded">'.$error.'</div>'; ?>
        <form method="post" action="<?php echo base_url('login'); ?>" class="grid gap-2">
            <input class="px-3 py-2 border rounded" name="email" placeholder="Email" required>
            <input class="px-3 py-2 border rounded" type="password" name="password" placeholder="Mật khẩu" required>
            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Đăng nhập</button>
        </form>
        <div class="text-right text-sm mt-2">
            <a class="text-blue-600 hover:underline" href="<?php echo base_url('forgot'); ?>">Quên mật khẩu?</a>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
