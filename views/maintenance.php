<?php ob_start(); ?>
<?php
    $img = asset_url(!empty($image) ? $image : 'public/assets/img/placeholder.svg');
    $headerLogo = asset_url('public/bank/noithat_logo.png');
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
    <title>Bảo trì | Noithat Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body, html { margin:0; padding:0; min-height:100vh; }
        .glow { box-shadow: 0 10px 40px rgba(59,130,246,0.35); }
    </style>
</head>
<body class="bg-slate-950 text-white min-h-screen relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(59,130,246,0.15),transparent_25%),radial-gradient(circle_at_80%_10%,rgba(236,72,153,0.12),transparent_20%),radial-gradient(circle_at_50%_80%,rgba(16,185,129,0.12),transparent_20%)]"></div>
    <header class="absolute top-0 left-0 w-full z-30">
        <div class="max-w-6xl mx-auto px-4">
            <div class="mt-4 bg-slate-900/80 backdrop-blur-lg border border-slate-800/70 rounded-3xl shadow-2xl flex items-center gap-4 px-4 md:px-6 py-3">
                <div class="h-16 md:h-20 flex items-center">
                    <img src="<?php echo $headerLogo; ?>" alt="Noithat Store logo" class="h-full w-auto object-contain drop-shadow-[0_10px_30px_rgba(0,0,0,0.45)]" onerror="this.src='<?php echo $img; ?>';">
                </div>
                <div class="hidden sm:flex flex-col leading-tight text-slate-100">
                    <span class="text-xs uppercase tracking-[0.25em] text-blue-200">Noithat Store</span>
                    <span class="text-sm md:text-base text-slate-200">Nội thất nâng tầm không gian sống</span>
                </div>
            </div>
        </div>
    </header>
    <main class="relative w-full min-h-screen flex items-center justify-center px-4 pt-28 pb-10">
        <div class="relative max-w-5xl w-full grid md:grid-cols-2 gap-6 items-center bg-slate-900/70 backdrop-blur-lg rounded-3xl p-6 md:p-8 border border-slate-800 glow">
            <div class="md:col-span-2 flex justify-center">
                <div class="px-4 py-3 bg-slate-800/70 border border-slate-700/60 rounded-2xl shadow-lg flex items-center justify-center">
                    <img src="<?php echo $headerLogo; ?>" alt="Noithat Store logo" class="h-20 md:h-24 w-auto object-contain drop-shadow-[0_20px_45px_rgba(0,0,0,0.5)]" onerror="this.style.display='none'">
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex items-center gap-2 text-xs uppercase tracking-[0.25em] text-blue-200">
                    <span class="inline-block w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    Noithat Store
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
                    <img src="<?php echo $img; ?>" alt="Maintenance" class="absolute inset-0 w-full h-full object-cover" onerror="this.src='<?php echo asset_url('public/assets/img/placeholder.svg'); ?>';">
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('video[data-force-play]').forEach(v => {
        v.play().catch(() => {});
    });
});
</script>
</html>
<?php ob_end_flush(); ?>
