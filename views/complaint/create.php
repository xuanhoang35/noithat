<?php ob_start(); ?>
<?php $orderStatusLabels = [
    'pending' => 'Chờ xác nhận',
    'processing' => 'Đang xử lý',
    'shipping' => 'Đang giao',
    'completed' => 'Hoàn thành',
    'cancelled' => 'Đã hủy'
]; ?>
<div class="bg-white rounded shadow-sm p-4 max-w-xl">
    <h1 class="text-xl font-semibold mb-2">Gửi khiếu nại</h1>
    <p class="text-sm text-slate-500 mb-3">Mã đơn: <?php echo $order['code']; ?> · Trạng thái: <?php echo $orderStatusLabels[$order['status']] ?? $order['status']; ?></p>
    <?php if (!empty($error)) echo '<div class="p-3 mb-2 bg-red-50 text-red-600 rounded">'.$error.'</div>'; ?>
    <form method="post" class="grid gap-2">
        <input class="px-3 py-2 border rounded" name="title" placeholder="Tiêu đề" required>
        <textarea class="px-3 py-2 border rounded" name="content" rows="4" placeholder="Nội dung khiếu nại" required></textarea>
        <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Gửi khiếu nại</button>
    </form>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
