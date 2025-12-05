<?php ob_start(); ?>
<?php $img = asset_url(!empty($product['image']) ? $product['image'] : 'public/assets/img/placeholder.svg'); ?>
<?php $stock = (int)($product['stock'] ?? 0); ?>
<div class="space-y-6">
    <div class="grid lg:grid-cols-[1fr,1.05fr] gap-6 items-start">
        <div class="glass-panel rounded-3xl p-4 shadow-xl">
            <div class="relative overflow-hidden rounded-2xl min-h-[320px]">
                <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover">
                <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-xs font-semibold <?php echo $stock > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'; ?>">
                    <?php echo $stock > 0 ? 'Còn hàng' : 'Hết hàng'; ?>
                </span>
            </div>
        </div>
        <div class="glass-panel rounded-3xl shadow-xl p-5 space-y-4">
            <div>
                <p class="section-title text-slate-500">Chi tiết sản phẩm</p>
                <h1 class="text-2xl font-bold text-slate-900 mt-2"><?php echo $product['name']; ?></h1>
                <p class="text-blue-700 font-bold text-2xl mb-1"><?php echo number_format($product['price']); ?> đ</p>
                <p class="text-sm font-semibold <?php echo $stock > 0 ? 'text-emerald-600' : 'text-red-600'; ?>">
                    <?php echo $stock > 0 ? 'Còn ' . $stock . ' sản phẩm' : 'Hàng đang về'; ?>
                </p>
            </div>
            <div class="grid grid-cols-2 gap-3 text-sm text-slate-600">
                <div class="p-3 rounded-2xl bg-white/70 border border-slate-100">Giao & lắp đặt 24-48h</div>
                <div class="p-3 rounded-2xl bg-white/70 border border-slate-100">Bảo hành minh bạch</div>
            </div>
            <?php if ($stock <= 0): ?>
                <div class="space-y-3">
                    <p class="text-red-600 font-semibold text-sm">Hàng đang về. Vui lòng liên hệ để được hỗ trợ.</p>
                    <a href="tel:0974734668" class="inline-flex items-center justify-center px-5 py-3 bg-amber-500 text-white rounded-lg hover:bg-amber-600 w-full sm:w-auto">Liên hệ ngay</a>
                </div>
            <?php else: ?>
                <form method="post" action="<?php echo base_url('cart/add'); ?>" class="space-y-3">
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/product/' . $product['id']); ?>">
                    <div class="flex items-center gap-3 flex-wrap">
                        <label class="text-sm text-slate-600">Số lượng</label>
                        <input type="number" name="qty" value="1" min="1" max="<?php echo $stock; ?>" class="w-24 px-3 py-2 border rounded text-sm">
                        <button class="px-5 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow">Thêm vào giỏ</button>
                    </div>
                    <p class="text-xs text-slate-500">Mã giảm giá áp dụng tại giỏ hàng.</p>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <div class="glass-panel rounded-3xl shadow-lg p-5 mt-6 lg:mt-10">
        <h3 class="text-lg font-semibold mb-3">Mô tả chi tiết</h3>
        <div class="prose prose-sm max-w-none text-slate-700">
            <?php echo nl2br(htmlspecialchars($product['description'] ?? '')); ?>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
