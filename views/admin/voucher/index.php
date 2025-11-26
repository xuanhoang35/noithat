<?php ob_start(); ?>
<div class="bg-white rounded-2xl shadow-sm p-5 space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold">Mã giảm giá</h1>
            <p class="text-slate-500 text-sm">Thêm, sửa, xóa voucher cho từng danh mục</p>
        </div>
    </div>

    <form method="post" action="<?php echo base_url('admin.php/vouchers/create'); ?>" class="grid md:grid-cols-5 gap-3 bg-slate-50 rounded-xl p-4 border border-slate-100">
        <input class="px-3 py-2 border rounded" name="code" placeholder="Mã voucher" required>
        <input class="px-3 py-2 border rounded" name="discount_percent" type="number" min="1" max="100" placeholder="% giảm" required>
        <input class="px-3 py-2 border rounded" name="usage_limit" type="number" min="1" value="1" placeholder="Số lượt sử dụng">
        <select class="px-3 py-2 border rounded" name="category_id">
            <option value="">Tất cả danh mục</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
            <?php endforeach; ?>
        </select>
        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 md:row-span-2">Thêm mã</button>
        <div class="md:col-span-4">
            <textarea class="w-full px-3 py-2 border rounded" name="description" rows="2" placeholder="Mô tả (tuỳ chọn)"></textarea>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-slate-100 text-left">
                    <th class="p-3">Mã</th>
                    <th class="p-3">% giảm</th>
                    <th class="p-3">Lượt (dùng/tối đa)</th>
                    <th class="p-3">Danh mục áp dụng</th>
                    <th class="p-3">Mô tả</th>
                    <th class="p-3 w-32">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vouchers as $voucher): ?>
                    <tr class="border-b hover:bg-slate-50">
                        <form id="voucher-update-<?php echo $voucher['id']; ?>" method="post" action="<?php echo base_url('admin.php/vouchers/update/' . $voucher['id']); ?>"></form>
                        <td class="p-3"><input form="voucher-update-<?php echo $voucher['id']; ?>" name="code" value="<?php echo htmlspecialchars($voucher['code']); ?>" class="w-full px-2 py-1 border rounded" required></td>
                        <td class="p-3"><input form="voucher-update-<?php echo $voucher['id']; ?>" name="discount_percent" type="number" min="1" max="100" value="<?php echo (int)$voucher['discount_percent']; ?>" class="w-full px-2 py-1 border rounded" required></td>
                        <td class="p-3">
                            <input form="voucher-update-<?php echo $voucher['id']; ?>" name="usage_limit" type="number" min="1" value="<?php echo (int)($voucher['usage_limit'] ?? 1); ?>" class="w-full px-2 py-1 border rounded">
                            <div class="text-xs text-slate-500 mt-1"><?php echo (int)($voucher['used_count'] ?? 0); ?>/<?php echo (int)($voucher['usage_limit'] ?? 1); ?></div>
                        </td>
                        <td class="p-3">
                            <select form="voucher-update-<?php echo $voucher['id']; ?>" name="category_id" class="w-full px-2 py-1 border rounded">
                                <option value="">Tất cả</option>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo (int)$voucher['category_id'] === (int)$c['id'] ? 'selected' : ''; ?>><?php echo $c['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="p-3"><textarea form="voucher-update-<?php echo $voucher['id']; ?>" name="description" rows="1" class="w-full px-2 py-1 border rounded"><?php echo htmlspecialchars($voucher['description']); ?></textarea></td>
                        <td class="p-3">
                            <div class="grid grid-cols-2 gap-2">
                                <button form="voucher-update-<?php echo $voucher['id']; ?>" class="h-10 w-full inline-flex items-center justify-center bg-amber-50 text-amber-700 rounded text-xs font-semibold hover:bg-amber-100">Lưu</button>
                                <form method="post" action="<?php echo base_url('admin.php/vouchers/delete/' . $voucher['id']); ?>" onsubmit="return confirm('Xóa mã này?');" class="w-full">
                                    <button class="h-10 w-full inline-flex items-center justify-center bg-red-50 text-red-600 rounded text-xs font-semibold hover:bg-red-100">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($vouchers)): ?>
                    <tr><td colspan="6" class="p-4 text-center text-slate-500">Chưa có mã giảm giá nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
