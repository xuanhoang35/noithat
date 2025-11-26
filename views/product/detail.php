<?php ob_start(); ?>
<?php $img = asset_url(!empty($product['image']) ? $product['image'] : 'public/assets/img/placeholder.svg'); ?>
<?php $stock = (int)($product['stock'] ?? 0); ?>
<div class="grid md:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm p-3">
        <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full rounded-xl object-cover">
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-5 space-y-4">
        <div>
            <h2 class="text-2xl font-bold mb-1"><?php echo $product['name']; ?></h2>
            <p class="text-blue-600 font-bold text-xl mb-2"><?php echo number_format($product['price']); ?> đ</p>
            <p class="text-sm font-semibold <?php echo $stock > 0 ? 'text-emerald-600' : 'text-red-600'; ?>">
                <?php echo $stock > 0 ? 'Còn ' . $stock . ' sản phẩm' : 'Tạm hết hàng'; ?>
            </p>
            <p class="text-slate-600"><?php echo $product['description']; ?></p>
        </div>
        <?php if ($stock <= 0): ?>
            <div class="space-y-3">
                <p class="text-red-600 font-semibold text-sm">Sản phẩm đã hết hàng. Vui lòng liên hệ để được hỗ trợ.</p>
                <a href="tel:0974734668" class="inline-flex items-center justify-center px-5 py-3 bg-amber-500 text-white rounded-lg hover:bg-amber-600 w-full sm:w-auto">Liên hệ</a>
            </div>
        <?php else: ?>
            <form method="post" action="<?php echo base_url('cart/add'); ?>" class="space-y-3">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/product/' . $product['id']); ?>">
                <div class="flex items-center gap-3 flex-wrap">
                    <label class="text-sm text-slate-600">Số lượng</label>
                    <input type="number" name="qty" value="1" min="1" max="<?php echo $stock; ?>" class="w-24 px-3 py-2 border rounded text-sm">
                    <button class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Thêm vào giỏ</button>
                </div>
                <div class="grid gap-2">
                    <label class="text-sm text-slate-600">Áp mã giảm giá</label>
                    <input type="text" class="px-3 py-2 border rounded text-sm" placeholder="Nhập mã giảm giá">
                </div>
                <div class="grid gap-2">
                    <label class="text-sm text-slate-600">Ghi chú đơn hàng</label>
                    <textarea class="px-3 py-2 border rounded text-sm" rows="3" placeholder="Ghi chú cho đơn hàng"></textarea>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
