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
    </style>
</head>
<body class="bg-slate-900 text-white flex items-center justify-center px-4">
    <div class="max-w-4xl w-full grid md:grid-cols-2 gap-6 items-center bg-slate-800/70 rounded-3xl p-6 shadow-2xl border border-slate-700">
        <div class="space-y-3">
            <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Noithat Store</p>
            <h1 class="text-3xl font-bold leading-tight"><?php echo htmlspecialchars($title); ?></h1>
            <p class="text-lg text-blue-100"><?php echo htmlspecialchars($subtitle); ?></p>
            <p class="text-sm text-slate-200 whitespace-pre-line"><?php echo htmlspecialchars($message); ?></p>
            <div class="flex flex-wrap gap-2 text-xs">
                <span class="px-3 py-2 rounded-full bg-blue-600 text-white">Hotline: 0974.734.668</span>
                <span class="px-3 py-2 rounded-full bg-slate-700 text-slate-100">Email: huyendothi.79@gmail.com</span>
            </div>
        </div>
        <div class="rounded-2xl overflow-hidden shadow-xl border border-slate-700">
            <img src="<?php echo $img; ?>" alt="Maintenance" class="w-full h-full object-cover">
        </div>
    </div>
</body>
</html>
<?php ob_end_flush(); ?>
