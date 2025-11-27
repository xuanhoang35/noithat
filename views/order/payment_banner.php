<?php ob_start(); ?>
<?php $hideHeader = true; $hideFooter = true; ?>
<style>
.payment-banner-full {
    position: fixed;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
    background: #fff;
}
.payment-banner-full img {
    width: 100vw;
    height: 100vh;
    object-fit: contain;
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
.payment-banner-full { background: <?php echo $bgColor; ?>; }
.payment-banner-full::after { color: <?php echo $textColor; ?>; }
</style>
<div class="payment-banner-full">
    <img src="<?php echo htmlspecialchars($banner); ?>" alt="Payment banner" style="max-width:<?php echo $maxWidth; ?>;max-height:<?php echo $maxHeight; ?>;">
</div>
<script>
    setTimeout(function(){
        window.location.href = "<?php echo $redirectUrl; ?>";
    }, 2000);
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
