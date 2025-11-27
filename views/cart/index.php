<?php ob_start(); ?>
<div class="space-y-6">
    <div class="glass-panel rounded-3xl p-5 shadow-xl flex items-center justify-between flex-wrap gap-3">
        <div>
            <p class="section-title text-slate-500">Giỏ hàng</p>
            <h1 class="text-2xl font-bold text-slate-900">Sẵn sàng thanh toán</h1>
        </div>
        <div class="flex gap-2 text-xs text-slate-600">
            <span class="px-3 py-2 rounded-full bg-white/70 border border-slate-200">Bước 1: Kiểm tra sản phẩm</span>
            <span class="px-3 py-2 rounded-full bg-white/70 border border-slate-200">Bước 2: Nhập thông tin & thanh toán</span>
        </div>
    </div>

    <?php if (!empty($_SESSION['cart_error'])): ?>
        <div class="p-3 bg-red-50 text-red-600 rounded"><?php echo $_SESSION['cart_error']; unset($_SESSION['cart_error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['cart_success'])): ?>
        <div class="p-3 bg-emerald-50 text-emerald-700 rounded"><?php echo $_SESSION['cart_success']; unset($_SESSION['cart_success']); ?></div>
    <?php endif; ?>
    <?php $prefillVoucher = $_SESSION['cart_voucher_code'] ?? ''; ?>
    <?php $appliedVoucher = $_SESSION['cart_voucher'] ?? null; ?>
    <?php $hasStockIssue = $hasStockIssue ?? false; $stockWarnings = $stockWarnings ?? []; ?>

    <?php if (empty($cart)): ?>
        <div class="p-4 bg-blue-50 text-blue-700 rounded">Chưa có sản phẩm.</div>
    <?php else: ?>
    <div class="grid lg:grid-cols-3 gap-4">
        <?php
            $voucherPercent = !empty($appliedVoucher) ? (int)$appliedVoucher['discount_percent'] : 0;
            $voucherCategory = $appliedVoucher['category_id'] ?? null;
            $sum = 0;
            $discountTotal = 0;
            $cartRows = [];
            foreach ($cart as $id => $item) {
                $line = $item['qty'] * $item['product']['price'];
                $sum += $line;
                $isEligible = !empty($appliedVoucher) && (empty($voucherCategory) || (int)$item['product']['category_id'] === (int)$voucherCategory);
                $lineDiscount = ($isEligible && $voucherPercent > 0) ? round($line * $voucherPercent / 100) : 0;
                $discountTotal += $lineDiscount;
                $cartRows[] = [
                    'id' => $id,
                    'item' => $item,
                    'line' => $line,
                    'discount' => $lineDiscount,
                    'final' => $line - $lineDiscount,
                    'eligible' => $isEligible
                ];
            }
        ?>
        <div class="lg:col-span-2 glass-panel rounded-3xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <tr class="bg-slate-100 text-left text-slate-700">
                        <th class="p-3">Sản phẩm</th>
                        <th class="p-3">SL</th>
                        <th class="p-3">Tồn</th>
                        <th class="p-3">Giá</th>
                        <th class="p-3">% giảm</th>
                        <th class="p-3">Tiền giảm</th>
                        <th class="p-3">Tổng</th>
                        <th class="p-3"></th>
                    </tr>
                    <?php foreach($cartRows as $row): $item = $row['item']; $product = $item['product']; $available = (int)($product['stock'] ?? 0); $warn = $stockWarnings[$row['id']] ?? null; ?>
                        <tr class="border-b hover:bg-slate-50">
                            <td class="p-3 font-medium text-slate-800">
                                <?php echo $product['name']; ?>
                                <?php if ($warn): ?>
                                    <span class="block text-xs text-red-600 mt-1"><?php echo $warn; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3"><?php echo $item['qty']; ?></td>
                            <td class="p-3">
                                <span class="inline-flex px-2 py-1 rounded-full text-xs <?php echo $available > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'; ?>">
                                    <?php echo $available > 0 ? $available . ' còn' : 'Hết'; ?>
                                </span>
                            </td>
                            <td class="p-3 text-blue-600 font-semibold"><?php echo number_format($product['price']); ?> đ</td>
                            <td class="p-3">
                                <?php if (empty($appliedVoucher)): ?>
                                    <span class="text-slate-400">—</span>
                                <?php elseif ($row['eligible']): ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-600">-<?php echo $voucherPercent; ?>%</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-500">Không áp dụng</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3 text-red-500 font-medium">
                                <?php echo $row['discount'] > 0 ? '-'.number_format($row['discount']).' đ' : '—'; ?>
                            </td>
                            <td class="p-3 font-semibold">
                                <?php echo number_format($row['final']); ?> đ
                                <?php if ($row['discount'] > 0): ?>
                                    <span class="block text-xs text-slate-400 line-through"><?php echo number_format($row['line']); ?> đ</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3">
                                <form method="post" action="<?php echo base_url('cart/remove'); ?>">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button class="px-3 py-1 text-sm rounded bg-red-50 text-red-600 hover:bg-red-100">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <div class="glass-panel rounded-3xl shadow-lg p-4 lg:sticky lg:top-24 h-fit">
            <div class="flex items-center justify-between mb-3">
                <h5 class="text-lg font-semibold">Tóm tắt</h5>
                <div class="text-lg font-bold text-blue-600"><?php echo number_format($sum); ?> đ</div>
            </div>
            <?php
                $discount = $discountTotal;
                $totalDue = max(0, $sum - $discount);
            ?>
            <form method="post" action="<?php echo base_url('cart/apply-voucher'); ?>" class="flex gap-2 mb-3">
                <input class="px-3 py-2 border rounded-lg w-full" name="voucher_code" placeholder="Mã giảm giá" value="<?php echo htmlspecialchars($appliedVoucher['code'] ?? $prefillVoucher); ?>">
                <button class="px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800">Áp dụng</button>
            </form>
            <?php if (!empty($appliedVoucher)): ?>
                <div class="text-sm text-emerald-600 mb-3">Đã áp dụng mã: <strong><?php echo htmlspecialchars($appliedVoucher['code']); ?></strong> (-<?php echo $appliedVoucher['discount_percent']; ?>%)</div>
            <?php endif; ?>
            <div class="space-y-2 text-sm mb-4">
                <div class="flex justify-between"><span>Tạm tính</span><span><?php echo number_format($sum); ?> đ</span></div>
                <?php if ($discount > 0): ?><div class="flex justify-between text-red-500"><span>Giảm giá</span><span>-<?php echo number_format($discount); ?> đ</span></div><?php endif; ?>
                <div class="flex justify-between text-base font-semibold text-blue-600"><span>Cần thanh toán</span><span><?php echo number_format($totalDue); ?> đ</span></div>
            </div>
            <?php if ($hasStockIssue): ?>
                <div class="p-3 mb-3 bg-red-50 border border-red-100 text-red-700 text-sm rounded">
                    Giỏ hàng có sản phẩm hết hàng/vượt tồn kho. Vui lòng liên hệ để được hỗ trợ hoặc cập nhật giỏ hàng.
                </div>
                <a href="tel:0974734668" class="w-full inline-flex items-center justify-center px-4 py-3 bg-amber-500 text-white rounded-lg hover:bg-amber-600 font-semibold">Liên hệ</a>
            <?php else: ?>
                <form method="post" action="<?php echo base_url('order/checkout'); ?>" class="grid grid-cols-1 gap-3">
                    <input class="px-3 py-3 border rounded-lg w-full" name="name" placeholder="Họ tên" required maxlength="30">
                    <input class="px-3 py-3 border rounded-lg w-full" name="phone" placeholder="SĐT" required maxlength="10" pattern="0[0-9]{9}" title="Chỉ nhập số, bắt đầu 0 và đủ 10 số">
                    <input class="px-3 py-3 border rounded-lg w-full" name="email" placeholder="Email" required maxlength="30" pattern="[A-Za-z0-9._%+\-]+@(gmail|email)[A-Za-z0-9.\-]*\.[A-Za-z0-9.\-]+" title="Chỉ chữ không dấu/số, chứa @gmail hoặc @email, tối đa 30 ký tự">
                    <input class="px-3 py-3 border rounded-lg w-full" name="address" placeholder="Địa chỉ" required maxlength="255">
                    <div class="space-y-2">
                        <p class="text-sm font-semibold text-slate-700">Phương thức thanh toán</p>
                        <label class="flex items-center gap-2 px-3 py-2 border rounded-lg cursor-pointer hover:border-blue-500">
                            <input type="radio" name="payment_method" value="cod" class="text-blue-600" checked>
                            <span>Thanh toán khi nhận hàng</span>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-2 border rounded-lg cursor-pointer hover:border-blue-500">
                            <input type="radio" name="payment_method" value="vnpay" class="text-blue-600">
                            <span>Thanh toán qua VNPay</span>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-2 border rounded-lg cursor-pointer hover:border-blue-500">
                            <input type="radio" name="payment_method" value="momo" class="text-blue-600">
                            <span>Thanh toán qua MoMo</span>
                        </label>
                    </div>
                    <textarea class="px-3 py-3 border rounded-lg w-full" name="note" placeholder="Ghi chú"></textarea>
                    <button class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">Đặt hàng</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
