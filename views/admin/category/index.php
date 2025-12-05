<?php ob_start(); ?>
<div class="grid lg:grid-cols-3 gap-3">
    <div>
        <div class="bg-white rounded-2xl shadow-sm p-4">
            <h2 class="text-lg font-semibold mb-3">Thêm danh mục</h2>
            <form method="post" action="<?php echo base_url('admin.php/categories/create'); ?>" class="grid gap-2">
                <input class="px-3 py-2 border rounded" name="name" placeholder="Tên danh mục" required>
                <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Thêm</button>
            </form>
        </div>
    </div>
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm p-4">
            <div class="flex items-center justify-between gap-3 mb-3">
                <h2 class="text-lg font-semibold">Danh mục</h2>
                <form method="get" class="flex gap-2">
                    <input class="px-3 py-2 border rounded text-sm" name="q" value="<?php echo htmlspecialchars($search ?? ''); ?>" placeholder="Tìm danh mục">
                    <button class="px-3 py-2 bg-slate-900 text-white rounded text-sm">Tìm</button>
                </form>
            </div>
            <ul class="divide-y">
                <?php $editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0; ?>
                <?php foreach ($categories as $c): ?>
                    <li class="py-2 px-2 flex justify-between items-center">
                        <?php if ($editId === (int)$c['id']): ?>
                            <form method="post" action="<?php echo base_url('admin.php/categories/update/' . $c['id']); ?>" class="flex gap-2 w-full items-center">
                                <input name="name" class="flex-1 px-3 py-2 border rounded" value="<?php echo htmlspecialchars($c['name']); ?>" required>
                                <div class="flex gap-2">
                                    <button class="h-10 px-3 flex items-center justify-center text-xs bg-blue-600 text-white rounded hover:bg-blue-700">Lưu</button>
                                    <a class="h-10 px-3 flex items-center justify-center text-xs bg-slate-100 text-slate-700 rounded" href="<?php echo base_url('admin.php/categories'); ?>">Hủy</a>
                                </div>
                            </form>
                        <?php else: ?>
                            <span><?php echo $c['name']; ?></span>
                            <div class="flex gap-2">
                                <a class="h-10 px-3 flex items-center justify-center text-xs bg-amber-50 text-amber-700 rounded hover:bg-amber-100" href="<?php echo base_url('admin.php/categories?edit=' . $c['id']); ?>">Sửa</a>
                                <form method="post" action="<?php echo base_url('admin.php/categories/delete/' . $c['id']); ?>" onsubmit="return confirm('Xóa danh mục này?');">
                                    <button class="h-10 px-3 flex items-center justify-center text-xs bg-red-50 text-red-600 rounded hover:bg-red-100">Xóa</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
