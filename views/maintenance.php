<?php ob_start(); ?>
<?php
    $imageList = [];
    if (!empty($images) && is_array($images)) {
        foreach ($images as $it) {
            $it = trim((string)$it);
            if ($it !== '') $imageList[] = $it;
        }
    }
    if (empty($imageList) && !empty($image)) {
        $imageList[] = $image;
    }
    if (empty($imageList)) {
        $imageList[] = 'public/assets/img/placeholder.svg';
    }
    $imageUrls = array_map(fn($i) => asset_url($i), $imageList);
    $videoSrc = '';
    $videoEmbed = '';
    if (!empty($video)) {
        $isUrl = preg_match('#^https?://#', $video);
        if ($isUrl) {
            if (preg_match('#(?:youtube\\.com/watch\\?v=|youtu\\.be/)([A-Za-z0-9_-]{6,})#', $video, $m)) {
                $videoEmbed = 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&autoplay=1&mute=0&loop=1&playlist=' . $m[1];
            } elseif (preg_match('#drive\\.google\\.com/file/d/([^/]+)/?#', $video, $m)) {
                $videoEmbed = 'https://drive.google.com/file/d/' . $m[1] . '/preview?autoplay=1';
            } elseif (strpos($video, 'facebook.com') !== false) {
                $videoEmbed = 'https://www.facebook.com/plugins/video.php?href=' . urlencode($video) . '&show_text=false&autoplay=true';
            } elseif (strpos($video, 'tiktok.com') !== false) {
                $videoEmbed = 'https://www.tiktok.com/embed/v2/' . rawurlencode($video) . '?autoplay=1';
            } else {
                $videoSrc = $video;
            }
        } else {
            $videoSrc = asset_url($video);
        }
    }
    $title = $title ?? 'Chúng tôi đang bảo trì';
    $subtitle = $subtitle ?? 'Sẽ trở lại sớm nhất';
    $message = $message ?? 'Xin lỗi vì sự bất tiện.';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảo trì | Nội Thất Store - Cửa hàng nội thất và thiết bị gia dụng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body, html { margin:0; padding:0; min-height:100vh; }
        .glow { box-shadow: 0 10px 40px rgba(59,130,246,0.35); }
    </style>
    <link rel="icon" type="image/png" href="<?php echo asset_url('public/bank/noithat_logo.png'); ?>">
</head>
<body class="bg-slate-950 text-white flex items-center justify-center px-4 relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(59,130,246,0.15),transparent_25%),radial-gradient(circle_at_80%_10%,rgba(236,72,153,0.12),transparent_20%),radial-gradient(circle_at_50%_80%,rgba(16,185,129,0.12),transparent_20%)]"></div>
    <div class="relative max-w-5xl w-full grid md:grid-cols-2 gap-6 items-center bg-slate-900/70 backdrop-blur-lg rounded-3xl p-6 md:p-8 border border-slate-800 glow">
        <div class="space-y-4">
            <div class="flex items-center gap-2 text-xs uppercase tracking-[0.25em] text-blue-200">
                <span class="inline-block w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                Nội Thất Store
            </div>
            <h1 class="text-3xl md:text-4xl font-bold leading-tight"><?php echo htmlspecialchars($title); ?></h1>
            <p class="text-lg text-blue-100"><?php echo htmlspecialchars($subtitle); ?></p>
            <p class="text-sm text-slate-200 whitespace-pre-line leading-relaxed"><?php echo htmlspecialchars($message); ?></p>
            <div class="flex flex-wrap gap-2 text-xs">
                <a href="tel:0974734668" class="px-4 py-2 rounded-full bg-blue-600 hover:bg-blue-500 text-white">Hotline: 0974.734.668</a>
                <a href="mailto:huyendothi.79@gmail.com" class="px-4 py-2 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-100">Email: huyendothi.79@gmail.com</a>
            </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-slate-800 bg-black/60 aspect-video">
            <?php if ($videoEmbed): ?>
                <iframe src="<?php echo htmlspecialchars($videoEmbed); ?>" class="absolute inset-0 w-full h-full" allow="autoplay; encrypted-media" allowfullscreen style="border:0; object-fit: cover;"></iframe>
            <?php elseif ($videoSrc): ?>
                <video src="<?php echo $videoSrc; ?>" class="absolute inset-0 w-full h-full object-cover" autoplay loop playsinline controls data-force-play></video>
            <?php else: ?>
                <?php foreach ($imageUrls as $idx => $url): ?>
                    <img src="<?php echo $url; ?>" alt="Maintenance" class="maintenance-slide absolute inset-0 w-full h-full object-cover <?php echo $idx === 0 ? 'is-active' : ''; ?>" data-maintenance-slide onerror="this.src='<?php echo asset_url('public/assets/img/placeholder.svg'); ?>';">
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('video[data-force-play]').forEach(v => {
        v.play().catch(() => {});
    });
    const slides = document.querySelectorAll('[data-maintenance-slide]');
    if (slides.length > 1) {
        let current = 0;
        setInterval(() => {
            slides[current].classList.remove('is-active');
            current = (current + 1) % slides.length;
            slides[current].classList.add('is-active');
        }, 5000);
    }
});
</script>
<style>
.maintenance-slide { opacity: 0; transform: scale(1.02); transition: opacity 0.9s ease, transform 0.9s ease; }
.maintenance-slide.is-active { opacity: 1; transform: scale(1); }
</style>
</html>
<?php ob_end_flush(); ?>
