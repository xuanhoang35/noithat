<?php ob_start(); ?>
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="h-[520px] flex flex-col">
        <div class="flex-1 overflow-y-auto bg-slate-50 px-4 py-3 space-y-3" id="chat-list" data-thread="<?php echo (int)$thread['id']; ?>">
            <?php foreach ($messages as $m): ?>
                <?php $isAdmin = (bool)$m['is_admin']; ?>
                <div class="flex <?php echo $isAdmin ? 'justify-start' : 'justify-end'; ?>" data-msg-id="<?php echo $m['id']; ?>">
                    <div class="max-w-[70%] rounded-2xl px-4 py-3 shadow-sm <?php echo $isAdmin ? 'bg-white' : 'bg-blue-600 text-white'; ?>">
                        <div class="text-xs mb-1 <?php echo $isAdmin ? 'text-slate-500' : 'text-blue-50'; ?>">
                            <?php echo $isAdmin ? 'Admin' : 'Bạn'; ?> · <?php echo date('d/m H:i', strtotime($m['created_at'])); ?>
                        </div>
                        <div class="text-sm leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($m['content']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($messages)): ?>
                <div class="text-center text-slate-500 text-sm py-10" data-empty>Chưa có tin nhắn nào, hãy đặt câu hỏi cho chúng tôi.</div>
            <?php endif; ?>
        </div>
        <div class="border-t bg-white px-4 py-3">
            <form method="post" action="<?php echo base_url('chat'); ?>" class="flex gap-3 items-end" id="chat-form">
                <div class="flex-1">
                    <label class="text-sm text-slate-600">Nội dung</label>
                    <textarea name="content" class="w-full mt-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-200" rows="3" placeholder="Nhập câu hỏi hoặc yêu cầu tư vấn..." <?php echo ($thread['status'] ?? '') === 'closed' ? 'disabled' : 'required'; ?>></textarea>
                </div>
                <div class="flex flex-col gap-2 w-32">
                    <button name="action" value="end" formnovalidate class="h-[44px] rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 text-sm">Kết thúc</button>
                    <button name="action" value="send" class="h-[44px] rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-medium" <?php echo ($thread['status'] ?? '') === 'closed' ? 'disabled' : ''; ?>>Gửi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
(function(){
    const list = document.getElementById('chat-list');
    if (!list) return;
    const form = document.getElementById('chat-form');
    const textarea = form ? form.querySelector('textarea[name="content"]') : null;
    if (textarea && form) {
        textarea.addEventListener('keydown', function(e){
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.submit();
            }
        });
    }
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
            wrap.className = 'flex ' + (m.is_admin ? 'justify-start' : 'justify-end');
            wrap.setAttribute('data-msg-id', m.id);
            wrap.innerHTML = `
                <div class="max-w-[70%] rounded-2xl px-4 py-3 shadow-sm ${m.is_admin ? 'bg-white' : 'bg-blue-600 text-white'}">
                    <div class="text-xs mb-1 ${m.is_admin ? 'text-slate-500' : 'text-blue-50'}">
                        ${m.is_admin ? 'Admin' : 'Bạn'} · ${m.created_at}
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
        fetch(`<?php echo base_url('chat/poll'); ?>?thread_id=${threadId}&last_id=${lastId}`, { cache: 'no-store' })
            .then(r => r.json())
            .then(data => { if (Array.isArray(data) && data.length) append(data); })
            .catch(() => {});
    };
    setInterval(poll, 2000);
})();
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
