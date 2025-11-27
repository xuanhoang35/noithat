<?php ob_start(); ?>
<div class="space-y-8">
    <section class="hero-grid rounded-3xl p-8 md:p-10 text-white shadow-2xl spotlight">
        <div class="relative grid md:grid-cols-[1.1fr,0.9fr] gap-6 items-center z-10">
            <div class="space-y-3">
                <p class="section-title text-blue-100">Về chúng tôi</p>
                <h1 class="text-3xl font-bold leading-tight">Không gian sống hiện đại, tinh tế và tiện nghi</h1>
                <p class="text-blue-100 text-sm md:text-base">Noithat Store mang đến giải pháp nội thất & thiết bị gia dụng đồng bộ, giao nhanh, bảo hành rõ ràng.</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="p-4 rounded-2xl bg-white/10 border border-white/10">
                    <div class="text-2xl font-bold">2.000+</div>
                    <div class="text-blue-100 text-sm">Sản phẩm tin dùng</div>
                </div>
                <div class="p-4 rounded-2xl bg-white/10 border border-white/10">
                    <div class="text-2xl font-bold">24/7</div>
                    <div class="text-blue-100 text-sm">Hỗ trợ khách hàng</div>
                </div>
                <div class="p-4 rounded-2xl bg-white/10 border border-white/10">
                    <div class="text-2xl font-bold">15+</div>
                    <div class="text-blue-100 text-sm">Năm kinh nghiệm</div>
                </div>
                <div class="p-4 rounded-2xl bg-white/10 border border-white/10">
                    <div class="text-2xl font-bold">98%</div>
                    <div class="text-blue-100 text-sm">Khách hài lòng</div>
                </div>
            </div>
        </div>
    </section>

    <div class="grid md:grid-cols-3 gap-3">
        <div class="floating fade-border p-4 rounded-2xl bg-white/90">
            <h6 class="text-blue-600 font-semibold">Tầm nhìn</h6>
            <p class="text-slate-600 text-sm mb-0">Trở thành lựa chọn hàng đầu cho mọi nhu cầu nội thất & thiết bị gia dụng trong gia đình Việt.</p>
        </div>
        <div class="floating fade-border p-4 rounded-2xl bg-white/90">
            <h6 class="text-blue-600 font-semibold">Sứ mệnh</h6>
            <p class="text-slate-600 text-sm mb-0">Mang đến sản phẩm chất lượng, thiết kế tinh tế, dịch vụ tư vấn & giao hàng nhanh.</p>
        </div>
        <div class="floating fade-border p-4 rounded-2xl bg-white/90">
            <h6 class="text-blue-600 font-semibold">Giá trị</h6>
            <p class="text-slate-600 text-sm mb-0">Uy tín – Tận tâm – Minh bạch giá – Bảo hành đảm bảo.</p>
        </div>
    </div>

    <div class="glass-panel rounded-3xl shadow-lg p-5 space-y-3">
        <h2 class="text-lg font-bold">Sản phẩm & dịch vụ</h2>
        <div class="grid md:grid-cols-3 gap-3 text-sm text-slate-600">
            <div class="p-3 rounded-2xl bg-white/70 border border-slate-100">Nội thất: sofa, bàn ghế, tủ, giường cho phòng khách, phòng ngủ, phòng ăn.</div>
            <div class="p-3 rounded-2xl bg-white/70 border border-slate-100">Thiết bị gia dụng: tủ lạnh, máy giặt, bếp, lò vi sóng, thiết bị bếp thông minh.</div>
            <div class="p-3 rounded-2xl bg-white/70 border border-slate-100">Dịch vụ: tư vấn bố trí, giao hàng & lắp đặt nhanh, hỗ trợ sau bán hàng.</div>
        </div>
    </div>

    <div class="glass-panel rounded-3xl shadow-lg p-5 space-y-3">
        <h2 class="text-lg font-bold">Cam kết</h2>
        <ul class="list-disc pl-5 text-slate-600 text-sm space-y-2">
            <li>Sản phẩm chính hãng, nguồn gốc rõ ràng</li>
            <li>Chính sách đổi trả, bảo hành minh bạch</li>
            <li>Hỗ trợ khách hàng 24/7, xử lý khiếu nại nhanh</li>
        </ul>
    </div>

    <div class="glass-panel rounded-3xl shadow-lg p-5 space-y-3">
        <h2 class="text-lg font-bold">Liên hệ</h2>
        <div class="grid md:grid-cols-3 gap-2 text-slate-600 text-sm">
            <div><strong>Hotline:</strong> 0974.734.668</div>
            <div><strong>Email:</strong> huyendothi.79@gmail.com</div>
            <div><strong>Địa chỉ:</strong> Phương Canh, Nam Từ Liêm, Hà Nội</div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
