<?php ob_start(); ?>
<style>
html, body {
    height: 100%;
    margin: 0;
    background-color: #fff;
}
.payment-banner-full {
    position: fixed;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 999;
}
.payment-banner-full img {
    width: 100vw;
    height: 100vh;
    object-fit: cover;
    padding: 0;
}
.payment-banner-full::after {
    content: 'Đang chuyển đến trang thanh toán...';
    position: absolute;
    bottom: 40px;
    font-size: 14px;
    letter-spacing: 0.05em;
}
</style>
<?php 
    $isMomo = $method === 'momo';
    $bgColor = $isMomo ? '#f9e9f1' : '#0057c2';
    $maxWidth = $isMomo ? '85%' : '100%';
    $maxHeight = $isMomo ? '85%' : '100%';
    $textColor = $isMomo ? 'rgba(0,0,0,0.7)' : 'rgba(255,255,255,0.8)';
?>
<style>
.payment-banner-full::after { color: <?php echo $textColor; ?>; }
</style>
<div class="payment-banner-full" style="background: <?php echo $bgColor; ?>">
    <img src="<?php echo htmlspecialchars($banner); ?>" alt="Payment banner" style="object-fit:contain;max-width:<?php echo $maxWidth; ?>;max-height:<?php echo $maxHeight; ?>;">
</div>
<script>
    setTimeout(function(){
        window.location.href = "<?php echo $redirectUrl; ?>";
    }, 2000);
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
