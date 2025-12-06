<?php ob_start(); ?>
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-5 space-y-4 text-center">
        <h1 class="text-xl font-semibold text-slate-800">Yêu cầu đang được xử lý</h1>
        <p class="text-slate-500 text-sm">Vui lòng chờ quản trị viên xác nhận và cấp mật khẩu mới. Mỗi chu kỳ kiểm tra kéo dài <span class="font-semibold text-blue-600">60 giây</span>.</p>
        <div id="countdown-wrap" class="space-y-2">
            <div class="text-4xl font-bold text-blue-600" id="countdown">60</div>
            <div id="wait-status" class="text-sm text-slate-500">
                <span id="wait-message" class="block">Chúng tôi sẽ thông báo ngay khi mật khẩu mới sẵn sàng.</span>
                <span id="wait-extra" class="block mt-1 text-amber-600"></span>
            </div>
        </div>
        <div id="new-password-box" class="hidden bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-lg p-4">
            <p class="font-semibold mb-1">Mật khẩu của quý khách là:</p>
            <div id="new-password-text" class="text-2xl font-bold tracking-wide"></div>
            <p class="text-xs mt-2 text-emerald-600">Vui lòng đăng nhập và đổi mật khẩu sau khi sử dụng.</p>
        </div>
        <div id="reject-box" class="hidden bg-red-50 border border-red-100 text-red-700 rounded-lg p-4 text-left">
            <p class="font-semibold mb-1">Yêu cầu bị từ chối</p>
            <p class="text-sm leading-relaxed">Yêu cầu cấp mật khẩu mới của bạn đã bị từ chối. Vui lòng liên hệ quản trị viên để được hỗ trợ nhanh nhất.</p>
        </div>
    </div>
</div>
<script>
(function(){
    const resendUrlBase = <?php echo json_encode(base_url('forgot/resend/')); ?>;
    const statusUrlBase = <?php echo json_encode(base_url('forgot/status/')); ?>;
    let remaining = 60;
    let resolved = false;
    const countdownEl = document.getElementById('countdown');
    const messageSpan = document.getElementById('wait-message');
    const extraSpan = document.getElementById('wait-extra');
    const passwordBox = document.getElementById('new-password-box');
    const passwordText = document.getElementById('new-password-text');
    const countdownWrap = document.getElementById('countdown-wrap');
    const requestId = <?php echo json_encode((string)$requestId); ?>;
    const resendLink = document.createElement('a');
    resendLink.href = '#';
    resendLink.className = 'text-blue-600 font-medium underline cursor-pointer hidden';
    resendLink.textContent = 'Gửi lại yêu cầu';
    resendLink.addEventListener('click', function(e){
        e.preventDefault();
        window.location.href = resendUrlBase + requestId;
    });
    extraSpan.appendChild(resendLink);
    function showResend(message) {
        extraSpan.textContent = message + ' ';
        extraSpan.appendChild(resendLink);
        resendLink.classList.remove('hidden');
    }
    const countdownTimer = setInterval(() => {
        if (resolved) return;
        remaining -= 1;
        if (remaining <= 0) {
            clearInterval(countdownTimer);
            clearInterval(statusTimer);
            showResend('Phiên chờ đã hết hạn. ');
        }
        countdownEl.textContent = Math.max(0, remaining);
    }, 1000);
    function checkStatus(){
        fetch(statusUrlBase + requestId, { cache: 'no-store', credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'completed') {
                    clearInterval(countdownTimer);
                    clearInterval(statusTimer);
                    resolved = true;
                    if (countdownWrap) countdownWrap.classList.add('hidden');
                    passwordBox.classList.remove('hidden');
                    passwordText.textContent = data.password || '(không xác định)';
                    extraSpan.textContent = 'Quản trị viên đã cấp mật khẩu mới. Vui lòng đăng nhập ngay.';
                    resendLink.classList.add('hidden');
                } else if (data.status === 'rejected') {
                    clearInterval(countdownTimer);
                    clearInterval(statusTimer);
                    resolved = true;
                    remaining = 0;
                    if (countdownEl) countdownEl.textContent = '0';
                    if (countdownWrap) countdownWrap.classList.add('hidden');
                    passwordBox.classList.add('hidden');
                    document.getElementById('reject-box').classList.remove('hidden');
                    messageSpan.textContent = 'Yêu cầu cấp mật khẩu mới của bạn bị từ chối.';
                    extraSpan.textContent = 'Vui lòng liên hệ quản trị viên để được hỗ trợ.';
                    resendLink.classList.add('hidden');
                } else if (data.status === 'invalid' || data.status === 'missing') {
                    showResend('Phiên chờ đã hết hạn.');
                }
            })
            .catch(() => {});
    }
    const statusTimer = setInterval(checkStatus, 1000);
    checkStatus();
})();
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
