<?php ob_start(); ?>
<?php $complaintStatusLabels = [
    'new' => 'Mới tiếp nhận',
    'in_progress' => 'Đang xử lý',
    'resolved' => 'Đã đóng'
]; ?>
<h1 class="text-xl font-semibold mb-3">Khiếu nại từ khách hàng</h1>
<div class="space-y-3">
    <?php foreach ($complaints as $c): ?>
    <?php $replies = (new \Complaint())->replies($c['id']); ?>
    <div class="bg-white rounded-2xl shadow-sm p-4">
        <div class="flex flex-col md:flex-row md:justify-between gap-3">
            <div>
                <div class="font-semibold flex items-center gap-2">[#<?php echo $c['id']; ?>] <?php echo htmlspecialchars($c['title']); ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                        <?php echo $c['status']==='resolved' ? 'bg-emerald-50 text-emerald-700' : ($c['status']==='in_progress' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700'); ?>">
                        <?php echo $complaintStatusLabels[$c['status']] ?? $c['status']; ?>
                    </span>
                </div>
                <div class="text-sm text-slate-500">Đơn: <?php echo $c['code']; ?> · User: <?php echo $c['email']; ?> · <?php echo $c['created_at']; ?></div>
                <div class="mt-2 text-slate-700">Nội dung: <?php echo nl2br(htmlspecialchars($c['content'])); ?></div>
                <?php if (!empty($replies)): ?>
                    <div class="mt-2 space-y-1">
                        <?php foreach ($replies as $r): ?>
                            <div class="p-2 bg-slate-50 rounded">
                                <div class="text-xs text-slate-500"><?php echo $r['created_at']; ?> · <?php echo $r['is_admin'] ? 'Admin' : 'User'; ?></div>
                                <div><?php echo nl2br(htmlspecialchars($r['content'])); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="min-w-[260px]">
                <form method="post" action="<?php echo base_url('admin.php/complaints/reply/' . $c['id']); ?>" class="grid gap-2">
                    <select class="px-3 py-2 border rounded text-sm" name="status">
                        <?php foreach (['new','in_progress','resolved'] as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo $c['status']===$s?'selected':''; ?>><?php echo $complaintStatusLabels[$s]; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($c['status'] !== 'resolved'): ?>
                        <textarea class="px-3 py-2 border rounded text-sm" name="response" rows="2" placeholder="Phản hồi khách hàng"></textarea>
                        <button class="px-3 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Gửi phản hồi</button>
                    <?php else: ?>
                        <div class="text-green-600 text-sm">Khiếu nại đã đóng.</div>
                        <button class="px-3 py-2 text-sm bg-blue-400 text-white rounded" disabled>Đã đóng</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
