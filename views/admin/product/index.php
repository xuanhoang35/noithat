<?php ob_start(); ?>
<div class="bg-white rounded-2xl shadow-sm p-4 mb-4">
    <div class="flex justify-between items-center mb-3">
        <h1 class="text-xl font-semibold">Sản phẩm</h1>
        <span class="text-slate-500 text-sm">Thêm nhanh sản phẩm mới</span>
    </div>
    <form method="post" action="<?php echo base_url('admin.php/products/create'); ?>" enctype="multipart/form-data" class="grid md:grid-cols-3 gap-3">
        <input class="px-3 py-2 border rounded" name="name" placeholder="Tên" required>
        <select class="px-3 py-2 border rounded" name="category_id">
            <?php foreach ($categories as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option><?php endforeach; ?>
        </select>
        <input class="px-3 py-2 border rounded" name="price" type="number" placeholder="Giá" required step="0.01">
        <input class="px-3 py-2 border rounded" name="stock" type="number" placeholder="Tồn" required>
        <input class="px-3 py-2 border rounded" name="image_url" placeholder="Link ảnh (tùy chọn)">
        <textarea class="px-3 py-2 border rounded col-span-1 md:col-span-3" name="description" placeholder="Mô tả"></textarea>
        <?php $uploadId = 'upload-create-' . uniqid(); ?>
        <div class="col-span-1 md:col-span-3 space-y-2">
            <label class="text-sm text-slate-600" for="<?php echo $uploadId; ?>">Ảnh sản phẩm (tải lên):</label>
            <div class="flex items-center gap-3">
                <label for="<?php echo $uploadId; ?>" class="flex-1 flex items-center justify-between border rounded px-3 py-2 cursor-pointer hover:border-blue-400">
                    <span id="<?php echo $uploadId; ?>-text" class="text-sm text-slate-500">Chưa có tệp</span>
                    <span class="text-sm font-semibold text-blue-600">Chọn tệp</span>
                </label>
                <input id="<?php echo $uploadId; ?>" class="hidden" type="file" name="image_file" accept="image/*">
            </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function(){
            const input = document.getElementById('<?php echo $uploadId; ?>');
            const text = document.getElementById('<?php echo $uploadId; ?>-text');
            if (input && text) {
                input.addEventListener('change', function(){
                    text.textContent = this.files && this.files[0] ? this.files[0].name : 'Chưa có tệp';
                });
            }
        });
        </script>
        <div class="col-span-1 md:col-span-3">
            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Thêm</button>
        </div>
    </form>
</div>
<div class="bg-white rounded-2xl shadow-sm p-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-3">
        <h2 class="text-lg font-semibold">Danh sách sản phẩm</h2>
        <form method="get" class="flex flex-wrap gap-2 items-center">
            <input class="px-3 py-2 border rounded text-sm" name="q" value="<?php echo htmlspecialchars($search ?? ''); ?>" placeholder="Tìm tên/ID sản phẩm">
            <select name="category_id" class="px-3 py-2 border rounded text-sm">
                <option value="">Tất cả danh mục</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo ((string)($categoryId ?? '') === (string)$c['id']) ? 'selected' : ''; ?>>
                        <?php echo $c['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="px-4 py-2 bg-slate-900 text-white rounded text-sm">Lọc</button>
        </form>
    </div>
    <div class="overflow-auto">
        <table class="min-w-full text-sm">
            <tr class="bg-slate-100 text-left">
                <th class="p-3">ID</th><th class="p-3">Tên</th><th class="p-3">Danh mục</th><th class="p-3">Giá</th><th class="p-3">Tồn</th><th class="p-3">Ảnh</th><th class="p-3">Hành động</th>
            </tr>
            <?php foreach ($products as $p): ?>
            <tr class="border-b hover:bg-slate-50">
                <td class="p-3"><?php echo $p['id']; ?></td>
                <td class="p-3"><?php echo $p['name']; ?></td>
                <td class="p-3"><?php echo $p['category_id']; ?></td>
                <td class="p-3"><?php echo number_format($p['price']); ?></td>
                <td class="p-3"><?php echo $p['stock']; ?></td>
                <td class="p-3"><?php if (!empty($p['image'])): ?><img src="<?php echo asset_url($p['image']); ?>" alt="" class="w-14 h-14 object-cover rounded"><?php endif; ?></td>
                <td class="p-3">
                    <div class="grid grid-cols-2 gap-2">
                        <a class="w-full h-10 flex items-center justify-center text-sm bg-amber-50 text-amber-700 rounded hover:bg-amber-100" href="<?php echo base_url('admin.php/products/edit/' . $p['id']); ?>">Sửa</a>
                        <form method="post" action="<?php echo base_url('admin.php/products/delete/' . $p['id']); ?>" onsubmit="return confirm('Xóa sản phẩm này?');" class="w-full">
                            <button class="w-full h-10 flex items-center justify-center text-sm bg-red-50 text-red-600 rounded hover:bg-red-100">Xóa</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
