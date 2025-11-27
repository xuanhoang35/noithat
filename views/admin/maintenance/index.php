<?php ob_start(); ?>
<?php
    $c = $config ?? [];
    $enabled = !empty($c['enabled']);
    $title = $c['title'] ?? '';
    $subtitle = $c['subtitle'] ?? '';
    $message = $c['message'] ?? '';
    $image = $c['image'] ?? '';
    $video = $c['video'] ?? '';
    $previewImage = $image ? asset_url($image) : asset_url('public/assets/img/placeholder.svg');
    $previewVideo = $video ? asset_url($video) : '';
?>
<div class="bg-white rounded-2xl shadow-sm p-5 space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold">Chế độ bảo trì</h1>
            <p class="text-slate-500 text-sm">Thiết kế trang thông báo và bật/tắt website.</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs <?php echo $enabled ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-700'; ?>">
            <?php echo $enabled ? 'Đang đóng website' : 'Đang mở website'; ?>
        </span>
    </div>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="p-3 rounded bg-red-50 text-red-700 border border-red-100"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="p-3 rounded bg-emerald-50 text-emerald-700 border border-emerald-100"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>

    <div class="grid lg:grid-cols-3 gap-4">
        <form class="lg:col-span-2 space-y-3" method="post" action="<?php echo base_url('admin.php/maintenance'); ?>" enctype="multipart/form-data">
            <div>
                <label class="text-sm text-slate-600">Tiêu đề</label>
                <input name="title" class="w-full px-3 py-2 border rounded" value="<?php echo htmlspecialchars($title); ?>">
            </div>
            <div>
                <label class="text-sm text-slate-600">Phụ đề</label>
                <input name="subtitle" class="w-full px-3 py-2 border rounded" value="<?php echo htmlspecialchars($subtitle); ?>">
            </div>
            <div>
                <label class="text-sm text-slate-600">Thông điệp</label>
                <textarea name="message" rows="3" class="w-full px-3 py-2 border rounded"><?php echo htmlspecialchars($message); ?></textarea>
            </div>
            <div class="grid md:grid-cols-2 gap-3">
                <div>
                    <label class="text-sm text-slate-600">Chọn ảnh</label>
                    <div class="flex items-center gap-2 mt-1">
                        <input type="file" name="image" accept="image/*" class="w-full text-sm" id="maintenance-image">
                        <button type="button" id="maintenance-image-clear" class="px-2 py-1 text-xs bg-slate-100 text-slate-700 rounded hover:bg-slate-200 hidden">X</button>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Chọn ảnh sẽ xóa video hiện tại.</p>
                    <div id="maintenance-image-name" class="text-xs text-blue-600 mt-1 hidden"></div>
                </div>
                <div>
                    <label class="text-sm text-slate-600">Chọn video</label>
                    <div class="flex items-center gap-2 mt-1">
                        <input type="file" name="video" accept="video/*" class="w-full text-sm" id="maintenance-video">
                        <button type="button" id="maintenance-video-clear" class="px-2 py-1 text-xs bg-slate-100 text-slate-700 rounded hover:bg-slate-200 hidden">X</button>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Chọn video sẽ xóa ảnh hiện tại.</p>
                    <div id="maintenance-video-name" class="text-xs text-blue-600 mt-1 hidden"></div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="enabled" value="1" class="sr-only peer" id="maintenance-toggle" <?php echo $enabled ? 'checked' : ''; ?>>
                    <div class="w-12 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:bg-red-500 transition"></div>
                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-6"></div>
                </label>
                <span class="text-sm text-red-600 font-semibold">Gạt để đóng/mở trang web (áp dụng ngay)</span>
            </div>
        </form>
        <div class="lg:col-span-1">
            <div class="border rounded-2xl overflow-hidden shadow-sm">
                <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white p-4">
                    <p class="text-xs uppercase tracking-widest text-blue-200">Preview</p>
                    <p class="text-lg font-semibold">Trang bảo trì</p>
                </div>
                <div class="p-4 space-y-2 bg-slate-50">
                    <?php if ($previewVideo): ?>
                        <video src="<?php echo $previewVideo; ?>" class="w-full h-40 rounded-lg border border-slate-200 object-cover" controls muted loop></video>
                    <?php else: ?>
                        <img src="<?php echo $previewImage; ?>" alt="Preview" class="w-full h-40 object-cover rounded-lg border border-slate-200">
                    <?php endif; ?>
                    <div class="bg-white rounded-xl p-3 shadow border border-slate-100">
                        <p class="text-lg font-bold text-slate-800"><?php echo htmlspecialchars($title); ?></p>
                        <p class="text-sm text-slate-600"><?php echo htmlspecialchars($subtitle); ?></p>
                        <p class="text-sm text-slate-500 mt-2 whitespace-pre-line"><?php echo htmlspecialchars($message); ?></p>
                        <div class="mt-3 flex gap-2">
                            <span class="px-3 py-2 rounded bg-blue-600 text-white text-xs">Liên hệ CSKH</span>
                            <span class="px-3 py-2 rounded bg-slate-200 text-slate-700 text-xs">Hotline: 0974.734.668</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const form = document.querySelector('form[action$="maintenance"]');
    if (!form) return;
    const toggle = document.getElementById('maintenance-toggle');
    if (toggle) {
        toggle.addEventListener('change', () => form.submit());
    }
    const imgInput = document.getElementById('maintenance-image');
    const videoInput = document.getElementById('maintenance-video');
    const imgClear = document.getElementById('maintenance-image-clear');
    const vidClear = document.getElementById('maintenance-video-clear');
    const imgName = document.getElementById('maintenance-image-name');
    const vidName = document.getElementById('maintenance-video-name');
    const disableCls = ['opacity-50','cursor-not-allowed'];
    const updateState = () => {
        const hasImg = imgInput && imgInput.files && imgInput.files.length > 0;
        const hasVid = videoInput && videoInput.files && videoInput.files.length > 0;
        if (imgInput) {
            if (hasVid) { imgInput.disabled = true; imgInput.classList.add(...disableCls); }
            else { imgInput.disabled = false; imgInput.classList.remove(...disableCls); }
        }
        if (videoInput) {
            if (hasImg) { videoInput.disabled = true; videoInput.classList.add(...disableCls); }
            else { videoInput.disabled = false; videoInput.classList.remove(...disableCls); }
        }
        if (imgName) {
            if (hasImg) { imgName.textContent = imgInput.files[0].name; imgName.classList.remove('hidden'); }
            else { imgName.textContent = ''; imgName.classList.add('hidden'); }
        }
        if (vidName) {
            if (hasVid) { vidName.textContent = videoInput.files[0].name; vidName.classList.remove('hidden'); }
            else { vidName.textContent = ''; vidName.classList.add('hidden'); }
        }
        if (imgClear) imgClear.classList.toggle('hidden', !hasImg);
        if (vidClear) vidClear.classList.toggle('hidden', !hasVid);
    };
    if (imgInput && videoInput) {
        imgInput.addEventListener('change', () => { if (imgInput.files.length) videoInput.value=''; updateState(); });
        videoInput.addEventListener('change', () => { if (videoInput.files.length) imgInput.value=''; updateState(); });
    }
    if (imgClear) imgClear.addEventListener('click', () => { imgInput.value=''; updateState(); });
    if (vidClear) vidClear.addEventListener('click', () => { videoInput.value=''; updateState(); });
    updateState();
});
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
