<?php ob_start(); ?>
<div class="space-y-6">
    <section class="p-6 rounded-3xl bg-gradient-to-r from-blue-700 via-blue-600 to-blue-500 text-white shadow-md">
        <h1 class="text-2xl font-bold mb-2">Dịch vụ Nội Thất Store - Cửa hàng nội thất và thiết bị gia dụng</h1>
        <p class="text-blue-100">Lắp đặt, bảo trì, vận chuyển và tư vấn – tất cả trong một.</p>
    </section>

    <?php if (!empty($success)): ?>
        <div data-flash-message class="p-4 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 transition-opacity duration-500">Đặt lịch thành công! Chúng tôi sẽ liên hệ xác nhận.</div>
    <?php endif; ?>

    <section class="space-y-3">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h3 class="text-lg font-semibold text-white">Danh sách dịch vụ</h3>
                <span class="text-sm text-white/80"><?php echo count($services); ?> dịch vụ</span>
            </div>
            <form method="get" class="flex gap-2">
                <input class="px-3 py-2 border rounded text-sm" name="q" value="<?php echo htmlspecialchars($search ?? ''); ?>" placeholder="Tìm dịch vụ">
                <button class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Tìm</button>
            </form>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            <?php foreach ($services as $sv): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 flex flex-col gap-2">
                    <h3 class="text-lg font-semibold text-slate-800"><?php echo htmlspecialchars($sv['name']); ?></h3>
                    <p class="text-sm text-slate-600 leading-relaxed"><?php echo htmlspecialchars($sv['description']); ?></p>
                    <div class="text-xs text-slate-500">Thời gian: <?php echo htmlspecialchars($sv['sla']); ?></div>
                    <?php if (!empty($sv['price'])): ?>
                        <div class="text-sm font-semibold text-blue-700"><?php echo number_format($sv['price']); ?> đ</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if (empty($services)): ?>
                <div class="col-span-3 text-sm text-slate-500">Chưa có dịch vụ nào.</div>
            <?php endif; ?>
        </div>
    </section>

    <section class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 space-y-4">
        <div class="flex flex-col gap-2">
            <h3 class="text-lg font-semibold text-slate-800">Đặt lịch ngay</h3>
            <p class="text-sm text-slate-600">Chọn dịch vụ cần, điền thông tin và thời gian mong muốn.</p>
        </div>
        <form method="post" action="<?php echo base_url('services/book'); ?>" class="grid md:grid-cols-2 gap-3">
            <select name="service_id" required class="px-3 py-2 border rounded">
                <option value="">Chọn dịch vụ</option>
                <?php foreach ($services as $sv): ?>
                    <option value="<?php echo $sv['id']; ?>"><?php echo htmlspecialchars($sv['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <div class="grid md:grid-cols-2 gap-2">
                <div>
                    <label class="text-xs text-slate-500 block mb-1">Ngày thực hiện</label>
                    <input name="schedule_date" type="date" required class="px-3 py-2 border rounded w-full">
                </div>
                <div>
                    <label class="text-xs text-slate-500 block mb-1">Giờ</label>
                    <input name="schedule_time" type="time" required class="px-3 py-2 border rounded w-full">
                </div>
            </div>
            <input name="name" required class="px-3 py-2 border rounded" placeholder="Họ tên" maxlength="30">
            <input name="phone" required class="px-3 py-2 border rounded" placeholder="Số điện thoại" maxlength="10" pattern="0[0-9]{9}" title="Chỉ nhập số, bắt đầu 0 và đủ 10 số">
            <input name="email" class="px-3 py-2 border rounded" placeholder="Email (tùy chọn)" maxlength="30" pattern="[A-Za-z0-9._%+\-]+@(gmail|email)[A-Za-z0-9.\-]*\.[A-Za-z0-9.\-]+" title="Chỉ chữ không dấu/số, chứa @gmail hoặc @email, tối đa 30 ký tự">
            <input name="address" required class="px-3 py-2 border rounded col-span-2" placeholder="Địa chỉ thực hiện">
            <textarea name="note" class="px-3 py-2 border rounded col-span-2" rows="2" placeholder="Ghi chú thêm (tùy chọn)"></textarea>
            <div class="col-span-2 flex gap-3">
                <button class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Đặt lịch</button>
                <a href="tel:0123456789" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-800 hover:bg-slate-200 text-sm">Gọi hotline</a>
            </div>
        </form>
    </section>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
