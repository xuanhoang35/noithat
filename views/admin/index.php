<?php ob_start(); ?>
<div class="grid md:grid-cols-4 gap-4 mb-6 mt-4">
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-xs uppercase text-slate-500">Sản phẩm</p>
        <div class="text-2xl font-bold text-slate-800 mt-1">Quản lý kho</div>
        <p class="text-sm text-slate-500 mt-1">Thêm/sửa/xóa sản phẩm</p>
        <a class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline mt-3" href="<?php echo base_url('admin.php/products'); ?>">Đi tới sản phẩm →</a>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-xs uppercase text-slate-500">Đơn hàng</p>
        <div class="text-2xl font-bold text-slate-800 mt-1">Xử lý & giao</div>
        <p class="text-sm text-slate-500 mt-1">Cập nhật trạng thái, theo dõi tiến độ</p>
        <a class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline mt-3" href="<?php echo base_url('admin.php/orders'); ?>">Đi tới đơn hàng →</a>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-xs uppercase text-slate-500">Dịch vụ</p>
        <div class="text-2xl font-bold text-slate-800 mt-1">Lịch hẹn</div>
        <p class="text-sm text-slate-500 mt-1">Quản lý dịch vụ & lịch đặt</p>
        <a class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline mt-3" href="<?php echo base_url('admin.php/services'); ?>">Đi tới dịch vụ →</a>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-xs uppercase text-slate-500">Khách hàng</p>
        <div class="text-2xl font-bold text-slate-800 mt-1">Tài khoản</div>
        <p class="text-sm text-slate-500 mt-1">Kích hoạt/khóa nhanh</p>
        <a class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline mt-3" href="<?php echo base_url('admin.php/users'); ?>">Đi tới khách hàng →</a>
    </div>
</div>

<div class="grid md:grid-cols-2 gap-4 mt-4">
    <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold text-slate-800">Kênh hỗ trợ</h2>
            <a class="text-sm text-blue-600 hover:underline" href="<?php echo base_url('admin.php/chats'); ?>">Xem chat</a>
        </div>
        <ul class="space-y-2 text-sm text-slate-600">
            <li>• Tư vấn khách hàng: xem và trả lời tin nhắn</li>
            <li>• Khiếu nại từ khách hàng: theo dõi và phản hồi </li>
            <li>• Feedback: ghi nhận ý kiến sau mua</li>
        </ul>
        <div class="flex gap-2 mt-4">
            <a href="<?php echo base_url('admin.php/chats'); ?>" class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700">Tư vấn khách</a>
            <a href="<?php echo base_url('admin.php/complaints'); ?>" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-800 text-sm hover:bg-slate-200">Khiếu nại từ khách</a>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
        <h2 class="text-lg font-semibold text-slate-800 mb-3">Shortcut nhanh</h2>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <a class="p-3 rounded-lg border border-slate-200 hover:border-blue-400 hover:bg-blue-50" href="<?php echo base_url('admin.php/products'); ?>">+ Thêm sản phẩm</a>
            <a class="p-3 rounded-lg border border-slate-200 hover:border-blue-400 hover:bg-blue-50" href="<?php echo base_url('admin.php/categories'); ?>">+ Thêm danh mục</a>
            <a class="p-3 rounded-lg border border-slate-200 hover:border-blue-400 hover:bg-blue-50" href="<?php echo base_url('admin.php/orders'); ?>">Cập nhật đơn</a>
            <a class="p-3 rounded-lg border border-slate-200 hover:border-blue-400 hover:bg-blue-50" href="<?php echo base_url('admin.php/services'); ?>">Thêm dịch vụ</a>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/layouts/main.php'; ?>
