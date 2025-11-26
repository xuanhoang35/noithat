<?php ob_start(); ?>
<div class="flex flex-col gap-4">
    <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center justify-between">
        <div>
            <p class="text-sm text-slate-500">Phiên chat #<?php echo $thread['id']; ?></p>
            <h1 class="text-xl font-semibold">Khách: <?php echo htmlspecialchars($thread['email']); ?></h1>
            <p class="text-slate-500 text-sm">Cập nhật: <?php echo date('d/m/Y H:i', strtotime($thread['updated_at'])); ?></p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs <?php echo $thread['status']==='open' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700'; ?>">
                <?php echo $thread['status']==='open' ? 'Đang mở' : 'Đã đóng'; ?>
            </span>
            <a href="<?php echo base_url('admin.php/chats'); ?>" class="px-3 py-2 text-sm bg-slate-100 rounded-lg hover:bg-slate-200">← Danh sách</a>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="h-[520px] flex flex-col">
            <div class="flex-1 overflow-y-auto bg-slate-50 px-4 py-3 space-y-3">
                <?php foreach ($messages as $m): ?>
                    <?php $isAdmin = (bool)$m['is_admin']; ?>
                    <div class="flex <?php echo $isAdmin ? 'justify-end' : 'justify-start'; ?>">
                        <div class="max-w-[70%] rounded-2xl px-4 py-3 shadow-sm <?php echo $isAdmin ? 'bg-blue-600 text-white' : 'bg-white'; ?>">
                            <div class="text-xs mb-1 <?php echo $isAdmin ? 'text-blue-50' : 'text-slate-500'; ?>">
                                <?php echo $isAdmin ? 'Admin' : $m['email']; ?> · <?php echo date('d/m H:i', strtotime($m['created_at'])); ?>
                            </div>
                            <div class="text-sm leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($m['content']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($messages)): ?>
                    <div class="text-center text-slate-500 text-sm py-10">Chưa có tin nhắn.</div>
                <?php endif; ?>
            </div>
            <div class="border-t bg-white px-4 py-3">
                <form method="post" action="<?php echo base_url('admin.php/chats/reply/' . $thread['id']); ?>" class="grid md:grid-cols-12 gap-3 items-end">
                    <div class="md:col-span-8">
                        <label class="text-sm text-slate-600">Nội dung</label>
                        <textarea name="content" class="w-full mt-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-200" rows="3" placeholder="Nhập câu trả lời..."></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm text-slate-600">Trạng thái</label>
                        <select name="status" class="w-full mt-1 px-3 py-2 border rounded-lg">
                            <option value="open" <?php echo $thread['status']==='open' ? 'selected' : ''; ?>>Đang mở</option>
                            <option value="closed" <?php echo $thread['status']==='closed' ? 'selected' : ''; ?>>Đã đóng</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 flex gap-2">
                        <button class="w-full h-[48px] rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-medium">Gửi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
