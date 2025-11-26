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
<div class="space-y-8">
    <section class="p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-blue-700 text-white rounded-3xl shadow-md">
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
    <section class="bg-white rounded-3xl shadow-sm p-4 space-y-3 overflow-hidden">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-800">Không gian thực tế</h2>
            <div class="flex gap-2">
                <button type="button" class="w-9 h-9 rounded-full border border-slate-200 text-slate-600 hover:bg-slate-100" data-slider-prev>‹</button>
                <button type="button" class="w-9 h-9 rounded-full border border-slate-200 text-slate-600 hover:bg-slate-100" data-slider-next>›</button>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl h-72">
            <?php foreach ($slides as $index => $img): ?>
                <div class="home-slide absolute inset-0 transition-opacity duration-[1200ms] ease-[cubic-bezier(0.4,0.0,0.2,1)] <?php echo $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0'; ?>">
                    <img src="<?php echo $img; ?>" class="w-full h-full object-cover" alt="Slider image <?php echo $index + 1; ?>">
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Danh mục nổi bật</h2>
            <a class="text-blue-600 text-sm hover:underline" href="<?php echo base_url('products'); ?>">Xem tất cả</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
            <?php foreach ($categories as $c): ?>
                <a class="p-4 rounded-xl bg-white shadow-sm hover:shadow-md text-center text-sm font-semibold text-slate-700 border border-slate-100" href="<?php echo base_url('products?category=' . $c['id']); ?>">
                    <?php echo $c['name']; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="space-y-3">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold mb-0">Sản phẩm nổi bật</h2>
            <a href="<?php echo base_url('products'); ?>" class="text-blue-600 hover:underline text-sm">Xem tất cả</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php foreach ($featured as $p): ?>
                <?php $img = asset_url(!empty($p['image']) ? $p['image'] : 'public/assets/img/placeholder.svg'); ?>
                <?php $stock = (int)($p['stock'] ?? 0); ?>
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition p-3 flex flex-col border border-slate-100">
                    <div class="overflow-hidden rounded-xl mb-3">
                        <a href="<?php echo base_url('product/' . $p['id']); ?>">
                            <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="w-full h-40 object-cover hover:scale-105 transition">
                        </a>
                    </div>
                    <h6 class="text-sm font-semibold line-clamp-2"><?php echo $p['name']; ?></h6>
                    <p class="text-blue-600 font-bold mb-2"><?php echo number_format($p['price']); ?> đ</p>
                    <p class="text-xs font-semibold <?php echo $stock > 0 ? 'text-emerald-600' : 'text-red-600'; ?> mb-2">
                        <?php echo $stock > 0 ? 'Còn ' . $stock . ' SP' : 'Hàng đang về'; ?>
                    </p>
                    <div class="mt-auto grid grid-cols-2 gap-2">
                        <a class="w-full text-center h-11 flex items-center justify-center text-sm border rounded-lg border-slate-200 hover:border-blue-500" href="<?php echo base_url('product/' . $p['id']); ?>">Xem</a>
                        <?php if ($stock <= 0): ?>
                            <a class="w-full h-11 inline-flex items-center justify-center text-sm bg-amber-500 text-white rounded-lg hover:bg-amber-600" href="tel:0974734668">Liên hệ</a>
                        <?php else: ?>
                            <form method="post" action="<?php echo base_url('cart/add'); ?>" class="w-full">
                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/'); ?>">
                                <button type="submit" class="w-full h-11 text-center text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">Thêm giỏ</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
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
