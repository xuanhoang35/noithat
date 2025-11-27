<?php ob_start(); ?>
<?php
    $c = $config ?? [];
    $enabled = !empty($c['enabled']);
    $title = $c['title'] ?? '';
    $subtitle = $c['subtitle'] ?? '';
    $message = $c['message'] ?? '';
    $image = $c['image'] ?? '';
    $images = $c['images'] ?? [];
    if (empty($images) && $image) { $images[] = $image; }
    $video = $c['video'] ?? '';
    $previewImages = [];
    foreach ($images as $img) {
        if ($img) { $previewImages[] = asset_url($img); }
    }
    if (empty($previewImages)) {
        $previewImages[] = asset_url('public/assets/img/placeholder.svg');
    }
    $previewVideo = '';
    $previewEmbed = '';
    if (!empty($video)) {
        $isUrl = preg_match('#^https?://#', $video);
        if ($isUrl) {
            // YouTube (loop bằng playlist)
            if (preg_match('#(?:youtube\\.com/watch\\?v=|youtu\\.be/)([A-Za-z0-9_-]{6,})#', $video, $m)) {
                $previewEmbed = 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&autoplay=1&mute=0&loop=1&playlist=' . $m[1];
            }
            // Google Drive preview (không hỗ trợ loop chuẩn)
            elseif (preg_match('#drive\\.google\\.com/file/d/([^/]+)/?#', $video, $m)) {
                $previewEmbed = 'https://drive.google.com/file/d/' . $m[1] . '/preview?autoplay=1';
            }
            // Facebook video
            elseif (strpos($video, 'facebook.com') !== false) {
                $previewEmbed = 'https://www.facebook.com/plugins/video.php?href=' . urlencode($video) . '&show_text=false&autoplay=true';
            }
            // TikTok (embed)
            elseif (strpos($video, 'tiktok.com') !== false) {
                $previewEmbed = 'https://www.tiktok.com/embed/v2/' . rawurlencode($video) . '?autoplay=1';
            } else {
                $previewVideo = $video;
            }
        } else {
            $previewVideo = asset_url($video);
        }
    }
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
            <div>
                <label class="text-sm text-slate-600">Ảnh nền (tùy chọn, có thể chọn nhiều)</label>
                <div class="flex items-center gap-2 mt-1">
                    <input type="file" name="images[]" accept="image/*" class="w-full text-sm" id="maintenance-image" multiple>
                    <button type="button" id="maintenance-image-clear" class="px-2 py-1 text-xs bg-slate-100 text-slate-700 rounded hover:bg-slate-200 hidden" aria-label="Xóa danh sách ảnh">X</button>
                </div>
                <p class="text-xs text-slate-500 mt-1">Chọn nhiều ảnh, trang bảo trì sẽ tự chuyển mượt sau mỗi 5 giây. Chọn ảnh sẽ bỏ URL video và video hiện tại.</p>
                <div id="maintenance-image-name" class="text-xs text-blue-600 mt-1 hidden"></div>
            </div>
            <div>
                <label class="text-sm text-slate-600">Video URL (Google Drive/YouTube/CDN)</label>
                <input name="video_url" class="w-full px-3 py-2 border rounded" placeholder="https://..." value="<?php echo htmlspecialchars(preg_match('#^https?://#', $video) ? $video : ''); ?>">
                <p class="text-xs text-slate-500 mt-1">Dán URL video để phát trực tiếp (ưu tiên dùng URL, bỏ upload tệp để nhanh hơn).</p>
            </div>
            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="enabled" value="1" class="sr-only peer" id="maintenance-toggle" <?php echo $enabled ? 'checked' : ''; ?>>
                    <div class="w-12 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:bg-red-500 transition"></div>
                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-6"></div>
                </label>
                <span class="text-sm text-red-600 font-semibold">Gạt để đóng/mở trang web (áp dụng ngay)</span>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" id="maintenance-submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu nội dung</button>
            </div>
        </form>
        <div class="lg:col-span-1">
            <div class="border rounded-2xl overflow-hidden shadow-sm">
                <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white p-4">
                    <p class="text-xs uppercase tracking-widest text-blue-200">Preview</p>
                    <p class="text-lg font-semibold">Trang bảo trì</p>
                </div>
                <div class="p-4 space-y-3 bg-slate-50">
                    <?php if ($previewEmbed): ?>
                        <div class="w-full h-48 rounded-lg border border-slate-200 overflow-hidden">
                            <iframe src="<?php echo htmlspecialchars($previewEmbed); ?>" class="w-full h-full" allowfullscreen allow="autoplay; encrypted-media"></iframe>
                        </div>
                    <?php elseif ($previewVideo): ?>
                        <video src="<?php echo $previewVideo; ?>" class="w-full h-48 rounded-lg border border-slate-200 object-cover" controls autoplay loop playsinline></video>
                    <?php else: ?>
                        <div class="relative w-full h-48 rounded-lg border border-slate-200 overflow-hidden bg-black/60">
                            <?php foreach ($previewImages as $idx => $url): ?>
                                <img src="<?php echo $url; ?>" alt="Preview" class="maintenance-preview-slide absolute inset-0 w-full h-full object-cover <?php echo $idx === 0 ? 'is-active' : ''; ?>" data-maintenance-preview-slide>
                            <?php endforeach; ?>
                        </div>
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
    const submitBtn = document.getElementById('maintenance-submit');
    const imgInput = document.getElementById('maintenance-image');
    const imgClear = document.getElementById('maintenance-image-clear');
    const imgName = document.getElementById('maintenance-image-name');
    const videoUrlInput = document.querySelector('input[name="video_url"]');

    const updateState = () => {
        const hasImg = imgInput && imgInput.files && imgInput.files.length > 0;
        if (imgName) {
            if (hasImg) {
                const names = Array.from(imgInput.files).map(f => f.name).join(', ');
                imgName.textContent = names;
                imgName.classList.remove('hidden');
            }
            else { imgName.textContent = ''; imgName.classList.add('hidden'); }
        }
        if (imgClear) imgClear.classList.toggle('hidden', !hasImg);
        if (videoUrlInput && hasImg) videoUrlInput.value = '';
    };

    if (imgInput) imgInput.addEventListener('change', updateState);
    if (imgClear) imgClear.addEventListener('click', () => { if (imgInput) imgInput.value=''; updateState(); });
    if (videoUrlInput) videoUrlInput.addEventListener('input', () => { if (videoUrlInput.value.trim() !== '' && imgInput) { imgInput.value=''; updateState(); } });
    updateState();

    if (toggle) {
        toggle.addEventListener('change', () => form.submit());
    }
    form.addEventListener('submit', () => { if (submitBtn) submitBtn.disabled = true; });

    // Preview slider (5s)
    const slides = document.querySelectorAll('[data-maintenance-preview-slide]');
    if (slides.length > 1) {
        let idx = 0;
        setInterval(() => {
            slides[idx].classList.remove('is-active');
            idx = (idx + 1) % slides.length;
            slides[idx].classList.add('is-active');
        }, 5000);
    }
});
</script>
<style>
.maintenance-preview-slide { opacity: 0; transform: scale(1.02); transition: opacity 0.8s ease, transform 0.8s ease; }
.maintenance-preview-slide.is-active { opacity: 1; transform: scale(1); }
</style>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
