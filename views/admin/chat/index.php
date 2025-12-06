<?php ob_start(); ?>
<div class="bg-white rounded-2xl shadow-sm p-5">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-semibold">Tư vấn khách hàng</h1>
            <p class="text-slate-500 text-sm">Danh sách phiên chat giữa khách và admin</p>
            <p class="text-slate-500 text-sm mt-1"><?php echo (int)($openCount ?? 0); ?> phiên chat đang cần tư vấn</p>
        </div>
    </div>
    <div class="overflow-x-auto max-h-[520px]" id="chat-thread-list">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-slate-100 text-left">
                    <th class="p-3">#</th>
                    <th class="p-3">Khách</th>
                    <th class="p-3">Trạng thái</th>
                    <th class="p-3">Cập nhật</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($threads as $t): ?>
                    <tr class="border-b hover:bg-slate-50">
                        <td class="p-3"><?php echo $t['id']; ?></td>
                        <td class="p-3"><?php echo htmlspecialchars($t['email']); ?></td>
                        <td class="p-3">
                            <span class="px-3 py-1 rounded-full text-xs <?php echo $t['status'] === 'open' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700'; ?>">
                                <?php echo $t['status'] === 'open' ? 'Đang mở' : 'Đã đóng'; ?>
                            </span>
                        </td>
                        <td class="p-3"><?php echo date('d/m/Y H:i', strtotime($t['updated_at'])); ?></td>
                        <td class="p-3 text-right">
                            <a class="px-3 py-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100" href="<?php echo base_url('admin.php/chats/show/' . $t['id']); ?>">Xem</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($threads)): ?>
                    <tr><td colspan="5" class="p-4 text-center text-slate-500">Chưa có phiên chat nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
(function(){
    const badge = document.querySelector('[data-badge="chats"]');
    const pollThreads = () => {
        fetch('<?php echo base_url('admin.php/chats'); ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
            .catch(() => {});
    };
    // Polling badge đã có ở layout; optional: refresh list khi có unread
    const refreshOnUnread = () => {
        if (badge && badge.style.display !== 'none') {
            // để đơn giản: reload trang khi badge chats >0 (giữ admin cập nhật danh sách)
            location.reload();
        }
    };
    setInterval(refreshOnUnread, 3000);
})();
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
