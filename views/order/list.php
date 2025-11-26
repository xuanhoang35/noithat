<?php ob_start(); ?>
<div class="space-y-3">
    <?php if (!empty($_SESSION['order_notice'])): ?>
        <div data-order-notice class="p-3 bg-emerald-50 text-emerald-700 rounded border border-emerald-100 transition-opacity duration-500">
            <?php echo $_SESSION['order_notice']; unset($_SESSION['order_notice']); ?>
        </div>
    <?php endif; ?>
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-slate-900 mb-0">Đơn hàng của tôi</h1>
        <span class="text-slate-500 text-sm">Nếu có vấn đề, gửi khiếu nại cho đơn tương ứng.</span>
    </div>
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <tr class="bg-slate-100 text-left">
                <th class="p-3">Mã</th><th class="p-3">Thanh toán</th><th class="p-3">Trạng thái</th><th class="p-3">Tổng</th><th class="p-3">Thời gian</th><th class="p-3">Khiếu nại</th>
            </tr>
            <?php foreach ($orders as $o): ?>
            <?php
                $badge = 'bg-slate-200 text-slate-700';
                if ($o['status']==='completed') $badge = 'bg-green-100 text-green-700';
                elseif ($o['status']==='shipping') $badge = 'bg-blue-100 text-blue-700';
                elseif ($o['status']==='processing') $badge = 'bg-amber-100 text-amber-700';
                elseif ($o['status']==='cancelled') $badge = 'bg-red-100 text-red-700';
                $statusLabels = [
                    'pending' => 'Chờ xác nhận',
                    'processing' => 'Đang xử lý',
                    'shipping' => 'Đang giao',
                    'completed' => 'Hoàn thành',
                    'cancelled' => 'Đã hủy'
                ];
                $statusLabel = $statusLabels[$o['status']] ?? $o['status'];
                $paymentLabels = [
                    'cod' => 'Khi nhận hàng',
                    'vnpay' => 'VNPay',
                    'momo' => 'MoMo'
                ];
                $paymentLabel = $paymentLabels[$o['payment_method'] ?? 'cod'] ?? 'Khác';
            ?>
            <tr class="border-b hover:bg-slate-50">
                <td class="p-3 font-medium"><?php echo $o['code']; ?></td>
                <td class="p-3 text-sm text-slate-600"><?php echo $paymentLabel; ?></td>
                <td class="p-3"><span class="px-2 py-1 rounded <?php echo $badge; ?>"><?php echo $statusLabel; ?></span></td>
                <td class="p-3 text-blue-600 font-semibold"><?php echo number_format($o['total_amount']); ?> đ</td>
                <td class="p-3 text-slate-600"><?php echo $o['created_at']; ?></td>
                <td class="p-3">
                    <a class="px-3 py-1.5 text-sm rounded bg-red-50 text-red-600 hover:bg-red-100" href="<?php echo base_url('complaints/create/' . $o['id']); ?>">Khiếu nại</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var notice = document.querySelector('[data-order-notice]');
    if (!notice) return;
    setTimeout(function(){
        notice.classList.add('opacity-0');
        setTimeout(function(){ notice.remove(); }, 600);
    }, 2000);
});
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
