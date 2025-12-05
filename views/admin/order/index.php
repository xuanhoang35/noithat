<?php ob_start(); ?>
<div class="bg-white rounded-2xl shadow-sm p-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-3">
        <div>
            <h1 class="text-xl font-semibold">Đơn hàng</h1>
            <span class="text-slate-500 text-sm">Theo dõi & cập nhật trạng thái</span>
        </div>
        <form method="get" class="flex gap-2 items-center">
            <input class="px-3 py-2 border rounded text-sm" name="q" value="<?php echo htmlspecialchars($search ?? ''); ?>" placeholder="Mã đơn, tên, SĐT, email">
            <button class="px-3 py-2 bg-slate-900 text-white rounded text-sm">Tìm</button>
        </form>
    </div>
    <div class="overflow-auto">
        <table class="min-w-full text-sm">
            <tr class="bg-slate-100 text-left">
                <th class="p-3">Mã</th><th class="p-3">Khách</th><th class="p-3">Tổng</th><th class="p-3">Thanh toán</th><th class="p-3">Trạng thái</th><th class="p-3">Cập nhật</th>
            </tr>
                <?php foreach ($orders as $o): ?>
                <?php
                    $badge = 'bg-slate-200 text-slate-700';
                    if ($o['status']==='completed') $badge = 'bg-green-100 text-green-700';
                    elseif ($o['status']==='shipping') $badge = 'bg-blue-100 text-blue-700';
                    elseif ($o['status']==='processing') $badge = 'bg-amber-100 text-amber-700';
                    elseif ($o['status']==='cancelled') $badge = 'bg-red-100 text-red-700';
                    $labels = [
                        'pending' => 'Chờ xác nhận',
                        'processing' => 'Đang xử lý',
                        'shipping' => 'Đang giao',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy'
                    ];
                    $label = $labels[$o['status']] ?? $o['status'];
                    $paymentLabel = [
                        'cod' => 'Khi nhận hàng',
                        'vnpay' => 'VNPay',
                        'momo' => 'MoMo'
                    ][$o['payment_method'] ?? 'cod'] ?? 'Khác';
                ?>
                <tr class="border-b hover:bg-slate-50">
                <td class="p-3 font-medium"><?php echo $o['code']; ?></td>
                <td class="p-3"><?php echo $o['customer_name']; ?></td>
                    <td class="p-3 text-blue-600"><?php echo number_format($o['total_amount']); ?></td>
                    <td class="p-3 text-slate-600 text-sm"><?php echo $paymentLabel; ?></td>
                    <td class="p-3"><span class="px-2 py-1 rounded <?php echo $badge; ?>"><?php echo $label; ?></span></td>
                    <?php
                        $dt = new DateTime($o['created_at'], new DateTimeZone('Asia/Ho_Chi_Minh'));
                        $displayTime = $dt->format('d/m/Y H:i');
                    ?>
                    <td class="p-3 text-slate-600 text-sm"><?php echo $displayTime; ?></td>
                    <td class="p-3">
                        <form method="post" action="<?php echo base_url('admin.php/orders/update-status/' . $o['id']); ?>" class="flex gap-2">
                            <select class="px-3 py-2 border rounded text-sm" name="status">
                                <?php foreach (['pending','processing','shipping','completed','cancelled'] as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo $o['status']===$s?'selected':''; ?>><?php echo $labels[$s]; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="px-3 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Lưu</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
