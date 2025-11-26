<?php ob_start(); ?>
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="h-[520px] flex flex-col">
        <div class="flex-1 overflow-y-auto bg-slate-50 px-4 py-3 space-y-3">
            <?php foreach ($messages as $m): ?>
                <?php $isAdmin = (bool)$m['is_admin']; ?>
                <div class="flex <?php echo $isAdmin ? 'justify-start' : 'justify-end'; ?>">
                    <div class="max-w-[70%] rounded-2xl px-4 py-3 shadow-sm <?php echo $isAdmin ? 'bg-white' : 'bg-blue-600 text-white'; ?>">
                        <div class="text-xs mb-1 <?php echo $isAdmin ? 'text-slate-500' : 'text-blue-50'; ?>">
                            <?php echo $isAdmin ? 'Admin' : 'Bạn'; ?> · <?php echo date('d/m H:i', strtotime($m['created_at'])); ?>
                        </div>
                        <div class="text-sm leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($m['content']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($messages)): ?>
                <div class="text-center text-slate-500 text-sm py-10">Chưa có tin nhắn nào, hãy đặt câu hỏi cho chúng tôi.</div>
            <?php endif; ?>
        </div>
        <div class="border-t bg-white px-4 py-3">
            <form method="post" action="<?php echo base_url('chat'); ?>" class="flex gap-3 items-end">
                <div class="flex-1">
                    <label class="text-sm text-slate-600">Nội dung</label>
                    <textarea name="content" class="w-full mt-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-200" rows="3" placeholder="Nhập câu hỏi hoặc yêu cầu tư vấn..." required></textarea>
                </div>
                <div class="flex flex-col gap-2 w-32">
                    <button name="action" value="end" formnovalidate class="h-[44px] rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 text-sm">Kết thúc</button>
                    <button name="action" value="send" class="h-[44px] rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-medium">Gửi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
