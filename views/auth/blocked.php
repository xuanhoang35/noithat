<?php ob_start(); ?>
<div class="max-w-md mx-auto text-center space-y-4">
    <div class="bg-red-50 text-red-700 border border-red-100 rounded-xl p-5 shadow-sm">
        <h1 class="text-xl font-semibold mb-2">Thông báo</h1>
        <p class="text-sm"><?php echo htmlspecialchars($message ?? 'Hết phiên đăng nhập, vui lòng đăng nhập lại.'); ?></p>
        <p class="text-xs text-red-500 mt-1">Bạn sẽ được đăng xuất trong giây lát.</p>
    </div>
    <div class="text-sm text-slate-500" id="countdown">Đăng xuất sau 3 giây...</div>
</div>
<script>
setTimeout(function(){ window.location.href = '<?php echo base_url('logout'); ?>'; }, 3000);
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
