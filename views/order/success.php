<?php ob_start(); ?>
<?php
    $paymentLabels = [
        'cod' => 'Thanh toán khi nhận hàng',
        'vnpay' => 'VNPay',
        'momo' => 'MoMo'
    ];
?>
<div class="text-center py-5 bg-white rounded shadow-sm">
    <h1 class="text-2xl font-bold text-green-600 mb-3">Đặt hàng thành công</h1>
    <p class="mb-1">Mã đơn: <strong><?php echo $orderId; ?></strong>. Cảm ơn bạn!</p>
    <p class="text-sm text-slate-500 mb-3">Phương thức thanh toán: <?php echo $paymentLabels[$paymentMethod ?? 'cod'] ?? 'Thanh toán khi nhận hàng'; ?></p>
    <?php if (!empty($voucherMessage)): ?>
        <p class="text-emerald-600 mb-3"><?php echo $voucherMessage; ?></p>
    <?php endif; ?>
    <div class="flex items-center justify-center gap-3">
        <a class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" href="<?php echo base_url('orders'); ?>">Xem đơn hàng</a>
        <a class="px-4 py-2 border border-blue-600 text-blue-600 rounded hover:bg-blue-50" href="<?php echo base_url('products'); ?>">Tiếp tục mua hàng</a>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
