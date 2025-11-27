<?php ob_start(); ?>
<?php
    $method = $method ?? 'vnpay';
    $orderCode = htmlspecialchars($order['code']);
    $formattedAmount = number_format($order['total_amount']) . ' đ';
    $qrPlaceholder = asset_url('public/assets/img/placeholder.svg');
    $vnpayLogo = asset_url('public/assets/img/vnpay-logo.png');
    $vnpayQr = asset_url('public/bank/Vnpay.jpg');
    $momoLogo = asset_url('public/assets/img/momo-logo.png');
    $momoQr = asset_url('public/bank/Momo.jpg');
?>

<?php if ($method === 'vnpay'): ?>
    <div class="max-w-5xl mx-auto bg-white rounded-3xl shadow border border-[#dde3f0] overflow-hidden text-slate-700">
        <div class="px-6 py-3 bg-[#edf6ff] text-[#2f4c7c] text-sm border-b border-[#d8e6f6] flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-white text-xs text-[#d9534f] border border-[#d9534f]">!</span>
            Quý khách vui lòng không tắt trình duyệt cho đến khi nhận được kết quả giao dịch trên website. Xin cảm ơn!
        </div>
        <div class="grid md:grid-cols-2">
            <div class="p-6 space-y-4 border-r border-[#e6ecf5]">
                <div class="text-center">
                    <img src="<?php echo $vnpayLogo; ?>" onerror="this.style.display='none'" alt="VNPay" class="mx-auto h-8 object-contain mb-2">
                    <p class="text-sm tracking-wide text-[#4b5d7a]">Ứng dụng mobile quét mã</p>
                    <h2 class="text-2xl font-semibold text-[#d81f26]">VN<span class="text-[#1d6cb0]">PAY</span><span class="text-[#d81f26] text-base">QR</span></h2>
                </div>
                <div class="flex justify-center">
                    <div class="border-4 border-[#d5e5f7] rounded-[32px] p-4 bg-white shadow-inner">
                        <div class="w-56 h-56 bg-[#f5f7fb] rounded-2xl flex items-center justify-center">
                            <img src="<?php echo $vnpayQr; ?>" alt="VNPay QR" class="w-48 h-48 object-cover rounded-xl">
                        </div>
                    </div>
                </div>
                <div class="text-center space-y-1">
                    <p class="text-[#1d6cb0] font-semibold text-lg">Scan to Pay</p>
                    <p class="text-xs text-[#7b88a1]">Thanh toán trực tuyến</p>
                    <p class="text-2xl font-bold text-[#2a3241]"><?php echo $formattedAmount; ?></p>
                    <p class="text-xs text-[#9aa5be]">Đơn hàng: <?php echo $orderCode; ?></p>
                    <a class="text-xs text-[#1d6cb0] hover:underline" href="#">Hướng dẫn thanh toán?</a>
                </div>
                <div class="flex justify-center">
                    <div class="text-xs text-[#9aa5be] flex items-center gap-2">
                        <span class="inline-block w-4 h-[1px] bg-[#d1d8e6]"></span>Hoặc<span class="inline-block w-4 h-[1px] bg-[#d1d8e6]"></span>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <form method="post" action="<?php echo base_url('payment/vnpay/' . $order['id']); ?>">
                        <input type="hidden" name="result" value="success">
                        <button class="w-full px-4 py-3 bg-[#1fb655] text-white rounded-lg hover:bg-[#19a04a] font-semibold">Thanh toán thành công</button>
                    </form>
                    <form method="post" action="<?php echo base_url('payment/vnpay/' . $order['id']); ?>">
                        <input type="hidden" name="result" value="failed">
                        <button class="w-full px-4 py-3 bg-[#e7ecf4] text-[#4a5568] rounded-lg hover:bg-[#dce3ef] font-semibold">Hủy</button>
                    </form>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <h3 class="text-sm font-semibold text-[#1c2d41] uppercase">Sử dụng Mobile Banking hỗ trợ VNPayQR</h3>
                <div class="grid grid-cols-3 gap-2 text-xs text-center h-[380px] overflow-y-auto pr-2">
                    <?php
                        $banks = ['Vietcombank','Agribank','BIDV','VietinBank','VPBank','MSB','SCB','ABBANK','Techcombank','MB Bank','VIB','TPBank','OCB','HDBank','Bac A Bank','NCB','Eximbank','Nam A Bank','OceanBank','Saigonbank','VietCapital','BaoViet Bank','PVcomBank','BIDC'];
                        foreach ($banks as $bank): ?>
                        <div class="border border-[#e6ecf5] rounded-lg py-3 px-2 bg-white shadow-sm text-[#3c4c63]"><?php echo $bank; ?></div>
                    <?php endforeach; ?>
                </div>
                <p class="text-xs text-[#9aa5be] text-center">Phát triển bởi VNPAY © 2025</p>
                <div class="flex justify-center gap-3 text-[10px] text-[#a0acbf]">
                    <span class="px-3 py-1 border border-[#d6deec] rounded-full">Secure GlobalSign</span>
                    <span class="px-3 py-1 border border-[#d6deec] rounded-full">Trustwave</span>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="max-w-5xl mx-auto bg-white rounded-3xl shadow border border-[#f0cfe3] overflow-hidden">
        <div class="px-6 py-4 flex items-center gap-3 border-b border-[#f4d7e7]">
            <img src="<?php echo $momoLogo; ?>" onerror="this.style.display='none'" alt="MoMo" class="h-10">
            <div>
                <p class="text-xs uppercase text-[#b5086b] tracking-widest">Cổng thanh toán MoMo</p>
                <p class="text-sm text-[#d23d8c]">Quét mã QR để thanh toán</p>
            </div>
        </div>
        <div class="grid md:grid-cols-2">
            <div class="bg-[#fdf2f8] p-6 space-y-5">
                <h3 class="text-sm font-semibold text-[#c50f76] uppercase">Thông tin đơn hàng</h3>
                <div class="space-y-3 text-sm text-[#6d2c51]">
                    <div>
                        <p class="text-xs uppercase text-[#d38bae]">Nhà cung cấp</p>
                        <p class="font-semibold">Nội Thất Store - Cửa hàng nội thất và thiết bị gia dụng</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-[#d38bae]">Mã đơn hàng</p>
                        <p><?php echo $orderCode; ?></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-[#d38bae]">Số tiền</p>
                        <p class="text-2xl font-semibold text-[#c50f76]"><?php echo $formattedAmount; ?></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-[#d38bae]">Đơn hàng sẽ hết hạn sau</p>
                        <div class="flex gap-2 text-center">
                            <div class="bg-white rounded-lg px-3 py-2 shadow text-[#c50f76]">
                                <p class="text-lg leading-none font-semibold">09</p>
                                <p class="text-[10px] uppercase">Phút</p>
                            </div>
                            <div class="bg-white rounded-lg px-3 py-2 shadow text-[#c50f76]">
                                <p class="text-lg leading-none font-semibold">55</p>
                                <p class="text-[10px] uppercase">Giây</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-sm text-[#b5086b]">
                    <a class="underline" href="<?php echo base_url('orders'); ?>">Quay về đơn hàng</a>
                </div>
            </div>
            <div class="p-6 space-y-5 text-center bg-gradient-to-br from-[#f52e94] via-[#db007c] to-[#b80063] text-white">
                <h2 class="text-xl font-semibold">Quét mã QR để thanh toán</h2>
                <div class="flex justify-center">
                    <div class="bg-white p-4 rounded-3xl shadow-inner">
                        <div class="w-56 h-56 bg-[#f0f0f0] rounded-2xl flex items-center justify-center">
                            <img src="<?php echo $momoQr; ?>" alt="MoMo QR" class="w-48 h-48 object-cover rounded-xl">
                        </div>
                    </div>
                </div>
                <p class="text-sm text-white/80">Sử dụng App MoMo hoặc ứng dụng Camera hỗ trợ QR code để quét mã.</p>
                <div class="grid sm:grid-cols-2 gap-3 text-[#b80063]">
                    <form method="post" action="<?php echo base_url('payment/momo/' . $order['id']); ?>">
                        <input type="hidden" name="result" value="success">
                        <button class="w-full px-4 py-3 bg-white rounded-lg font-semibold hover:bg-[#f7e3ee]">Thanh toán thành công</button>
                    </form>
                    <form method="post" action="<?php echo base_url('payment/momo/' . $order['id']); ?>">
                        <input type="hidden" name="result" value="failed">
                        <button class="w-full px-4 py-3 bg-white/80 rounded-lg font-semibold hover:bg-white">Thanh toán thất bại</button>
                    </form>
                </div>
                <div class="text-xs text-white/80">
                    Gặp khó khăn khi thanh toán? <a class="underline" href="#">Xem hướng dẫn</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
