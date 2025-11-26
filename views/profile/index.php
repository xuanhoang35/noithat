<?php ob_start(); ?>
<?php $complaintStatusLabels = [
    'new' => 'Mới tiếp nhận',
    'in_progress' => 'Đang xử lý',
    'resolved' => 'Đã đóng'
]; ?>
<?php
$defaultAvatar = 'public/Profile/user-iconprofile.png';
$avatarPath = !empty($user['avatar']) ? $user['avatar'] : $defaultAvatar;
$avatar = asset_url($avatarPath);
$defaultAvatarUrl = asset_url($defaultAvatar);
?>
<div class="grid lg:grid-cols-2 gap-4">
    <div class="bg-white rounded-2xl shadow-sm p-5">
        <h1 class="text-xl font-semibold mb-3">Thông tin cá nhân</h1>
        <form method="post" action="<?php echo base_url('profile'); ?>" enctype="multipart/form-data" class="grid gap-3">
            <label for="avatar-input" class="flex flex-col items-center gap-3 mb-4 cursor-pointer text-center">
                <img src="<?php echo $avatar; ?>" alt="Avatar" data-profile-avatar class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg" onerror="this.src='<?php echo $defaultAvatarUrl; ?>';">
                <div>
                    <p class="font-semibold text-lg"><?php echo htmlspecialchars($user['name'] ?? ''); ?></p>
                    <p class="text-sm text-slate-500"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                </div>
            </label>
            <input type="file" name="avatar" id="avatar-input" accept="image/*" class="hidden">
            <div>
                <label class="text-sm text-slate-600">Họ tên</label>
                <input class="px-3 py-2 border rounded w-full" name="name" value="<?php echo $user['name'] ?? ''; ?>" required>
            </div>
            <div>
                <label class="text-sm text-slate-600">Email (không đổi)</label>
                <input class="px-3 py-2 border rounded w-full bg-slate-100" value="<?php echo $user['email'] ?? ''; ?>" disabled>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="text-sm text-slate-600">SĐT</label>
                    <input class="px-3 py-2 border rounded w-full" name="phone" value="<?php echo $user['phone'] ?? ''; ?>">
                </div>
                <div>
                    <label class="text-sm text-slate-600">Địa chỉ mặc định</label>
                    <input class="px-3 py-2 border rounded w-full" name="address" value="<?php echo $user['address'] ?? ''; ?>" placeholder="Địa chỉ giao hàng">
                </div>
            </div>
            <button class="mt-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 w-full">Cập nhật</button>
        </form>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-5">
        <h2 class="text-lg font-semibold mb-3">Khiếu nại của bạn</h2>
        <?php if (empty($complaints)): ?>
            <p class="text-slate-500">Chưa có khiếu nại nào.</p>
        <?php else: ?>
            <div class="space-y-3 max-h-[600px] overflow-auto">
            <?php foreach ($complaints as $c): ?>
                <div class="p-3 border rounded">
                    <div class="font-semibold"><?php echo htmlspecialchars($c['title']); ?></div>
                    <div class="text-sm text-slate-500">Đơn: <?php echo $c['code']; ?> · Trạng thái: <?php echo $complaintStatusLabels[$c['status']] ?? $c['status']; ?></div>
                    <div class="mt-2 text-slate-600">Nội dung: <?php echo nl2br(htmlspecialchars($c['content'])); ?></div>
                    <?php $replies = (new Complaint())->replies($c['id']); ?>
                    <?php if (!empty($replies)): ?>
                        <div class="mt-2 space-y-1">
                            <?php foreach ($replies as $r): ?>
                                <div class="p-2 bg-slate-50 rounded">
                                    <div class="text-xs text-slate-500"><?php echo $r['created_at']; ?> · <?php echo $r['is_admin'] ? 'Admin' : 'Bạn'; ?></div>
                                    <div><?php echo nl2br(htmlspecialchars($r['content'])); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($c['status'] !== 'resolved'): ?>
                        <form method="post" action="<?php echo base_url('complaints/reply/' . $c['id']); ?>" class="mt-2 grid gap-2">
                            <textarea class="px-3 py-2 border rounded" name="content" rows="2" placeholder="Phản hồi thêm"></textarea>
                            <button class="px-3 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 w-fit">Gửi phản hồi</button>
                        </form>
                    <?php else: ?>
                        <div class="mt-2 text-green-600 text-sm">Khiếu nại đã đóng.</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const input = document.getElementById('avatar-input');
    const preview = document.querySelector('[data-profile-avatar]');
    if (!input || !preview) return;
    input.addEventListener('change', function(){
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e){ preview.src = e.target.result; };
            reader.readAsDataURL(input.files[0]);
        }
    });
});
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
