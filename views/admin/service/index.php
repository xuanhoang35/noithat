<?php ob_start(); ?>
<div class="bg-white rounded-2xl shadow-sm p-5 space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold">Quản lý dịch vụ</h1>
        </div>
    </div>

    <form method="post" action="<?php echo base_url('admin.php/services/create'); ?>" class="grid md:grid-cols-4 gap-3 bg-slate-50 rounded-xl p-4 border border-slate-100">
        <input name="name" required class="px-3 py-2 border rounded" placeholder="Tên dịch vụ">
        <input name="sla" class="px-3 py-2 border rounded" placeholder="Thời gian thực hiện">
        <input name="price" type="number" step="0.01" class="px-3 py-2 border rounded" placeholder="Giá (VNĐ)">
        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 md:row-span-2">Thêm dịch vụ</button>
        <div class="md:col-span-3">
            <textarea name="description" rows="2" class="w-full px-3 py-2 border rounded" placeholder="Mô tả ngắn"></textarea>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-slate-100 text-left">
                    <th class="p-3">Tên dịch vụ</th>
                    <th class="p-3">Mô tả</th>
                    <th class="p-3">Thời gian</th>
                    <th class="p-3">Giá</th>
                    <th class="p-3 w-36">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php $editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0; ?>
                <?php foreach ($services as $s): ?>
                    <?php $isEdit = $editId === (int)$s['id']; ?>
                    <tr class="border-b hover:bg-slate-50">
                        <?php if ($isEdit): ?>
                            <form method="post" action="<?php echo base_url('admin.php/services/update/' . $s['id']); ?>">
                                <td class="p-3"><input name="name" class="w-full px-2 py-1 border rounded" value="<?php echo htmlspecialchars($s['name']); ?>" required></td>
                                <td class="p-3"><textarea name="description" class="w-full px-2 py-1 border rounded" rows="2"><?php echo htmlspecialchars($s['description']); ?></textarea></td>
                                <td class="p-3"><input name="sla" class="w-full px-2 py-1 border rounded" value="<?php echo htmlspecialchars($s['sla']); ?>"></td>
                                <td class="p-3"><input name="price" type="number" step="0.01" class="w-full px-2 py-1 border rounded" value="<?php echo htmlspecialchars($s['price']); ?>"></td>
                                <td class="p-3">
                                    <div class="grid grid-cols-2 gap-2">
                                        <button class="h-10 w-full bg-blue-600 text-white rounded hover:bg-blue-700 text-xs">Lưu</button>
                                        <a class="h-10 w-full flex items-center justify-center bg-slate-100 text-slate-700 rounded text-xs" href="<?php echo base_url('admin.php/services'); ?>">Hủy</a>
                                    </div>
                                </td>
                            </form>
                        <?php else: ?>
                            <td class="p-3 font-semibold text-slate-800"><?php echo htmlspecialchars($s['name']); ?></td>
                            <td class="p-3 text-slate-600"><?php echo htmlspecialchars($s['description']); ?></td>
                            <td class="p-3 text-slate-600"><?php echo htmlspecialchars($s['sla']); ?></td>
                            <td class="p-3 text-slate-800"><?php echo $s['price'] ? number_format($s['price']) . ' đ' : '-'; ?></td>
                            <td class="p-3">
                                <div class="grid grid-cols-2 gap-2">
                                    <a class="h-10 w-full flex items-center justify-center bg-amber-50 text-amber-700 rounded text-xs hover:bg-amber-100" href="<?php echo base_url('admin.php/services?edit=' . $s['id']); ?>">Sửa</a>
                                    <form method="post" action="<?php echo base_url('admin.php/services/delete/' . $s['id']); ?>" onsubmit="return confirm('Xóa dịch vụ này?');" class="w-full">
                                        <button class="h-10 w-full bg-red-50 text-red-600 rounded text-xs hover:bg-red-100">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($services)): ?>
                    <tr><td colspan="5" class="p-4 text-center text-slate-500">Chưa có dịch vụ nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-4 border border-slate-100">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-3">
            <div>
                <h2 class="text-lg font-semibold">Đặt lịch dịch vụ</h2>
                <span class="text-sm text-slate-500"><?php echo count($bookings ?? []); ?> lịch</span>
            </div>
            <form method="get" action="<?php echo base_url('admin.php/services'); ?>" class="flex flex-wrap gap-2 items-center">
                <input type="hidden" name="seen" value="services">
                <input type="text" name="q" value="<?php echo htmlspecialchars($search ?? ''); ?>" class="px-3 py-2 border rounded text-sm" placeholder="Tìm khách hoặc SĐT">
                <select name="service_id" class="px-3 py-2 border rounded text-sm">
                    <option value="">Tất cả dịch vụ</option>
                    <?php foreach ($services as $s): ?>
                        <option value="<?php echo $s['id']; ?>" <?php echo ((string)($serviceId ?? '') === (string)$s['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Tìm</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-100 text-left">
                        <th class="p-3">Dịch vụ</th>
                        <th class="p-3">Khách</th>
                        <th class="p-3">Liên hệ</th>
                        <th class="p-3">Thời gian</th>
                        <th class="p-3">Địa chỉ</th>
                        <th class="p-3">Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($bookings ?? []) as $b): ?>
                        <tr class="border-b hover:bg-slate-50">
                            <td class="p-3 font-semibold text-slate-800"><?php echo htmlspecialchars($b['service_name']); ?></td>
                            <td class="p-3 text-slate-700"><?php echo htmlspecialchars($b['name']); ?></td>
                            <td class="p-3 text-slate-600"><?php echo htmlspecialchars($b['phone']); ?><?php if(!empty($b['email'])) echo '<br>'.htmlspecialchars($b['email']); ?></td>
                            <td class="p-3 text-slate-600"><?php echo date('d/m/Y H:i', strtotime($b['schedule_at'])); ?></td>
                            <td class="p-3 text-slate-600"><?php echo htmlspecialchars($b['address']); ?></td>
                            <td class="p-3 text-slate-600"><?php echo htmlspecialchars($b['note']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($bookings)): ?>
                        <tr><td colspan="6" class="p-4 text-center text-slate-500">Chưa có lịch đặt nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
