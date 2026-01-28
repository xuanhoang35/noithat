<?php ob_start(); ?>
<div class="bg-white rounded-2xl shadow-sm p-4 mb-4">
    <h1 class="text-xl font-semibold mb-3">Sửa sản phẩm</h1>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="mb-3 px-3 py-2 rounded bg-red-50 text-red-700 text-sm">
            <?php echo htmlspecialchars($_SESSION['flash_error']); ?>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="mb-3 px-3 py-2 rounded bg-emerald-50 text-emerald-700 text-sm">
            <?php echo htmlspecialchars($_SESSION['flash_success']); ?>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <form method="post" action="<?php echo base_url('admin.php/products/edit/' . $product['id']); ?>" enctype="multipart/form-data" class="grid md:grid-cols-3 gap-3">
        <input class="px-3 py-2 border rounded" name="name" value="<?php echo $product['name']; ?>" placeholder="Tên" required>
        <select class="px-3 py-2 border rounded" name="category_id">
            <?php foreach ($categories as $c): ?>
                <option value="<?php echo $c['id']; ?>" <?php echo $product['category_id']==$c['id']?'selected':''; ?>><?php echo $c['name']; ?></option>
            <?php endforeach; ?>
        </select>
        <input class="px-3 py-2 border rounded" name="price" type="number" placeholder="Giá" required step="0.01" value="<?php echo $product['price']; ?>">
        <input class="px-3 py-2 border rounded" name="stock" type="number" placeholder="Tồn" required value="<?php echo $product['stock']; ?>">
        <input class="px-3 py-2 border rounded" name="image_url" placeholder="Link ảnh (tùy chọn)" value="<?php echo htmlspecialchars($product['image']); ?>">
        <textarea class="px-3 py-2 border rounded col-span-1 md:col-span-3" name="description" placeholder="Mô tả"><?php echo $product['description']; ?></textarea>
        <?php $uploadEditId = 'upload-edit-' . uniqid(); ?>
        <div class="col-span-1 md:col-span-3 space-y-2">
            <label class="text-sm text-slate-600" for="<?php echo $uploadEditId; ?>">Ảnh sản phẩm (tải lên):</label>
            <div class="flex items-center gap-3">
                <label for="<?php echo $uploadEditId; ?>" class="flex-1 flex items-center justify-between border rounded px-3 py-2 cursor-pointer hover:border-blue-400">
                    <span id="<?php echo $uploadEditId; ?>-text" class="text-sm text-slate-500">Chưa có tệp</span>
                    <span class="text-sm font-semibold text-blue-600">Chọn tệp</span>
                </label>
                <input id="<?php echo $uploadEditId; ?>" class="hidden" type="file" name="image_file" accept="image/*">
            </div>
            <?php if (!empty($product['image'])): ?>
                <div class="mt-2"><img src="<?php echo asset_url($product['image']); ?>" alt="" class="w-20 h-20 object-cover rounded"></div>
            <?php endif; ?>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function(){
            const input = document.getElementById('<?php echo $uploadEditId; ?>');
            const text = document.getElementById('<?php echo $uploadEditId; ?>-text');
            if (input && text) {
                input.addEventListener('change', function(){
                    text.textContent = this.files && this.files[0] ? this.files[0].name : 'Chưa có tệp';
                });
            }
        });
        </script>
        <div class="col-span-1 md:col-span-3 flex gap-2">
            <a class="px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200" href="<?php echo base_url('admin.php/products'); ?>">Hủy</a>
            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Lưu</button>
        </div>
    </form>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
