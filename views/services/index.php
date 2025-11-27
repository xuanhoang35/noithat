<?php ob_start(); ?>
<div class="space-y-8">
    <section class="hero-grid rounded-3xl p-7 text-white shadow-2xl spotlight">
        <div class="relative grid md:grid-cols-[1.1fr,0.9fr] gap-6 items-center z-10">
            <div class="space-y-3">
                <p class="section-title text-blue-100">Dịch vụ Noithat Store</p>
                <h1 class="text-3xl font-bold leading-tight">Lắp đặt, bảo trì, vận chuyển và tư vấn trọn gói</h1>
                <p class="text-blue-100 text-sm md:text-base">Đội ngũ kỹ thuật có mặt nhanh, bảo hành minh bạch, hỗ trợ 24/7 để không gian luôn hoàn hảo.</p>
                <div class="flex gap-3 flex-wrap">
                    <a href="#service-booking" class="px-5 py-3 rounded-full bg-white text-slate-900 font-semibold hover:bg-blue-100 shadow-lg">Đặt lịch ngay</a>
                    <a href="tel:0974734668" class="px-5 py-3 rounded-full btn-soft text-white/90 border border-white/25 hover:bg-white/15">Gọi tư vấn</a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="p-4 rounded-2xl bg-white/10 border border-white/10">
                    <div class="text-2xl font-bold">24-48h</div>
                    <div class="text-blue-100 text-sm">Có mặt lắp đặt</div>
                </div>
                <div class="p-4 rounded-2xl bg-white/10 border border-white/10">
                    <div class="text-2xl font-bold">24/7</div>
                    <div class="text-blue-100 text-sm">Hỗ trợ sự cố</div>
                </div>
                <div class="p-4 rounded-2xl bg-white/10 border border-white/10">
                    <div class="text-2xl font-bold">+5000</div>
                    <div class="text-blue-100 text-sm">Lịch thành công</div>
                </div>
                <div class="p-4 rounded-2xl bg-white/10 border border-white/10">
                    <div class="text-2xl font-bold">4.8/5</div>
                    <div class="text-blue-100 text-sm">Khách hài lòng</div>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($success)): ?>
        <div data-flash-message class="p-4 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 transition-opacity duration-500">Đặt lịch thành công! Chúng tôi sẽ liên hệ xác nhận.</div>
    <?php endif; ?>

    <section class="space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <p class="section-title text-slate-500">Danh sách dịch vụ</p>
                <h3 class="text-xl font-bold text-slate-900">Chọn gói phù hợp</h3>
            </div>
            <span class="text-sm text-slate-500"><?php echo count($services); ?> dịch vụ</span>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            <?php foreach ($services as $sv): ?>
                <div class="floating fade-border p-4 rounded-2xl bg-white/90 shadow-sm flex flex-col gap-2">
                    <h3 class="text-lg font-semibold text-slate-900"><?php echo htmlspecialchars($sv['name']); ?></h3>
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

    <section id="service-booking" class="glass-panel rounded-3xl shadow-xl p-5 space-y-4">
        <div class="flex flex-col gap-2">
            <p class="section-title text-slate-500">Đặt lịch</p>
            <h3 class="text-xl font-bold text-slate-900">Chọn dịch vụ & thời gian mong muốn</h3>
            <p class="text-sm text-slate-600">Nhập thông tin, chúng tôi sẽ gọi xác nhận trong vòng 15 phút.</p>
        </div>
        <form method="post" action="<?php echo base_url('services/book'); ?>" class="grid md:grid-cols-2 gap-3">
            <select name="service_id" required class="px-3 py-3 border rounded-xl">
                <option value="">Chọn dịch vụ</option>
                <?php foreach ($services as $sv): ?>
                    <option value="<?php echo $sv['id']; ?>"><?php echo htmlspecialchars($sv['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <div class="grid md:grid-cols-2 gap-2">
                <div>
                    <label class="text-xs text-slate-500 block mb-1">Ngày thực hiện</label>
                    <input name="schedule_date" type="date" required class="px-3 py-3 border rounded-xl w-full">
                </div>
                <div>
                    <label class="text-xs text-slate-500 block mb-1">Giờ</label>
                    <input name="schedule_time" type="time" required class="px-3 py-3 border rounded-xl w-full">
                </div>
            </div>
            <input name="name" required class="px-3 py-3 border rounded-xl" placeholder="Họ tên" maxlength="30">
            <input name="phone" required class="px-3 py-3 border rounded-xl" placeholder="Số điện thoại" maxlength="10" pattern="0[0-9]{9}" title="Chỉ nhập số, bắt đầu 0 và đủ 10 số">
            <input name="email" class="px-3 py-3 border rounded-xl" placeholder="Email (tùy chọn)" maxlength="30" pattern="[A-Za-z0-9._%+\-]+@(gmail|email)[A-Za-z0-9.\-]*\.[A-Za-z0-9.\-]+" title="Chỉ chữ không dấu/số, chứa @gmail hoặc @email, tối đa 30 ký tự">
            <input name="address" required class="px-3 py-3 border rounded-xl col-span-2" placeholder="Địa chỉ thực hiện">
            <textarea name="note" class="px-3 py-3 border rounded-xl col-span-2" rows="2" placeholder="Ghi chú thêm (tùy chọn)"></textarea>
            <div class="col-span-2 flex gap-3">
                <button class="px-5 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow font-semibold">Đặt lịch</button>
                <a href="tel:0123456789" class="px-5 py-3 rounded-xl border border-slate-200 text-slate-800 hover:border-blue-400 text-sm">Gọi hotline</a>
            </div>
        </form>
    </section>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
