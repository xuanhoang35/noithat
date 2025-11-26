<?php
$orderCode = htmlspecialchars($order['code']);
$amountFormatted = number_format($order['total_amount'], 0, ',', '.') . ' VNĐ';
$qrImage = asset_url('public/bank/Vnpay.jpg');
$logo = asset_url('public/bank/vnpay-logo.png');
$banks = [
    'Vietcombank','Agribank','BIDV','VPBank','VietinBank','MSB','SCB','ABBANK','TVB','NCB',
    'SHB','VIB','TPBank','VietCapital','VCBPAY','EXIMBANK','Nam A Bank','Bac A Bank','MB','OCB',
    'HDBank','Woori','PVcomBank','BIDV Online'
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng thanh toán VNPay</title>
    <style>
html, body {margin:0 !important;padding:0 !important;background:#f2f6fb;}
body {font-family:"Helvetica Neue", Arial, sans-serif;color:#1d2a3f;padding-top:80px;}
        header {
            background:#fff;
            border-bottom:1px solid #e1e8f4;
            padding:8px 32px 10px;
            display:flex;
            align-items:center;
            gap:16px;
            position:fixed;
            top:0;
            left:0;
            right:0;
            z-index:999;
        }
        header img { height:72px; margin-top:-8px; margin-bottom:-10px; }
        header h1 { margin:0;font-size:18px;color:#1d3e87;font-weight:600; }
        .container {
            max-width: 980px;
            margin: 40px auto 30px;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 15px 45px rgba(82,115,166,0.25);
            border: 1px solid #e1e7f2;
            padding: 28px 32px 38px;
        }
        .notice {
            background:#ebf6ff;
            border:1px solid #d2e6fb;
            color:#326293;
            padding:12px 16px;
            border-radius:10px;
            font-size:13px;
            margin-bottom:20px;
        }
        .content {
            display:grid;
            grid-template-columns: 420px 1fr;
            gap:32px;
        }
        .qr-panel {
            border-right:1px solid #edf0f7;
            padding-right:24px;
        }
        .qr-panel h2 {
            text-align:center;
            font-weight:600;
            margin-top:0;
            color:#283b55;
        }
        .qr-panel h1 {
            text-align:center;
            color:#d21d2b;
            font-size:28px;
            margin:6px 0 14px;
        }
        .qr-box {
            border:2px solid #d8e7fa;
            border-radius:18px;
            padding:18px;
            text-align:center;
            background:#f5f8fd;
        }
        .qr-box img {
            width:280px;
            height:280px;
            object-fit:cover;
            border-radius:10px;
        }
        .qr-panel .scan-label{
            color:#1d6db9;
            font-weight:600;
            text-align:center;
            margin:10px 0 4px;
        }
        .qr-panel .amount{
            text-align:center;
            font-size:26px;
            font-weight:700;
            margin:0;
        }
        .qr-panel .order{
            text-align:center;
            font-size:12px;
            color:#8390a7;
        }
        .banks h3 {
            margin:0 0 12px;
            font-size:14px;
            font-weight:600;
            text-transform:uppercase;
            color:#1f3b68;
        }
        .bank-grid{
            height:310px;
            overflow:auto;
            border:1px solid #e6ebf5;
            border-radius:12px;
            padding:12px;
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:10px;
            background:#fbfdff;
        }
        .bank{
            background:#fff;
            border:1px solid #e1e7f3;
            border-radius:10px;
            text-align:center;
            font-size:12px;
            color:#3b4d6d;
            padding:10px 6px;
            box-shadow:0 5px 15px rgba(45,83,150,0.08);
        }
        .actions{
            margin-top:18px;
            text-align:center;
        }
        .actions button{
            background:#e5eaf3;
            border:none;
            border-radius:8px;
            padding:10px 24px;
            cursor:pointer;
            color:#3d4f6b;
            font-weight:600;
        }
        .options-line{
            text-align:center;
            color:#9aa5bb;
            font-size:12px;
            margin:18px 0 6px;
        }
        footer{
            background:#fff;
            border-top:1px solid #e1e8f4;
            padding:16px 40px;
            color:#7a8daa;
            display:flex;
            justify-content:space-between;
            align-items:center;
            font-size:12px;
        }
        footer .badges{
            display:flex;
            gap:10px;
        }
        footer span.badge{
            border:1px solid #d4ddec;
            padding:4px 10px;
            border-radius:10px;
        }
        .result-buttons{
            margin-top:12px;
            display:flex;
            justify-content:center;
            gap:10px;
        }
        .result-buttons form { flex:0 0 auto; }
        .result-buttons button{
            border:none;
            padding:8px 18px;
            border-radius:8px;
            cursor:pointer;
            font-weight:600;
        }
        .result-success{ background:#1fb655;color:#fff; }
        .result-fail{ background:#f2f5fb;color:#3f4e68; }
    </style>
</head>
<body>
    <header>
        <img src="<?php echo $logo; ?>" alt="VNPay" onerror="this.style.display='none'">
        <h1>Cổng thanh toán VNPay</h1>
    </header>
    <div class="container">
        <div class="notice">Quý khách vui lòng không tắt trình duyệt cho đến khi nhận được kết quả giao dịch trên website. Xin cảm ơn!</div>
        <div class="content">
            <div class="qr-panel">
                <h2>Ứng dụng mobile quét mã</h2>
                <h1>VN<span style="color:#1d6cb0;">PAY</span><span style="color:#d21d2b;">QR</span></h1>
                <div class="qr-box">
                    <img src="<?php echo $qrImage; ?>" alt="VNPay QR">
                </div>
                <p class="scan-label">Scan to Pay</p>
                <p class="order">Thanh toán trực tuyến</p>
                <p class="amount"><?php echo $amountFormatted; ?></p>
                <p class="order">Đơn hàng: <?php echo $orderCode; ?></p>
                <div class="options-line">Hoặc</div>
                <div class="actions">
                    <form method="post" action="<?php echo base_url('payment/vnpay/' . $order['id']); ?>">
                        <input type="hidden" name="result" value="failed">
                        <button>Hủy</button>
                    </form>
                </div>
                <div class="result-buttons">
                    <form method="post" action="<?php echo base_url('payment/vnpay/' . $order['id']); ?>">
                        <input type="hidden" name="result" value="success">
                        <button class="result-success">Xác nhận đã thanh toán</button>
                    </form>
                    <form method="post" action="<?php echo base_url('payment/vnpay/' . $order['id']); ?>">
                        <input type="hidden" name="result" value="failed">
                        <button class="result-fail">Hủy / Thanh toán thất bại</button>
                    </form>
                </div>
            </div>
            <div class="banks">
                <h3>Sử dụng Mobile Banking hỗ trợ VNPayQR</h3>
                <div class="bank-grid">
                    <?php foreach ($banks as $bank): ?>
                        <div class="bank"><?php echo $bank; ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <span>© 2025 - Cổng thanh toán VNPay</span>
        <div class="badges">
            <span class="badge">Secure GlobalSign</span>
            <span class="badge">Trustwave</span>
        </div>
    </footer>
</body>
</html>
