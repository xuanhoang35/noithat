<?php ob_start(); ?>
<?php
    $img = asset_url(!empty($image) ? $image : 'public/assets/img/placeholder.svg');
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
<body class="bg-slate-950 text-white flex items-center justify-center px-4 relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(59,130,246,0.15),transparent_25%),radial-gradient(circle_at_80%_10%,rgba(236,72,153,0.12),transparent_20%),radial-gradient(circle_at_50%_80%,rgba(16,185,129,0.12),transparent_20%)]"></div>
    <div class="relative max-w-5xl w-full grid md:grid-cols-2 gap-6 items-center bg-slate-900/70 backdrop-blur-lg rounded-3xl p-6 md:p-8 border border-slate-800 glow">
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
        <div class="rounded-2xl overflow-hidden shadow-2xl border border-slate-800">
            <img src="<?php echo $img; ?>" alt="Maintenance" class="w-full h-full object-cover" onerror="this.src='<?php echo asset_url('public/assets/img/placeholder.svg'); ?>';">
        </div>
    </div>
</body>
</html>
<?php ob_end_flush(); ?>
