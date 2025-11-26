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
            <div class="flex-1 overflow-y-auto bg-slate-50 px-4 py-3 space-y-3" id="chat-list" data-thread="<?php echo (int)$thread['id']; ?>">
                <?php foreach ($messages as $m): ?>
                    <?php $isAdmin = (bool)$m['is_admin']; ?>
                    <div class="flex <?php echo $isAdmin ? 'justify-end' : 'justify-start'; ?>" data-msg-id="<?php echo $m['id']; ?>">
                        <div class="max-w-[70%] rounded-2xl px-4 py-3 shadow-sm <?php echo $isAdmin ? 'bg-blue-600 text-white' : 'bg-white'; ?>">
                            <div class="text-xs mb-1 <?php echo $isAdmin ? 'text-blue-50' : 'text-slate-500'; ?>">
                                <?php echo $isAdmin ? 'Admin' : $m['email']; ?> · <?php echo date('d/m H:i', strtotime($m['created_at'])); ?>
                            </div>
                            <div class="text-sm leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($m['content']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($messages)): ?>
                    <div class="text-center text-slate-500 text-sm py-10" data-empty>Chưa có tin nhắn.</div>
                <?php endif; ?>
            </div>
            <div class="border-t bg-white px-4 py-3">
                <form method="post" action="<?php echo base_url('admin.php/chats/reply/' . $thread['id']); ?>" class="grid md:grid-cols-12 gap-3 items-end">
                    <div class="md:col-span-8">
                        <label class="text-sm text-slate-600">Nội dung</label>
                        <textarea name="content" class="w-full mt-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-200" rows="3" placeholder="Nhập câu trả lời..." id="admin-chat-textarea"></textarea>
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
<script>
(function(){
    const list = document.getElementById('chat-list');
    if (!list) return;
    const threadId = list.getAttribute('data-thread');
    let lastId = 0;
    list.querySelectorAll('[data-msg-id]').forEach(el => {
        const id = parseInt(el.getAttribute('data-msg-id'), 10);
        if (id > lastId) lastId = id;
    });
    const append = (items) => {
        const empty = list.querySelector('[data-empty]');
        if (empty) empty.remove();
        items.forEach(m => {
            const wrap = document.createElement('div');
            wrap.className = 'flex ' + (m.is_admin ? 'justify-end' : 'justify-start');
            wrap.setAttribute('data-msg-id', m.id);
            wrap.innerHTML = `
                <div class="max-w-[70%] rounded-2xl px-4 py-3 shadow-sm ${m.is_admin ? 'bg-blue-600 text-white' : 'bg-white'}">
                    <div class="text-xs mb-1 ${m.is_admin ? 'text-blue-50' : 'text-slate-500'}">
                        ${m.is_admin ? 'Admin' : (m.email || 'Khách')} · ${m.created_at}
                    </div>
                    <div class="text-sm leading-relaxed whitespace-pre-line"></div>
                </div>
            `;
            wrap.querySelector('div > div:last-child').textContent = m.content;
            list.appendChild(wrap);
            lastId = Math.max(lastId, parseInt(m.id, 10));
        });
        list.scrollTop = list.scrollHeight;
    };
    const poll = () => {
        fetch(`<?php echo base_url('admin.php/chats/poll/' . $thread['id']); ?>?last_id=${lastId}`, { cache: 'no-store' })
            .then(r => r.json())
            .then(data => { if (Array.isArray(data) && data.length) append(data); })
            .catch(() => {});
    };
    setInterval(poll, 2000);
    const textarea = document.getElementById('admin-chat-textarea');
    const form = textarea ? textarea.closest('form') : null;
    if (textarea && form) {
        textarea.addEventListener('keydown', function(e){
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.submit();
            }
        });
    }
})();
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
