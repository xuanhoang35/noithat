<?php ob_start(); ?>
<section class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
    <div class="about-hero__overlay p-8 grid md:grid-cols-2 gap-4 items-center" style="background: linear-gradient(120deg, rgba(255,255,255,0.74), rgba(255,255,255,0.68)), url('https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1400&q=90') center/cover no-repeat;">
        <div>
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">Về chúng tôi</span>
            <h1 class="text-3xl font-bold mt-3 mb-3 text-slate-800">Không gian sống hiện đại, tinh tế và tiện nghi</h1>
            <p class="text-slate-600">Noithat Store mang đến giải pháp nội thất & thiết bị gia dụng đồng bộ, giao nhanh, bảo hành rõ ràng.</p>
        </div>
        <div class="text-right">
            <div class="text-2xl font-bold text-blue-600">++ 2.000+</div>
            <div class="text-sm text-slate-600">Sản phẩm được tin dùng</div>
            <div class="text-2xl font-bold text-blue-600 mt-2">24/7</div>
            <div class="text-sm text-slate-600">Hỗ trợ khách hàng</div>
        </div>
    </div>
</section>

<div class="grid md:grid-cols-3 gap-3 mb-4">
    <div class="p-4 bg-white rounded-xl shadow-sm">
        <h6 class="text-blue-600 font-semibold">Tầm nhìn</h6>
        <p class="text-slate-600 text-sm mb-0">Trở thành lựa chọn hàng đầu cho mọi nhu cầu nội thất & thiết bị gia dụng trong gia đình Việt.</p>
    </div>
    <div class="p-4 bg-white rounded-xl shadow-sm">
        <h6 class="text-blue-600 font-semibold">Sứ mệnh</h6>
        <p class="text-slate-600 text-sm mb-0">Mang đến sản phẩm chất lượng, thiết kế tinh tế, dịch vụ tư vấn & giao hàng nhanh.</p>
    </div>
    <div class="p-4 bg-white rounded-xl shadow-sm">
        <h6 class="text-blue-600 font-semibold">Giá trị</h6>
        <p class="text-slate-600 text-sm mb-0">Uy tín – Tận tâm – Minh bạch giá – Bảo hành đảm bảo.</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-4 mb-4">
    <h2 class="text-lg font-semibold mb-3">Sản phẩm & dịch vụ</h2>
    <div class="grid md:grid-cols-3 gap-3 text-sm text-slate-600">
        <div class="p-3 bg-slate-50 rounded">Nội thất: sofa, bàn ghế, tủ, giường cho phòng khách, phòng ngủ, phòng ăn.</div>
        <div class="p-3 bg-slate-50 rounded">Thiết bị gia dụng: tủ lạnh, máy giặt, bếp, lò vi sóng, thiết bị bếp thông minh.</div>
        <div class="p-3 bg-slate-50 rounded">Dịch vụ: tư vấn bố trí, giao hàng & lắp đặt nhanh, hỗ trợ sau bán hàng.</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-4 mb-4">
    <h2 class="text-lg font-semibold mb-3">Cam kết</h2>
    <ul class="list-disc pl-5 text-slate-600 text-sm space-y-1">
        <li>Sản phẩm chính hãng, nguồn gốc rõ ràng</li>
        <li>Chính sách đổi trả, bảo hành minh bạch</li>
        <li>Hỗ trợ khách hàng 24/7, xử lý khiếu nại nhanh</li>
    </ul>
</div>

<div class="bg-white rounded-xl shadow-sm p-4">
    <h2 class="text-lg font-semibold mb-3">Liên hệ</h2>
    <div class="grid md:grid-cols-3 gap-2 text-slate-600 text-sm">
        <div><strong>Hotline:</strong> 0974.734.668</div>
        <div><strong>Email:</strong> huyendothi.79@gmail.com</div>
        <div><strong>Địa chỉ:</strong> Phương Canh, Nam Từ Liêm, Hà Nội</div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
