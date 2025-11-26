<?php ob_start(); ?>
<?php
    $c = $config ?? [];
    $enabled = !empty($c['enabled']);
    $title = $c['title'] ?? '';
    $subtitle = $c['subtitle'] ?? '';
    $message = $c['message'] ?? '';
    $image = $c['image'] ?? '';
    $preview = $image ? asset_url($image) : asset_url('public/assets/img/placeholder.svg');
?>
<div class="bg-white rounded-2xl shadow-sm p-5 space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold">Chế độ bảo trì</h1>
            <p class="text-slate-500 text-sm">Thiết kế trang thông báo và bật/tắt website.</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs <?php echo $enabled ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-700'; ?>">
            <?php echo $enabled ? 'Đang đóng website' : 'Đang mở website'; ?>
        </span>
    </div>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="p-3 rounded bg-red-50 text-red-700 border border-red-100"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="p-3 rounded bg-emerald-50 text-emerald-700 border border-emerald-100"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>

    <div class="grid lg:grid-cols-3 gap-4">
        <form class="lg:col-span-2 space-y-3" method="post" action="<?php echo base_url('admin.php/maintenance'); ?>" enctype="multipart/form-data">
            <div>
                <label class="text-sm text-slate-600">Tiêu đề</label>
                <input name="title" class="w-full px-3 py-2 border rounded" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            <div>
                <label class="text-sm text-slate-600">Phụ đề</label>
                <input name="subtitle" class="w-full px-3 py-2 border rounded" value="<?php echo htmlspecialchars($subtitle); ?>">
            </div>
            <div>
                <label class="text-sm text-slate-600">Thông điệp</label>
                <textarea name="message" rows="3" class="w-full px-3 py-2 border rounded"><?php echo htmlspecialchars($message); ?></textarea>
            </div>
            <div>
                <label class="text-sm text-slate-600">Chọn ảnh (bắt buộc)</label>
                <input type="file" name="image" accept="image/*" class="w-full text-sm">
            </div>
            <label class="inline-flex items-center gap-2">
                <span class="relative inline-flex items-center">
                    <input type="checkbox" name="enabled" value="1" <?php echo $enabled ? 'checked' : ''; ?> class="sr-only" id="maintenance-toggle">
                    <span class="w-12 h-6 bg-slate-200 rounded-full transition peer-checked:bg-red-500"></span>
                    <span class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-6"></span>
                </span>
                <span class="text-sm text-red-600 font-semibold">Đóng trang web (bật chế độ bảo trì)</span>
            </label>
            <div class="flex gap-3">
                <button class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Lưu</button>
                <a class="px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 text-sm" href="<?php echo base_url('maintenance'); ?>" target="_blank">Xem trang bảo trì</a>
            </div>
        </form>
        <div class="lg:col-span-1">
            <div class="border rounded-2xl overflow-hidden shadow-sm">
                <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white p-4">
                    <p class="text-xs uppercase tracking-widest text-blue-200">Preview</p>
                    <p class="text-lg font-semibold">Trang bảo trì</p>
                </div>
                <div class="p-4 space-y-2 bg-slate-50">
                    <img src="<?php echo $preview; ?>" alt="Preview" class="w-full h-40 object-cover rounded-lg border border-slate-200">
                    <div class="bg-white rounded-xl p-3 shadow border border-slate-100">
                        <p class="text-lg font-bold text-slate-800"><?php echo htmlspecialchars($title); ?></p>
                        <p class="text-sm text-slate-600"><?php echo htmlspecialchars($subtitle); ?></p>
                        <p class="text-sm text-slate-500 mt-2 whitespace-pre-line"><?php echo htmlspecialchars($message); ?></p>
                        <div class="mt-3 flex gap-2">
                            <span class="px-3 py-2 rounded bg-blue-600 text-white text-xs">Liên hệ CSKH</span>
                            <span class="px-3 py-2 rounded bg-slate-200 text-slate-700 text-xs">Hotline: 0974.734.668</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const toggle = document.getElementById('maintenance-toggle');
    const form = toggle ? toggle.closest('form') : null;
    if (!toggle || !form) return;
    toggle.addEventListener('change', function(){
        form.submit(); // gạt là lưu ngay
    });
});
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
