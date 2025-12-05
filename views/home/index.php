<?php ob_start(); ?>
<?php $slides = array_map(function($p){ return asset_url($p); }, $sliderImages ?? []); ?>
<?php if (!empty($_SESSION['welcome_message'])): ?>
    <div data-home-notice class="mb-4 px-4 py-3 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-lg shadow-sm transition-opacity duration-500">
        <?php echo htmlspecialchars($_SESSION['welcome_message']); unset($_SESSION['welcome_message']); ?>
    </div>
<?php elseif (!empty($_SESSION['order_notice'])): ?>
    <div data-home-notice class="mb-4 px-4 py-3 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-lg shadow-sm transition-opacity duration-500">
        <?php echo htmlspecialchars($_SESSION['order_notice']); unset($_SESSION['order_notice']); ?>
    </div>
<?php endif; ?>
<div class="space-y-10">
    <section class="p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-blue-700 text-white rounded-3xl shadow-2xl">
        <div class="grid md:grid-cols-2 gap-6 items-center">
            <div class="space-y-3">
                <p class="text-blue-200 text-xs uppercase tracking-wider">Nội thất & thiết bị</p>
                <h1 class="text-3xl md:text-4xl font-bold leading-tight">Nâng tầm không gian sống hiện đại</h1>
                <p class="text-slate-200">Thiết kế tinh tế, chất liệu bền bỉ, giao nhanh và lắp đặt tận nơi.</p>
                <div class="flex gap-3 flex-wrap">
                    <a class="px-5 py-3 bg-blue-500 text-white rounded-full hover:bg-blue-600 shadow" href="<?php echo base_url('products'); ?>">Khám phá ngay</a>
                    <a class="px-5 py-3 bg-white/10 text-white rounded-full hover:bg-white/20" href="<?php echo base_url('cart'); ?>">Xem giỏ hàng</a>
                </div>
                <div class="flex gap-4 text-sm text-blue-100">
                    <div class="bg-white/10 rounded-lg px-3 py-2">+2000 sản phẩm</div>
                    <div class="bg-white/10 rounded-lg px-3 py-2">Bảo hành rõ ràng</div>
                    <div class="bg-white/10 rounded-lg px-3 py-2">Giao & lắp đặt nhanh</div>
                </div>
            </div>
            <div class="text-center">
                <div class="p-3 bg-white/10 rounded-3xl shadow-inner">
                    <img src="https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=800&q=80" class="w-full rounded-2xl object-cover" alt="Hero">
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($slides)): ?>
    <section class="glass-panel rounded-3xl shadow-xl p-5 space-y-4 overflow-hidden">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="section-title text-slate-500">Không gian thực tế</p>
                <h2 class="text-xl font-bold text-slate-900">Góc nhà khách hàng</h2>
            </div>
            <div class="flex gap-2">
                <button type="button" class="w-10 h-10 rounded-full border border-slate-200 text-slate-700 hover:bg-slate-100" data-slider-prev>‹</button>
                <button type="button" class="w-10 h-10 rounded-full border border-slate-200 text-slate-700 hover:bg-slate-100" data-slider-next>›</button>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl h-72 md:h-80">
            <?php foreach ($slides as $index => $img): ?>
                <div class="home-slide absolute inset-0 transition-opacity duration-[1200ms] ease-[cubic-bezier(0.4,0.0,0.2,1)] <?php echo $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0'; ?>">
                    <img src="<?php echo $img; ?>" class="w-full h-full object-cover" alt="Slider image <?php echo $index + 1; ?>">
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <p class="section-title text-slate-500">Danh mục</p>
                <h2 class="text-xl font-bold text-slate-900">Nổi bật nhất tuần</h2>
            </div>
            <a class="text-blue-700 text-sm font-semibold hover:underline" href="<?php echo base_url('products'); ?>">Xem tất cả</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
            <?php foreach ($categories as $c): ?>
                <a class="floating fade-border p-4 rounded-2xl text-center text-sm font-semibold text-slate-800 bg-white/90 hover:border-blue-200 hover:shadow-md transition" href="<?php echo base_url('products?category=' . $c['id']); ?>">
                    <span class="block text-xs text-blue-600 mb-1">Danh mục</span>
                    <?php echo $c['name']; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="space-y-4">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <div>
                <p class="section-title text-slate-500">Sản phẩm</p>
                <h2 class="text-xl font-bold text-slate-900">Được yêu thích</h2>
            </div>
            <a href="<?php echo base_url('products'); ?>" class="text-blue-700 hover:underline text-sm font-semibold">Xem tất cả</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php foreach ($featured as $p): ?>
                <?php $img = asset_url(!empty($p['image']) ? $p['image'] : 'public/assets/img/placeholder.svg'); ?>
                <?php $stock = (int)($p['stock'] ?? 0); ?>
                <div class="floating fade-border rounded-2xl shadow-sm transition p-3 flex flex-col bg-white/90 h-full">
                    <div class="overflow-hidden rounded-xl mb-3 relative h-44">
                        <a href="<?php echo base_url('product/' . $p['id']); ?>">
                            <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                        </a>
                        <span class="absolute top-2 left-2 px-3 py-1 rounded-full text-xs font-semibold <?php echo $stock > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'; ?>">
                            <?php echo $stock > 0 ? 'Còn hàng' : 'Hết hàng'; ?>
                        </span>
                    </div>
                    <div class="flex flex-col gap-1 flex-1 min-h-[80px]">
                        <h6 class="text-sm font-semibold line-clamp-2 text-slate-900"><?php echo $p['name']; ?></h6>
                        <p class="text-blue-700 font-bold text-base"><?php echo number_format($p['price']); ?> đ</p>
                        <p class="text-[12px] font-semibold <?php echo $stock > 0 ? 'text-emerald-600' : 'text-red-600'; ?> mb-2">
                            <?php echo $stock > 0 ? 'Còn ' . $stock . ' sản phẩm' : 'Hàng đang về'; ?>
                        </p>
                    </div>
                    <div class="mt-auto grid grid-cols-2 gap-2 items-stretch pt-1">
                        <a class="w-full text-center h-12 flex items-center justify-center text-sm border rounded-lg border-slate-200 hover:border-blue-500" href="<?php echo base_url('product/' . $p['id']); ?>">Xem</a>
                        <?php if ($stock <= 0): ?>
                            <a class="w-full h-12 inline-flex items-center justify-center text-sm bg-amber-500 text-white rounded-lg hover:bg-amber-600" href="tel:0974734668">Liên hệ</a>
                        <?php else: ?>
                            <form method="post" action="<?php echo base_url('cart/add'); ?>" class="w-full flex">
                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/'); ?>">
                                <button type="submit" class="w-full h-12 text-center text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center justify-center">Thêm giỏ</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="glass-panel rounded-3xl shadow-lg p-6 flex flex-col md:flex-row gap-6 items-center">
        <div class="flex-1 space-y-2">
            <p class="section-title text-slate-500">Dịch vụ</p>
            <h3 class="text-2xl font-bold text-slate-900">Lắp đặt - bảo trì - chăm sóc tận tâm</h3>
            <p class="text-slate-600">Đặt lịch kỹ thuật nhanh, tư vấn sắp đặt phù hợp không gian, bảo trì trọn gói để sản phẩm luôn như mới.</p>
        </div>
        <div class="flex gap-3">
            <a href="<?php echo base_url('services'); ?>" class="px-5 py-3 rounded-full bg-blue-600 text-white font-semibold hover:bg-blue-700">Đặt lịch ngay</a>
            <a href="tel:0974734668" class="px-5 py-3 rounded-full border border-slate-200 text-slate-800 hover:border-blue-400">Gọi hotline</a>
        </div>
    </section>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const homeNotice = document.querySelector('[data-home-notice]');
    if (homeNotice) {
        setTimeout(() => {
            homeNotice.classList.add('opacity-0');
            setTimeout(() => homeNotice.remove(), 600);
        }, 2000);
    }
    const slides = Array.from(document.querySelectorAll('.home-slide'));
    if (!slides.length) return;
    let index = 0;
    const total = slides.length;
    const show = (nextIndex) => {
        slides[index].classList.remove('opacity-100','z-10');
        slides[index].classList.add('opacity-0','z-0');
        index = (nextIndex + total) % total;
        slides[index].classList.remove('opacity-0','z-0');
        slides[index].classList.add('opacity-100','z-10');
    };
    if (total > 1) {
        document.querySelectorAll('[data-slider-next]').forEach(btn => btn.addEventListener('click', () => { show(index + 1); restart(); }));
        document.querySelectorAll('[data-slider-prev]').forEach(btn => btn.addEventListener('click', () => { show(index - 1); restart(); }));
    } else {
        document.querySelectorAll('[data-slider-next],[data-slider-prev]').forEach(btn => btn.classList.add('opacity-50','pointer-events-none'));
    }
    let autoTimer = null;
    const restart = () => {
        if (autoTimer) clearInterval(autoTimer);
        if (total > 1) {
            autoTimer = setInterval(() => show(index + 1), 6000);
        }
    };
    restart();
});
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
