<?php ob_start(); ?>
<div class="bg-white rounded-2xl shadow-sm p-4 space-y-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold">Slide banner</h1>
            <p class="text-slate-500 text-sm">Quản lý hình ảnh đang chạy ở trang chủ.</p>
        </div>
        <form method="post" action="<?php echo base_url('admin.php/sliders/create'); ?>" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2 items-start sm:items-center">
            <input type="file" name="image_file" accept="image/*" class="text-sm">
            <span class="text-xs text-slate-400">hoặc</span>
            <input type="url" name="image_url" placeholder="Dán URL ảnh" class="px-3 py-2 border rounded text-sm w-64">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">Thêm ảnh</button>
        </form>
    </div>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="px-3 py-2 rounded bg-red-50 text-red-700 text-sm">
            <?php echo htmlspecialchars($_SESSION['flash_error']); ?>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="px-3 py-2 rounded bg-emerald-50 text-emerald-700 text-sm">
            <?php echo htmlspecialchars($_SESSION['flash_success']); ?>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <?php if (!empty($sliders)): ?>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <?php foreach ($sliders as $s): $img = asset_url($s['image']); ?>
                <div class="border rounded-xl overflow-hidden shadow-sm bg-slate-50">
                    <img src="<?php echo $img; ?>" alt="Slider" class="w-full h-40 object-cover">
                    <div class="p-3 flex items-center justify-between text-sm">
                        <span class="text-slate-500"><?php echo $s['created_at']; ?></span>
                        <form method="post" action="<?php echo base_url('admin.php/sliders/delete/' . $s['id']); ?>" onsubmit="return confirm('Xóa ảnh này khỏi slide?');">
                            <button class="px-3 py-1 bg-red-50 text-red-600 rounded hover:bg-red-100 text-xs">Xóa</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="p-4 bg-slate-50 text-slate-600 rounded">Chưa có ảnh slide nào.</div>
    <?php endif; ?>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
