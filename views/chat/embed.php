<?php
$thread = $thread ?? null;
$messages = $messages ?? [];
$isClosed = ($thread['status'] ?? '') === 'closed';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chat hỗ trợ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: rgba(148,163,184,.6); border-radius: 999px; }
    </style>
</head>
<body class="bg-transparent">
<div class="flex flex-col h-[500px] w-[360px] bg-white rounded-2xl shadow-2xl overflow-hidden">
    <div class="flex-1 overflow-y-auto bg-slate-50 px-4 py-3 space-y-3">
        <?php foreach ($messages as $m): $isAdmin = (bool)$m['is_admin']; ?>
            <div class="flex <?php echo $isAdmin ? 'justify-start' : 'justify-end'; ?>">
                <div class="max-w-[75%] rounded-2xl px-4 py-3 shadow <?php echo $isAdmin ? 'bg-white' : 'bg-blue-600 text-white'; ?>">
                    <div class="text-[11px] mb-1 <?php echo $isAdmin ? 'text-slate-500' : 'text-blue-100'; ?>">
                        <?php echo $isAdmin ? 'Admin' : 'Bạn'; ?> · <?php echo date('d/m H:i', strtotime($m['created_at'])); ?>
                    </div>
                    <div class="text-sm whitespace-pre-line leading-relaxed"><?php echo htmlspecialchars($m['content']); ?></div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($messages)): ?>
            <div class="text-center text-slate-500 text-sm py-10">Bạn cần tư vấn gì? Hãy gửi tin nhắn đầu tiên.</div>
        <?php endif; ?>
    </div>
    <form method="post" action="<?php echo base_url('chat'); ?>" class="border-t bg-white px-4 py-3 space-y-2">
        <textarea name="content" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-200 text-sm" rows="2" placeholder="Nhập nội dung..." <?php echo $isClosed ? 'disabled' : 'required'; ?>></textarea>
        <input type="hidden" name="embed" value="1">
        <div class="flex gap-2">
            <button name="action" value="end" class="flex-1 h-10 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-100 text-sm">Kết thúc</button>
            <button name="action" value="send" class="flex-1 h-10 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm" <?php echo $isClosed ? 'disabled' : ''; ?>>Gửi</button>
        </div>
    </form>
</div>
</body>
</html>
