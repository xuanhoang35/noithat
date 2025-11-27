<?php
$orderCode = htmlspecialchars($order['code']);
$amountFormatted = number_format($order['total_amount'], 0, ',', '.') . 'đ';
$qrImage = asset_url('public/bank/Momo.jpg');
$logo = asset_url('public/bank/momo-logo.png');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng thanh toán MoMo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing:border-box; }
        html, body {
            margin:0 !important;
            padding:0 !important;
            background:#fff6fb;
        }
        body {
            font-family: 'Inter', sans-serif;
            color:#4c1e38;
        }
        .page {
            min-height:100vh;
            display:flex;
            flex-direction:column;
        }
        header {
            padding:10px 32px 12px;
            display:flex;
            align-items:center;
            gap:16px;
            border-bottom:1px solid #ffe0f0;
            background:#fff;
            position:fixed;
            top:0;
            left:0;
            right:0;
            z-index:20;
        }
header img { height:40px; }
header h1 {
    font-size:18px;
    font-weight:600;
    color:#b00069;
    margin:0;
        }
        main {
            flex:1;
            display:flex;
            justify-content:center;
            padding:40px 24px;
            margin-top:70px;
        }
        .content {
            max-width:1100px;
            width:100%;
            display:grid;
            grid-template-columns:360px 1fr;
            gap:24px;
        }
        .card {
            background:#fff;
            border-radius:20px;
            box-shadow:0 10px 25px rgba(176,0,105,0.12);
            padding:28px;
        }
        .card h2 {
            font-size:18px;
            margin:0 0 16px;
            color:#b00069;
        }
        .info-row {
            display:flex;
            justify-content:space-between;
            margin-bottom:12px;
            font-size:14px;
        }
        .info-row strong { color:#2d0c1f; }
        .amount {
            font-size:26px;
            font-weight:700;
            color:#b00069;
            margin-top:8px;
        }
        .timer-card {
            margin-top:20px;
            background:#ffe7f3;
            border-radius:16px;
            padding:18px;
            text-align:center;
            color:#b00069;
        }
        .timer {
            display:flex;
            justify-content:center;
            gap:12px;
            margin-top:10px;
        }
        .timer-box {
            background:#fff;
            border-radius:12px;
            padding:10px 16px;
            box-shadow:0 8px 20px rgba(176,0,105,0.14);
        }
        .timer-box span {
            display:block;
            font-size:22px;
            font-weight:600;
        }
        .timer-box label {
            display:block;
            font-size:11px;
            text-transform:uppercase;
            color:#c05690;
        }
        .qr-card {
            background:linear-gradient(140deg,#ff36a8,#b00069);
            border-radius:24px;
            padding:36px 48px;
            color:#fff;
            position:relative;
            overflow:hidden;
            box-shadow:0 20px 40px rgba(176,0,105,0.25);
        }
        .qr-card > * { position:relative; }
        .qr-card h2 {
            margin:0 0 20px;
            font-size:20px;
            font-weight:600;
        }
        .qr-box {
            background:#fff;
            border-radius:24px;
            padding:20px;
            display:flex;
            justify-content:center;
            margin-bottom:16px;
        }
        .qr-box img {
            width:280px;
            height:280px;
            object-fit:cover;
            border-radius:20px;
        }
        .note {
            font-size:13px;
            line-height:1.5;
        }
        footer {
            padding:24px 40px;
            border-top:1px solid #ffe0f0;
            font-size:13px;
            color:#9c5578;
            display:flex;
            justify-content:space-between;
            background:#fff;
        }
        .actions {
            display:flex;
            gap:12px;
            margin-top:20px;
        }
        .actions form { flex:1; }
        .btn {
            width:100%;
            border:none;
            padding:12px 0;
            font-weight:600;
            border-radius:12px;
            cursor:pointer;
        }
        .btn-success {
            background:#ffffff;
            color:#b00069;
            box-shadow:0 8px 20px rgba(255,255,255,0.3);
        }
        .btn-fail {
            background:#fdd7e8;
            color:#6d1f41;
        }
        @media(max-width:960px){
            .content { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
    <div class="page">
        <header>
            <img src="<?php echo $logo; ?>" alt="MoMo">
            <h1>Cổng thanh toán MoMo</h1>
        </header>
        <main>
            <div class="content">
                <div>
                    <div class="card">
                        <h2>Thông tin đơn hàng</h2>
                        <div class="info-row"><span>Nhà cung cấp</span><strong>Nội Thất Store - Cửa hàng nội thất và thiết bị gia dụng</strong></div>
                        <div class="info-row"><span>Mã đơn hàng</span><strong><?php echo $orderCode; ?></strong></div>
                        <div class="info-row"><span>Mô tả</span><strong><?php echo $orderCode; ?></strong></div>
                        <div class="info-row"><span>Số tiền</span></div>
                        <div class="amount"><?php echo $amountFormatted; ?></div>
                    </div>
                    <div class="timer-card">
                        <div>Đơn hàng sẽ hết hạn sau:</div>
                        <div class="timer">
                            <div class="timer-box">
                                <span id="timer-min">10</span>
                                <label>Phút</label>
                            </div>
                            <div class="timer-box">
                                <span id="timer-sec">00</span>
                                <label>Giây</label>
                            </div>
                        </div>
                        <div style="margin-top:10px;font-size:12px;"><a href="<?php echo base_url('orders'); ?>" style="color:#b00069;text-decoration:none;">Quay về</a></div>
                    </div>
                </div>
                <div class="qr-card">
                    <h2>Quét mã QR để thanh toán</h2>
                    <div class="qr-box">
                        <img src="<?php echo $qrImage; ?>" alt="MoMo QR">
                    </div>
                    <p class="note">Sử dụng App MoMo hoặc ứng dụng camera hỗ trợ QR code để quét mã. Gặp khó khăn khi thanh toán? <a style="color:#ffdd57;text-decoration:none;font-weight:600;" href="#">Xem hướng dẫn</a></p>
                    <div class="actions">
                        <form method="post" action="<?php echo base_url('payment/momo/' . $order['id']); ?>">
                            <input type="hidden" name="result" value="success">
                            <button class="btn btn-success">Thanh toán thành công</button>
                        </form>
                        <form method="post" action="<?php echo base_url('payment/momo/' . $order['id']); ?>">
                            <input type="hidden" name="result" value="failed">
                            <button class="btn btn-fail">Hủy / Thanh toán thất bại</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
        <footer>
            <span>© 2025 - Cổng thanh toán MoMo</span>
            <span>Hỗ trợ khách hàng: 0974 73 66 68 (không giới hạn/phút) • huyendothi.79@momo.vn</span>
        </footer>
    </div>
    <script>
        let remaining = 600;
        const minEl = document.getElementById('timer-min');
        const secEl = document.getElementById('timer-sec');
        const timer = setInterval(() => {
            remaining = Math.max(0, remaining - 1);
            const m = Math.floor(remaining / 60);
            const s = remaining % 60;
            minEl.textContent = String(m).padStart(2, '0');
            secEl.textContent = String(s).padStart(2, '0');
            if (remaining <= 0) clearInterval(timer);
        }, 1000);
    </script>
</body>
</html>
